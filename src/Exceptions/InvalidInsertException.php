<?php

namespace DPRMC\ComplySciApi\Exceptions;


class InvalidInsertException extends \Exception {

    public array  $arrayOfInsertedSecurities;
    public string $devNote = "One of the symbols in the symbols array is invalid. Test them through the web interface to see which is the problem child.";

    public function __construct( string $message = "", int $code = 0, ?Throwable $previous = NULL, array $arrayOfInsertedSecurities = [] ) {
        parent::__construct( $message, $code, $previous );

        $this->arrayOfInsertedSecurities = $arrayOfInsertedSecurities;
    }

}