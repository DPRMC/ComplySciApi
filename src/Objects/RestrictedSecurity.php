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
     * @var string|null Let's try this as a public readonly now.
     */
    public readonly ?string $ListName;


    /**
     * @param array $record
     */
    public function __construct( array $record, string $listName ) {
        $this->SecurityId          = $record[ 'SecurityId' ] ?? NULL;
        $this->StartDate           = isset( $record[ 'StartDate' ] ) ? Carbon::parse( $record[ 'StartDate' ] ) : NULL;
        $this->EffectiveTime       = $record[ 'EffectiveTime' ] ?? NULL;
        $this->ExpirationTime      = $record[ 'ExpirationTime' ] ?? NULL;
        $this->MonitoredBy         = $record[ 'MonitoredBy' ] ?? NULL;
        $this->GKKey               = $record[ 'GKKey' ] ?? NULL;
        $this->Industry            = $record[ 'Industry' ] ?? NULL;
        $this->SupervisorNote      = $record[ 'SupervisorNote' ] ?? NULL;
        $this->Symbol              = $record[ 'Symbol' ] ?? NULL;
        $this->CUSIP               = $record[ 'CUSIP' ] ?? NULL;
        $this->ISIN                = $record[ 'ISIN' ] ?? NULL;
        $this->SEDOL               = $record[ 'SEDOL' ] ?? NULL;
        $this->RestrictedGroups    = $record[ 'RestrictedGroups' ] ?? NULL;
        $this->RestrictedUsers     = $record[ 'RestrictedUsers' ] ?? NULL;
        $this->CompanyName         = $record[ 'CompanyName' ] ?? NULL;
        $this->SecurityType        = $record[ 'SecurityType' ] ?? NULL;
        $this->SecurityDescription = $record[ 'SecurityDescription' ] ?? NULL;
        $this->Valoren             = $record[ 'Valoren' ] ?? NULL;
        $this->FollowUpDate        = isset( $record[ 'FollowUpDate' ] ) ? Carbon::parse( $record[ 'FollowUpDate' ] ) : NULL;
        $this->EndDate             = isset( $record[ 'EndDate' ] ) ? Carbon::parse( $record[ 'EndDate' ] ) : NULL;
        $this->ReasonAdded         = $record[ 'ReasonAdded' ] ?? NULL;
        $this->ReasonRemoved       = $record[ 'ReasonRemoved' ] ?? NULL;
        $this->DealId              = $record[ 'DealId' ] ?? NULL;
        $this->CustomField01       = $record[ 'CustomField01' ] ?? NULL;
        $this->CustomField02       = $record[ 'CustomField02' ] ?? NULL;
        $this->CustomField03       = $record[ 'CustomField03' ] ?? NULL;
        $this->CustomField04       = $record[ 'CustomField04' ] ?? NULL;
        $this->CustomField05       = $record[ 'CustomField05' ] ?? NULL;
        $this->CustomField06       = $record[ 'CustomField06' ] ?? NULL;
        $this->CustomField07       = $record[ 'CustomField07' ] ?? NULL;
        $this->CustomField08       = $record[ 'CustomField08' ] ?? NULL;
        $this->ListName            = $listName;
    }


    public function getUniqueKeyForRecord( string $listName ): string {
        $securityId = $this->SecurityId;
        $startDate  = $this->StartDate->toDateString();

        return md5( $securityId . $listName . $startDate );
    }


    /**
     * Setter for the ListName property. Not sure this is needed anymore.
     * @param string $listName
     * @return void
     */
    public function setListName( string $listName ): void {
        $this->ListName = $listName;
    }

}