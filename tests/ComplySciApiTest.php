<?php


class ComplySciApiTest extends \PHPUnit\Framework\TestCase {

    public static function setUpBeforeClass(): void {

    }


    public static function tearDownAfterClass(): void {

    }


    /**
     * @test
     * @group auth
     */
    public function testAuthenticateShouldProvideKey() {
        $client = new \DPRMC\ComplySciApi\ComplySciApiClient();
        $client->requestAccessToken( $_ENV[ 'COMPLYSCI_USER' ], $_ENV[ 'COMPLYSCI_PASS' ] );

        $this->assertIsString( $client->accessToken );
        $this->assertIsString( $client->refreshToken );
        $this->assertIsString( $client->tokenType );
        $this->assertIsNumeric( $client->expiresIn );
    }


    /**
     * @test
     * @group sec_list
     */
    public function testGetRestrictedSecurityListShouldReturnArray(){
        $client = new \DPRMC\ComplySciApi\ComplySciApiClient();
        $client->requestAccessToken( $_ENV[ 'COMPLYSCI_USER' ], $_ENV[ 'COMPLYSCI_PASS' ] );

        $client->requestAllRestrictedSecurities(true);
    }


}