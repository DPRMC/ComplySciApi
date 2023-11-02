<?php

namespace DPRMC\ComplySciApi\Objects;


use Carbon\Carbon;

trait DataCleanerTrait {

    protected function _splitCommaDelimitedString( string $string = NULL ): array {
        if( empty($string)):
            return [];
        endif;
        $string = trim( $string );
        $array  = explode( ',', $string );
        return array_map( 'trim', $array );
    }
}