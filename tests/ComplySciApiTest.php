<?php


class ComplySciApiTest extends \PHPUnit\Framework\TestCase {

    const DEBUG = false;

    public static \DPRMC\ComplySciApi\ComplySciApiClient $client;

    public static function setUpBeforeClass(): void {
        self::$client = new \DPRMC\ComplySciApi\ComplySciApiClient();
        self::$client->requestAccessToken( $_ENV[ 'COMPLYSCI_USER' ], $_ENV[ 'COMPLYSCI_PASS' ] );
    }


    public static function tearDownAfterClass(): void {

    }


    /**
     * @test
     * @group auth
     */
    public function testAuthenticateShouldProvideKey() {
        $this->assertIsString( self::$client->accessToken );
        $this->assertIsString( self::$client->refreshToken );
        $this->assertIsString( self::$client->tokenType );
        $this->assertIsNumeric( self::$client->expiresIn );
    }


    /**
     * @test
     * @group count
     */
    public function testNumberOfRestrictedSecurityRecordsShouldReturnAnInteger() {
        $numberOfRestrictedSecurityRecords = self::$client->requestNumberOfRestrictedSecurityRecords( FALSE );
        $this->assertGreaterThan( 0, $numberOfRestrictedSecurityRecords );
    }


    /**
     * @test
     * @group batch
     */
    public function testRequestRestrictedSecuritiesBatchShouldReturnArray() {
        $listsByName = self::$client->requestRestrictedSecuritiesBatch( 3,
                                                                        3,
                                                                        TRUE,
                                                                        FALSE );
        $this->assertIsArray( $listsByName );
    }


    /**
     * @test
     * @group list
     */
    public function testGetRestrictedSecurityListShouldReturnArray() {
        $listsByName = self::$client->requestRestrictedSecurities( null,
                                                                   10000,
                                                                   TRUE,
                                                                   self::DEBUG );

        $this->assertIsArray($listsByName);

//
//        /**
//         * @var \DPRMC\ComplySciApi\Objects\RestrictedList $list
//         */
//        foreach ( $listsByName as $listName => $list ):
//            dump( "\n\n\n\n" );
//            dump( "LIOST NAME: " . $listName );
//            /**
//             * @var \DPRMC\ComplySciApi\Objects\RestrictedSecurity $restrictedSecurity
//             */
//            foreach ( $list->Records as $md5 => $restrictedSecurity ):
//                $startDate = 'null';
//                $endDate   = 'null';
//                if ( $restrictedSecurity->StartDate ):
//                    $startDate = $restrictedSecurity->StartDate->toDateString();
//                endif;
//
//                if ( $restrictedSecurity->EndDate ):
//                    $endDate = $restrictedSecurity->EndDate->toDateString();
//                endif;
//                dump( $restrictedSecurity->Symbol . ' [' . $startDate . '] [' . $endDate . ']' );
//            endforeach;
//        endforeach;
//
//
//        dump( 'LIST NAMES: ' . "\n\n" );
//        foreach ( $listsByName as $listName => $list ):
//            dump( $listName );
//        endforeach;
    }


    /**
     * @test
     * @group listname
     */
    public function testGetRestrictedSecurityListByNameShouldReturnArray() {
        $listsByName = self::$client->requestRestrictedSecurities( 'Restricted Securities List',
                                                                   10000,
                                                                   true,
                                                                   self::DEBUG );
        $this->assertIsArray( $listsByName );
    }

}