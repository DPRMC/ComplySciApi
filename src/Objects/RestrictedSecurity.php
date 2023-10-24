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
     * @param array $record
     */
    public function __construct( array $record ) {
        $this->SecurityId          = $record[ 'SecurityId' ];
        $this->StartDate           = isset( $record[ 'StartDate' ] ) ? Carbon::parse( $record[ 'StartDate' ] ) : NULL;
        $this->EffectiveTime       = $record[ 'EffectiveTime' ];
        $this->ExpirationTime      = $record[ 'ExpirationTime' ];
        $this->MonitoredBy         = $record[ 'MonitoredBy' ];
        $this->GKKey               = $record[ 'GKKey' ];
        $this->Industry            = $record[ 'Industry' ];
        $this->SupervisorNote      = $record[ 'SupervisorNote' ];
        $this->Symbol              = $record[ 'Symbol' ];
        $this->CUSIP               = $record[ 'CUSIP' ];
        $this->ISIN                = $record[ 'ISIN' ];
        $this->RestrictedGroups    = $record[ 'RestrictedGroups' ];
        $this->RestrictedUsers     = $record[ 'RestrictedUsers' ];
        $this->CompanyName         = $record[ 'CompanyName' ];
        $this->SecurityType        = $record[ 'SecurityType' ];
        $this->SecurityDescription = $record[ 'SecurityDescription' ];
        $this->Valoren             = $record[ 'Valoren' ];
        $this->FollowUpDate        = isset( $record[ 'FollowUpDate' ] ) ? Carbon::parse( $record[ 'FollowUpDate' ] ) : NULL;
        $this->EndDate             = isset( $record[ 'EndDate' ] ) ? Carbon::parse( $record[ 'EndDate' ] ) : NULL;
        $this->ReasonAdded         = $record[ 'ReasonAdded' ];
        $this->ReasonRemoved       = $record[ 'ReasonRemoved' ];
        $this->DealId              = $record[ 'DealId' ];
        $this->CustomField01       = $record[ 'CustomField01' ];
        $this->CustomField02       = $record[ 'CustomField02' ];
        $this->CustomField03       = $record[ 'CustomField03' ];
        $this->CustomField04       = $record[ 'CustomField04' ];
        $this->CustomField05       = $record[ 'CustomField05' ];
        $this->CustomField06       = $record[ 'CustomField06' ];
        $this->CustomField07       = $record[ 'CustomField07' ];
        $this->CustomField08       = $record[ 'CustomField08' ];
    }


    public function getUniqueKeyForRecord( string $listName ): string {
        $securityId = $this->SecurityId;
        $startDate  = $this->StartDate->toDateString();

        return md5( $securityId . $listName . $startDate );
    }

}