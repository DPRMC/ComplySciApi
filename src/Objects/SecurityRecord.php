<?php

namespace DPRMC\ComplySciApi\Objects;


use Carbon\Carbon;

class SecurityRecord {

    use DataCleanerTrait;

    public readonly ?string $Issuer;
    public readonly ?string $SecurityDescription;
    public readonly ?string $Ticker;
    public readonly ?string $CUSIP;
    public readonly ?string $SEDOL;
    public readonly ?string $ISIN;
    public readonly ?string $GKKey;
    public readonly ?array  $Exchanges;
    public readonly ?string $CurrencyCode;
    public readonly ?string $SecurityType;
    public readonly ?string $Status;

    /**
     * @param array $record
     */
    public function __construct( array $record ) {
        $this->Issuer              = $record[ 'Issuer' ] ?? NULL;
        $this->SecurityDescription = $record[ 'Security Description' ] ?? NULL;
        $this->Ticker              = $record[ 'Ticker' ] ?? NULL;
        $this->CUSIP               = $record[ 'CUSIP' ] ?? NULL;
        $this->SEDOL               = $record[ 'SEDOL' ] ?? NULL;
        $this->ISIN                = $record[ 'ISIN' ] ?? NULL;
        $this->GKKey               = $record[ 'GKKey' ] ?? NULL;
        $this->Exchanges           = $this->_splitCommaDelimitedString( $record[ 'Exchanges' ] );
        $this->CurrencyCode        = $record[ 'CurrencyCode' ] ?? NULL;
        $this->SecurityType        = $record[ 'SecurityType' ] ?? NULL;
        $this->Status              = $record[ 'Status' ] ?? NULL;
    }
}