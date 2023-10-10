<?php

namespace DPRMC\ComplySciApi;

class ComplySciApiClient {

    const BASE_URL    = 'https://na02.complysci.com';
    const API_VERSION = '2';

    protected \GuzzleHttp\Client $guzzleClient;
    protected string             $accessToken;

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
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestAccessToken( string $username, string $password ): void {
        $PATH        = '/api/oauth2/token';
        $requestPath = $this->_getRequestPath( $PATH );
        $response    = $this->guzzleClient->post( $requestPath, [
            'debug' => true,
            'form_params' => [
                "UserName" => $username,
                "Password" => $password,
            ],
        ] );

        $body = $response->getBody();

        dump( $body );
    }
}