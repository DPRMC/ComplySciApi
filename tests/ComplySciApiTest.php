<?php


class ComplySciApiTest extends \PHPUnit\Framework\TestCase {

    const DEBUG = FALSE;

    public static \DPRMC\ComplySciApi\ComplySciApiClient $client;

    public static function setUpBeforeClass(): void {
        self::$client = new \DPRMC\ComplySciApi\ComplySciApiClient();
        //self::$client->requestAccessToken( $_ENV[ 'COMPLYSCI_USER' ], $_ENV[ 'COMPLYSCI_PASS' ] );
        self::$client->requestAccessToken( $_ENV[ 'DEV_COMPLYSCI_USER' ], $_ENV[ 'DEV_COMPLYSCI_PASS' ] );
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
        $ResultSet = self::$client->requestRestrictedSecuritiesBatch( 'Restricted Securities',
                                                                      1,
                                                                      1,
                                                                      FALSE,
                                                                      self::DEBUG );

        $this->assertInstanceOf( \DPRMC\ComplySciApi\Objects\ResponseGetRestrictedSecurities::class, $ResultSet );
    }


    /**
     * @test
     * @group list
     */
    public function testGetRestrictedSecurityListShouldReturnArray() {
        $ResultSet = self::$client->requestRestrictedSecurities( NULL,
                                                                 10,
                                                                 TRUE,
                                                                 self::DEBUG );

        $this->assertInstanceOf( \DPRMC\ComplySciApi\Objects\ResponseGetRestrictedSecurities::class, $ResultSet );

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
     * @group insert
     */
    public function testInsertRestrictedSecurities() {

        $listName             = 'Restricted List';
        $restrictedSecurities = [];
        $listAdministrator    = 'mdrennen@deerparkrd.com';
        $groups               = [ 'All Employees' ];
        $employees            = [];

        $restrictedSecurities[] = new \DPRMC\ComplySciApi\Objects\InsertableObjects\InsertableRestrictedSecurity( 'AAPL',
                                                                                                                  \Carbon\Carbon::today(),
                                                                                                                  $listName,
                                                                                                                  $listAdministrator,
                                                                                                                  $groups );

        try {
            $responseInsertedRestrictedSecurities = self::$client->requestInsertRestrictedSecurities( $restrictedSecurities,
                                                                                                      self::DEBUG );

            $this->assertInstanceOf( \DPRMC\ComplySciApi\Objects\ResponseInsertedRestrictedSecurities::class,
                                     $responseInsertedRestrictedSecurities );

            $this->assertIsInt( $responseInsertedRestrictedSecurities->totalCount );
            $this->assertGreaterThan( 0, $responseInsertedRestrictedSecurities->totalCount );
        } catch ( GuzzleHttp\Exception\ClientException $e ) {
            $response             = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            dump( $responseBodyAsString );
        }


        $ResponseGetRestrictedSecurities = self::$client->requestRestrictedSecurities( $listName, NULL, TRUE, self::DEBUG );
        $this->assertInstanceOf( \DPRMC\ComplySciApi\Objects\ResponseGetRestrictedSecurities::class,
                                 $ResponseGetRestrictedSecurities );
        $this->assertGreaterThan( 0, $ResponseGetRestrictedSecurities->numSecuritiesInAllLists() );

    }

}