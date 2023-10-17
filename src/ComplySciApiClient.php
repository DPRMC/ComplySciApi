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


    public function __construct() {
        $this->guzzleClient = new \GuzzleHttp\Client();
    }

    protected function _getRequestPath( string $path ): string {
        //return self::BASE_URL . $path . '?api-version=' . self::API_VERSION;
        return self::BASE_URL . $path;
    }


    /**
     * @param string $username
     * @param string $password
     * @param bool $debug
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestAccessToken( string $username, string $password, bool $debug = FALSE ): void {
        $this->debug = $debug;
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
     * @return void
     * @throws NotAuthenticatedException
     */
    protected function _confirmWeAreAuthenticated(): void {
        if ( ! isset( $this->accessToken ) ):
            throw new NotAuthenticatedException( "Be sure to run requestAccessToken() first to authenticate." );
        endif;
    }


    public function requestAllRestrictedSecurities( bool $debug = FALSE ): array {
        $this->debug = $debug;
        $this->_confirmWeAreAuthenticated();

        $listsByListName = [];

        $PATH        = '/api/1/restricted-list';
        $requestPath = $this->_getRequestPath( $PATH );
        $response    = $this->guzzleClient->post( $requestPath, [
            'debug'   => $debug,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->accessToken,
            ],
            'json'    => [
                "CurrentPage"  => 1,
                "PageSize"     => 1,
                "IsActiveList" => TRUE,
            ],
        ] );

        $responseAsArray = $this->_getArrayFromResponse( $response );

        $totalCount = $responseAsArray[ 'TotalCount' ];
        $pageSize   = 100;
        $numBatches = ceil( $totalCount / $pageSize );

        $this->_debug("Total Count of Restricted Securities: " . $totalCount);
        $this->_debug("Page Size is: " . $pageSize);
        $this->_debug("Num Requests/Batches I will ask ComplySci for: " . $numBatches);



        for ( $i = 1; $i <= $numBatches; $i++ ):
            $this->_debug("Starting batch " . $i);
            $response        = $this->guzzleClient->post( $requestPath, [
                'debug'   => $debug,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                ],
                'json'    => [
                    "CurrentPage"  => $i,
                    "PageSize"     => $pageSize,
                    "IsActiveList" => FALSE,
                ],
            ] );
            $responseAsArray = $this->_getArrayFromResponse( $response );
            $lists           = $responseAsArray[ 'Lists' ];
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
                dump($list);
                $listName = $list[ 'ListName' ];
                if ( ! isset( $listsByListName[ $listName ] ) ):
                    $listsByListName[ $listName ] = new RestrictedList( $list );
                else:
                    // @var RestrictedList $listsByListName[$listName]
                    $listsByListName[ $listName ]->parseAndAddRecords( $list[ 'Records' ] );
                endif;
            endforeach; // End looping through potentially multiple lists returned in result set.
        endfor; // End looping through batches.

        return $listsByListName;
    }

    protected function _debug(string $message=''): void {
        if( ! $this->debug ):
            return;
        endif;

        echo "\n" . $message;
    }
}