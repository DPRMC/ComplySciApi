<?php

namespace DPRMC\ComplySciApi\Exceptions;


class NoGkKeyForSymbolException extends \Exception {

    /**
     * @var string The symbol (ticker, cusip, etc) that was submitted to Comply to try to find their GkKey.
     */
    public string $symbol;


    /**
     * @var string Just a little note for us developers to help us remember.
     */
    public string $devNote = "I have a method that finds the GkKey given a symbol. This exception is thrown if the Comply API doesn't have a GkKey for a given symbol.";

    public function __construct( string $message = "", int $code = 0, ?Throwable $previous = NULL, string $symbol = '' ) {
        parent::__construct( $message, $code, $previous );

        $this->symbol = $symbol;
    }

}