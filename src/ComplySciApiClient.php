<?php

namespace DPRMC\ComplySciApi;

use DPRMC\ComplySciApi\Exceptions\NotAuthenticatedException;
use DPRMC\ComplySciApi\Objects\RestrictedList;
use Psr\Http\Message\ResponseInterface;

/**
 * @url https://na02.complysci.com/swagger/ui/index
 * The following URL gets pasted into the INPUT at the top of the URL above.
 * https://na02.complysci.com/swagger/docs/v1
 *
 */
class ComplySciApiClient {

    const BASE_URL    = 'https://na02.complysci.com';
    const API_VERSION = '2';

    protected \GuzzleHttp\Client $guzzleClient;
    public readonly string       $accessToken;
    public readonly string       $tokenType;
    public readonly int          $expiresIn; // In seconds
    public readonly string       $refreshToken;

    protected bool $debug;

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
     * fwrite is used here so debug statements will show in PHPUNIT
     * @param string $message
     * @return void
     */
    protected function _debug( string $message = '' ): void {
        if ( ! $this->debug ):
            return;
        endif;

        fwrite( STDERR, "\n" . $message );
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
     * @return array
     * @throws NotAuthenticatedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestRestrictedSecuritiesBatch( string $listName = NULL,
                                                      int    $currentPage = 1,
                                                      int    $pageSize = self::DEFAULT_PAGE_SIZE,
                                                      bool   $isActiveList = TRUE,
                                                      bool   $debug = FALSE ): array {
        $this->debug = $debug;
        $this->_confirmWeAreAuthenticated();

        $listsByListName = [];

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

        $lists = $responseAsArray[ 'Lists' ];
        $this->_debug( "Found " . count( $lists ) . " lists in the response from the last request." );

        /**
         * $lists =>
         * 0 => array:10 [
         * "MonitoringManagingGroups" => " Supervisors, Restricted List Supervision"
         * "ListName" => "Restricted Securities List"
         * "ListDescription" => "List updated through FTP"
         * "CreatedBy" => "ComplySciDeerParkRD"
         * "CreatedDate" => "2021-08-19T17:09:32.033"
         * "LastModifiedBy" => "CSIAdmin"
         * "LastModifiedDate" => "2021-10-12T20:56:21.957"
         * "IsActive" => true
         * "VisibleToGroups" => "All Employees"
         * "Records" => array:100 [
         * 0 => array:31 [
         *
         * @var array $list
         */
        foreach ( $lists as $i => $list ):
            $listName = $list[ 'ListName' ];
            $this->_debug( "List (" . $i . ") is " . $listName );
            if ( ! isset( $listsByListName[ $listName ] ) ):
                $this->_debug( "List doesn't exist yet, so adding it to the listsByName array." );
                $listsByListName[ $listName ] = new RestrictedList( $list );
            else:
                $this->_debug( "List already existed, so adding another [" . count( $list[ 'Records' ] ) . "] Restricted Securities to my copy of that list in memory." );
                // @var RestrictedList $listsByListName[$listName]
                $listsByListName[ $listName ]->parseAndAddRecords( $listName, $list[ 'Records' ] );
            endif;

            $this->_debug( "There are now this many records in that list: " . count( $listsByListName[ $listName ]->Records ) );
        endforeach; // End looping through potentially multiple lists returned in result set.

        return $listsByListName;
    }


    /**
     * @param string|NULL $listName
     * @param int|NULL $limit
     * @param bool $isActiveList
     * @param bool $debug
     * @return array
     * @throws NotAuthenticatedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestRestrictedSecurities( string $listName = NULL,
                                                 int    $limit = NULL,
                                                 bool   $isActiveList = TRUE,
                                                 bool   $debug = FALSE ): array {
        $pageSize    = self::DEFAULT_PAGE_SIZE;
        $this->debug = $debug;
        $this->_confirmWeAreAuthenticated();

        $listsByListName = [];

        $totalCount = $this->requestNumberOfRestrictedSecurityRecords( $debug );

        // If a limit was passed in, determine if we need to set it here.
        if ( $limit && $totalCount > $limit ):
            $this->_debug( "This method was called with a limit which is less than the total number of records available to get, which was " . $totalCount );
            $this->_debug( " So I will only get " . $limit . " records from ComplySci now." );
            $totalCount = $limit;
        endif;


        /**
         *
         */
        if ( $pageSize > $totalCount ):
            $this->_debug( "The default page size is greater than the total count of securities we are going to request." );
            $this->_debug( "So I am going to set the page size equal to the total count, so we get them all in one batch." );
            $pageSize = $totalCount;
        endif;


        $numBatches = ceil( $totalCount / $pageSize );

        $this->_debug( "Total Count of Restricted Securities: " . $totalCount );
        $this->_debug( "Page Size is: " . $pageSize );
        $this->_debug( "Num Requests/Batches I will ask ComplySci for: " . $numBatches );


        for ( $i = 1; $i <= $numBatches; $i++ ):
            $this->_debug( "----- Processing batch " . $i );
            $this->_debug( "Page size is " . $pageSize );
            $newListsByListName = $this->requestRestrictedSecuritiesBatch( $listName,
                                                                           $i,
                                                                           $pageSize,
                                                                           $isActiveList,
                                                                           $debug );
            $listsByListName    = array_merge_recursive( $listsByListName, $newListsByListName );
        endfor; // End looping through batches.

        return $listsByListName;
    }


}