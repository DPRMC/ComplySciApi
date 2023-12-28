<?php

namespace DPRMC\ComplySciApi;

use Carbon\Carbon;
use DPRMC\ComplySciApi\Exceptions\InvalidInsertException;
use DPRMC\ComplySciApi\Exceptions\NotAuthenticatedException;
use DPRMC\ComplySciApi\Objects\DebugTrait;
use DPRMC\ComplySciApi\Objects\InsertableObjects\InsertableRestrictedSecurity;
use DPRMC\ComplySciApi\Objects\ResponseInsertedRestrictedSecurities;
use DPRMC\ComplySciApi\Objects\ResponseSecurityLookup;
use DPRMC\ComplySciApi\Objects\RestrictedSecurity;
use DPRMC\ComplySciApi\Objects\ResponseGetRestrictedSecurities;
use GuzzleHttp\Exception\InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;


/**
 * @url https://na02.complysci.com/swagger/ui/index
 * The following URL gets pasted into the INPUT at the top of the URL above.
 * https://na02.complysci.com/swagger/docs/v1
 *
 */
class ComplySciApiClient {

    use DebugTrait;

    const BASE_URL    = 'https://na02.complysci.com';
    const API_VERSION = '2';

    protected \GuzzleHttp\Client $guzzleClient;
    public readonly string       $accessToken;
    public readonly string       $tokenType;
    public readonly int          $expiresIn; // In seconds
    public readonly string       $refreshToken;

    /**
     * The default number of records we want to pull from paginated results.
     */
    const DEFAULT_PAGE_SIZE = 10000;


    /**
     *
     */
    public function __construct() {
        $this->guzzleClient = new \GuzzleHttp\Client();
    }


    /**
     * @param string $path
     * @return string
     */
    protected function _getRequestPath( string $path ): string {
        //return self::BASE_URL . $path . '?api-version=' . self::API_VERSION;
        return self::BASE_URL . $path;
    }


    /**
     * @return void
     * @throws NotAuthenticatedException
     */
    protected function _confirmWeAreAuthenticated(): void {
        if ( ! isset( $this->accessToken ) ):
            throw new NotAuthenticatedException( "Be sure to run requestAccessToken() first to authenticate." );
        endif;
    }


    /**
     * @param ResponseInterface $response
     * @return array
     */
    protected function _getArrayFromResponse( ResponseInterface $response ): array {
        $body = $response->getBody();

        // Cast to a string: { ... }
        $body->seek( 0 );

        return json_decode( $body, TRUE );
    }


    /**
     * The first step in accessing the ComplySci API.
     * We need an access token.
     * @param string $username
     * @param string $password
     * @param bool $debug
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestAccessToken( string $username, string $password, bool $debug = FALSE ): void {
        $PATH        = '/api/oauth2/token';
        $requestPath = $this->_getRequestPath( $PATH );
        $response    = $this->guzzleClient->post( $requestPath, [
            'debug' => $debug,
            'json'  => [
                "UserName" => $username,
                "Password" => $password,
            ],
        ] );

        $data = $this->_getArrayFromResponse( $response );

        $this->accessToken  = $data[ 'access_token' ];
        $this->tokenType    = $data[ 'token_type' ];
        $this->expiresIn    = $data[ 'expires_in' ];
        $this->refreshToken = $data[ 'refresh_token' ];
    }


    /**
     * You would use this method if you were going to request the Restricted Security records in batches.
     * @param bool $debug
     * @return int The total number of Restricted Security records in the system.
     * @throws NotAuthenticatedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestNumberOfRestrictedSecurityRecords( bool $debug = FALSE ): int {
        $this->_confirmWeAreAuthenticated();

        // Get the number of total records available to us.
        $PATH        = '/api/1/restricted-list';
        $requestPath = $this->_getRequestPath( $PATH );
        $response    = $this->guzzleClient->post( $requestPath, [
            'debug'   => $debug,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
            ],
            'json'    => [
                "CurrentPage" => 1,
                "PageSize"    => 1,
            ],
        ] );

        $responseAsArray = $this->_getArrayFromResponse( $response );

        $totalCount = $responseAsArray[ 'TotalCount' ];

        return $totalCount;
    }


    /**
     * @param string|NULL $listName
     * @param int $currentPage
     * @param int $pageSize
     * @param bool $isActiveList
     * @param bool $debug
     * @return ResponseGetRestrictedSecurities
     * @throws NotAuthenticatedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestRestrictedSecuritiesBatch( string $listName = NULL,
                                                      int    $currentPage = 1,
                                                      int    $pageSize = self::DEFAULT_PAGE_SIZE,
                                                      bool   $isActiveList = TRUE,
                                                      bool   $debug = FALSE ): ResponseGetRestrictedSecurities {
        $this->debug = $debug;
        $this->_confirmWeAreAuthenticated();

        // Get the number of total records available to us.
        $PATH        = '/api/1/restricted-list';
        $requestPath = $this->_getRequestPath( $PATH );

        $jsonOptions = [
            "CurrentPage"  => $currentPage,
            "PageSize"     => $pageSize,
            "IsActiveList" => $isActiveList,
        ];
        if ( $listName ):
            $jsonOptions[ 'ListName' ] = $listName;
        endif;

        $response = $this->guzzleClient->post( $requestPath, [
            'debug'   => FALSE,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
            ],
            'json'    => $jsonOptions,
        ] );

        $responseAsArray = $this->_getArrayFromResponse( $response );

        return new ResponseGetRestrictedSecurities( $responseAsArray );
    }


    /**
     * @param string|NULL $listName
     * @param int|NULL $limit
     * @param bool $isActiveList
     * @param bool $debug
     * @return ResponseGetRestrictedSecurities
     * @throws NotAuthenticatedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestRestrictedSecurities( string $listName = NULL,
                                                 int    $limit = NULL,
                                                 bool   $isActiveList = TRUE,
                                                 bool   $debug = FALSE ): ResponseGetRestrictedSecurities {
        $pageSize    = self::DEFAULT_PAGE_SIZE;
        $this->debug = $debug;
        $this->_confirmWeAreAuthenticated();

        $ResultSet = new ResponseGetRestrictedSecurities();

        $runAnotherBatch = TRUE;
        $i               = 1;
        $count           = 0;
        do {
            $newResultSet      = $this->requestRestrictedSecuritiesBatch( $listName,
                                                                          $i,
                                                                          $pageSize,
                                                                          $isActiveList,
                                                                          $debug );
            $numInNewResultSet = $newResultSet->numSecuritiesInAllLists();
            $count             += $numInNewResultSet;
            $ResultSet         = $ResultSet->mergeResultSet( $newResultSet );

            if ( $count >= $limit ):
                $runAnotherBatch = FALSE;
            endif;

            if ( 0 == $numInNewResultSet ):
                $runAnotherBatch = FALSE;
            endif;

        } while ( $runAnotherBatch );


        return $ResultSet;
    }


    /**
     * @param array $restrictedSecurities
     * @param bool $debug
     * @return ResponseInsertedRestrictedSecurities
     * @throws InvalidInsertException
     * @throws NotAuthenticatedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestInsertRestrictedSecurities( array $restrictedSecurities, bool $debug = FALSE ): ResponseInsertedRestrictedSecurities {
        $this->_confirmWeAreAuthenticated();

        // Get the number of total records available to us.
        $PATH        = '/api/1/restricted-list/insert';
        $requestPath = $this->_getRequestPath( $PATH );

        $arrayOfRestrictedSecurities = InsertableRestrictedSecurity::getArrayOfRestrictedSecuritiesToInsert( $restrictedSecurities );

        $jsonOptions = [
            "Records" => $arrayOfRestrictedSecurities,
        ];

        try {
            $response = $this->guzzleClient->post( $requestPath, [
                'debug'   => $debug,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ],
                'json'    => $jsonOptions,
            ] );
        } catch ( \Exception $exception ) {
            $response             = $exception->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();

            if ( $this->_isInvalidInsertException( $responseBodyAsString ) ):
                throw new InvalidInsertException( "One of the symbols was probably not in the ComplySci system. Try it from their web interface.",
                                                  0,
                                                  NULL,
                                                  $arrayOfRestrictedSecurities );
            endif;
        }


        $responseAsArray = $this->_getArrayFromResponse( $response );

        return new ResponseInsertedRestrictedSecurities( $responseAsArray );
    }


    /**
     * @param array $tickers
     * @param string $currencyCode
     * @param bool $includeInactiveSecurities
     * @param bool $debug
     * @return ResponseSecurityLookup
     * @throws NotAuthenticatedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @url https://na01.complysci.com/swagger/ui/index#!/Security/Security_SecuritiesSearch
     */
    public function requestSecurityLookupByTickers( array  $tickers,
                                                    string $currencyCode = 'USD',
                                                    bool   $includeInactiveSecurities = TRUE,
                                                    bool   $debug = FALSE ): ResponseSecurityLookup {
        $this->_confirmWeAreAuthenticated();

        // Get the number of total records available to us.
        $PATH        = '/api/1/securities/security-lookup';
        $requestPath = $this->_getRequestPath( $PATH );

        $jsonOptions = [
            'CurrencyCode'              => $currencyCode,
            "Tickers"                   => $tickers,
            'IncludeInactiveSecurities' => $includeInactiveSecurities,
        ];

        $response = $this->guzzleClient->post( $requestPath, [
            'debug'   => $debug,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
            ],
            'json'    => $jsonOptions,
        ] );

        $responseAsArray = $this->_getArrayFromResponse( $response );

        return new ResponseSecurityLookup( $responseAsArray );
    }


    /**
     * UNFINISHED! MDD
     * @param bool $debug
     * @return void
     * @throws NotAuthenticatedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * "Status": [
     * "Open",
     * "In Process"
     * ],
     * "CreatedAfter": "2023-12-08T15:52:20.2464073Z",
     * "CreatedBefore": "2023-12-22T15:52:20.2464073Z",
     * "UpdatedAfter": "2023-12-15T15:52:20.2464073Z",
     * "UpdateBefore": "2023-12-22T15:52:20.2464073Z",
     * "Groups": [
     * "string"
     * ],
     * "GroupIds": [
     * 0
     * ],
     * "Users": [
     * "string"
     * ],
     * "UserIds": [
     * 0
     * ],
     * "CurrentPage": 1,
     * "PageSize": 100
     */
    public function requestCommunications( array $users = [], Carbon $createdAfter = NULL, Carbon $createdBefore = NULL, bool $debug = FALSE ) {
        $this->_confirmWeAreAuthenticated();

        $PATH        = '/api/2/communications';
        $requestPath = $this->_getRequestPath( $PATH );


        $jsonOptions = [];

        if ( ! empty( $users ) ):
            $jsonOptions[ 'Users' ] = $users;
        endif;

        if ( $createdAfter ):
            $jsonOptions[ 'CreatedAfter' ] = $createdAfter->toISOString();
        endif;

        if ( $createdBefore ):
            $jsonOptions[ 'CreatedBefore' ] = $createdBefore->toISOString();
        endif;

        try {
            $response = $this->guzzleClient->post( $requestPath, [
                'debug'   => $debug,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ],
                'json'    => $jsonOptions,
            ] );
        } catch ( \GuzzleHttp\Exception\ClientException $e ) {

            dd( $e->getResponse()->getBody()->getContents() );

        }


        $responseAsArray = $this->_getArrayFromResponse( $response );

        dd( $responseAsArray );

        //return new ResponseSecurityLookup( $responseAsArray );
    }


    /**
     * This will return true if the SYMBOL being passed to ComplySci isn't in their system.
     * @param $responseBodyAsString
     * @return bool
     * @example "Error encountered during inserting company list items. Issue: Error encountered during insert 2: 515, Level 16, State 2, Procedure dbo.usp_iCompanyListItem, Line 203, Issue: Cannot insert the value NULL into column 'ItemReferenceID', table 'PTCC.dbo.CompanyListItems'; column does not allow nulls. INSERT fails."
     */
    protected function _isInvalidInsertException( $responseBodyAsString ): bool {
        $testString = "Cannot insert the value NULL into column 'ItemReferenceID'";
        if ( str_contains( $responseBodyAsString, $testString ) ):
            return TRUE;
        endif;
        return FALSE;
    }
}