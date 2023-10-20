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

        $this->parseAndAddRecords( $this->ListName, $List[ 'Records' ] );
    }


    /**
     * @param string $listName
     * @param array $Records
     * @return void
     */
    public function parseAndAddRecords( string $listName, array $Records ): void {
        foreach ( $Records as $unparsedRecord ):
            $uniqueKey                   = $this->getUniqueKeyForRecord( $listName, $unparsedRecord );
            $this->Records[ $uniqueKey ] = new RestrictedSecurity( $unparsedRecord );
        endforeach;
    }


    /**
     * From ComplySci:
     * Unique Identifiers:
     * List items don’t have record IDs or primary keys.
     * The system identifies their uniqueness based on Security Identifier + List Name + Start Date.
     *
     * @param string $listName
     * @param array $unparsedRecord
     * @return string
     */
    public function getUniqueKeyForRecord( string $listName, array $unparsedRecord ): string {
        $securityId = $unparsedRecord[ 'SecurityId' ];
        $startDate  = substr( $unparsedRecord[ 'StartDate' ], 0, 10 );

        return md5( $securityId . $listName . $startDate );
    }

}