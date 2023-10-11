<?php

namespace DPRMC\ComplySciApi\Objects;


use Carbon\Carbon;

class RestrictedSecurity {

    use DataCleanerTrait;

    public readonly ?Carbon $StartDate;
    public readonly ?string $SecurityId;
    public readonly ?string $EffectiveTime;
    public readonly ?string $ExpirationTime;
    public readonly ?string $MonitoredBy;
    public readonly ?int    $GKKey;
    public readonly ?string $Industry;
    public readonly ?string $SupervisorNote;
    public readonly ?string $Symbol;
    public readonly ?string $CUSIP;
    public readonly ?string $ISIN;
    public readonly ?string $SEDOL;
    public readonly ?string $RestrictedGroups;
    public readonly ?string $RestrictedUsers;
    public readonly ?string $CompanyName;
    public readonly ?string $SecurityType;
    public readonly ?string $SecurityDescription;
    public readonly ?int    $Valoren;
    public readonly ?Carbon $FollowUpDate;
    public readonly ?Carbon $EndDate;
    public readonly ?string $ReasonAdded;
    public readonly ?string $ReasonRemoved;
    public readonly ?string $DealId;
    public readonly ?string $CustomField01;
    public readonly ?string $CustomField02;
    public readonly ?string $CustomField03;
    public readonly ?string $CustomField04;
    public readonly ?string $CustomField05;
    public readonly ?string $CustomField06;
    public readonly ?string $CustomField07;
    public readonly ?string $CustomField08;


    /**
     * @param array $Record
     */
    public function __construct( array $Record ) {
        $this->StartDate           = isset( $Record[ 'StartDate' ] ) ? Carbon::parse( $Record[ 'StartDate' ] ) : NULL;
        $this->EffectiveTime       = $Record[ 'EffectiveTime' ];
        $this->ExpirationTime      = $Record[ 'ExpirationTime' ];
        $this->MonitoredBy         = $Record[ 'MonitoredBy' ];
        $this->GKKey               = $Record[ 'GKKey' ];
        $this->Industry            = $Record[ 'Industry' ];
        $this->SupervisorNote      = $Record[ 'SupervisorNote' ];
        $this->Symbol              = $Record[ 'Symbol' ];
        $this->CUSIP               = $Record[ 'CUSIP' ];
        $this->ISIN                = $Record[ 'ISIN' ];
        $this->RestrictedGroups    = $Record[ 'RestrictedGroups' ];
        $this->RestrictedUsers     = $Record[ 'RestrictedUsers' ];
        $this->CompanyName         = $Record[ 'CompanyName' ];
        $this->SecurityType        = $Record[ 'SecurityType' ];
        $this->SecurityDescription = $Record[ 'SecurityDescription' ];
        $this->Valoren             = $Record[ 'Valoren' ];
        $this->FollowUpDate        = isset( $Record[ 'FollowUpDate' ] ) ? Carbon::parse( $Record[ 'FollowUpDate' ] ) : NULL;
        $this->EndDate             = isset( $Record[ 'EndDate' ] ) ? Carbon::parse( $Record[ 'StarEndDatetDate' ] ) : NULL;
        $this->ReasonAdded         = $Record[ 'ReasonAdded' ];
        $this->ReasonRemoved       = $Record[ 'ReasonRemoved' ];
        $this->DealId              = $Record[ 'DealId' ];
        $this->CustomField01       = $Record[ 'CustomField01' ];
        $this->CustomField02       = $Record[ 'CustomField02' ];
        $this->CustomField03       = $Record[ 'CustomField03' ];
        $this->CustomField04       = $Record[ 'CustomField04' ];
        $this->CustomField05       = $Record[ 'CustomField05' ];
        $this->CustomField06       = $Record[ 'CustomField06' ];
        $this->CustomField07       = $Record[ 'CustomField07' ];
        $this->CustomField08       = $Record[ 'CustomField08' ];
    }

}