<?php

namespace DPRMC\ComplySciApi\Objects;


use Carbon\Carbon;


/**
 * This object contains the
 */
class ResponseSecurityLookup {

    use DebugTrait;

    /**
     * @var SecurityRecord[]
     */
    public array $Records = [];


    /**
     * @param array $arrayFromApi
     */
    public function __construct( array $arrayFromApi = [] ) {

        if ( empty( $arrayFromApi ) ):
            return;
        endif;

        $records = $arrayFromApi[ 'Records' ];
        $this->_debug( "Found " . count( $records ) . " records in the response from the last request." );

        /**
         * $records =>
         * 0 => array:11 [
         * "Issuer" => "Apple Inc"
         * "Security Description" => "Apple Rg"
         * "Ticker" => "AAPL"
         * "CUSIP" => "037833100"
         * "SEDOL" => "2046251"
         * "ISIN" => "US0378331005"
         * "GKKey" => "908440"
         * "Exchanges" => "Bolsa de Comercio de Santiago, USD,Bolsa de..."
         * "CurrencyCode" => "USD"
         * "SecurityType" => "Stocks"
         * "Status" => "Active"
         */

        /**
         * @var array $record Format shown in the comment above
         */
        foreach ( $records as $i => $record ):
            $this->Records[] = new SecurityRecord( $record );
        endforeach; // End looping through potentially multiple lists returned in result set.
    }
}