<?php

namespace DPRMC\ComplySciApi\Exceptions;


class MultipleGkKeysForSymbolException extends \Exception {

    /**
     * @var string The symbol (ticker, cusip, etc) that was submitted to Comply to try to find their GkKey.
     */
    public string $symbol;

    /**
     * @var array The list of GkKeys that were returned from the Comply API given the above symbol.
     */
    public array  $gkKeys;

    /**
     * @var string Just a little note for us developers to help us remember.
     */
    public string $devNote = "I have a method that finds the GkKey given a symbol. This exception is thrown if the Comply API returns more than one GkKey for a symbol. This is a problem if I am trying to uniquely identify a security in the Comply system by ticker/sedol/isin/cusip.";

    public function __construct( string $message = "", int $code = 0, ?Throwable $previous = NULL, string $symbol = '', array $gkKeys = [] ) {
        parent::__construct( $message, $code, $previous );

        $this->symbol = $symbol;
        $this->gkKeys = $gkKeys;
    }

}