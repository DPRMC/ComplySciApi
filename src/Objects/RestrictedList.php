<?php

namespace DPRMC\ComplySciApi\Objects;


use Carbon\Carbon;

class RestrictedList {

    use DataCleanerTrait;

    public readonly array  $MonitoringManagingGroups;
    public readonly string $ListName;
    public readonly string $ListDescription;
    public readonly string $CreatedBy;
    public readonly Carbon $CreatedDate;
    public readonly string $LastModifiedBy;
    public readonly Carbon $LastModifiedDate;
    public readonly bool   $IsActive;
    public readonly array  $VisibleToGroups;
    public array           $Records;


    public function __construct( array $List ) {

        $this->MonitoringManagingGroups = $this->_splitCommaDelimitedString( $List[ 'MonitoringManagingGroups' ] );
        $this->ListName                 = $List[ 'ListName' ];
        $this->ListDescription          = $List[ 'ListDescription' ];
        $this->CreatedBy                = $List[ 'CreatedBy' ];
        $this->CreatedDate              = Carbon::parse( $List[ 'CreatedDate' ] );
        $this->LastModifiedBy           = $List[ 'LastModifiedBy' ];
        $this->LastModifiedDate         = Carbon::parse( $List[ 'LastModifiedDate' ] );
        $this->IsActive                 = $List[ 'IsActive' ];
        $this->VisibleToGroups          = $this->_splitCommaDelimitedString( $List[ 'VisibleToGroups' ] ); // Should this be an array?

        $this->_parseRecords( $List[ 'Records' ] );
    }


    protected function _parseRecords( array $Records ): void {
        foreach ( $Records as $unparsedRecord ):
            $this->Records[] = new RestrictedSecurity( $unparsedRecord );
        endforeach;
    }

}