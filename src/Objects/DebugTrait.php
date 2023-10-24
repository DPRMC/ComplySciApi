<?php

namespace DPRMC\ComplySciApi\Objects;


use Carbon\Carbon;

trait DebugTrait {

    public bool $debug = false;
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
}