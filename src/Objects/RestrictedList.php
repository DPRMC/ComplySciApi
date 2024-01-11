<?php

namespace DPRMC\ComplySciApi\Objects;


use Carbon\Carbon;

class RestrictedList {

    use DataCleanerTrait;

    public readonly ?array  $MonitoringManagingGroups;
    public readonly ?string $ListName;
    public readonly ?string $ListDescription;
    public readonly ?string $CreatedBy;
    public readonly ?Carbon $CreatedDate;
    public readonly ?string $LastModifiedBy;
    public readonly ?Carbon $LastModifiedDate;
    public readonly ?bool   $IsActive;
    public readonly ?array  $VisibleToGroups;
    public ?array           $Records;


    public function __construct( array $list ) {

        $this->MonitoringManagingGroups = $this->_splitCommaDelimitedString( $list[ 'MonitoringManagingGroups' ] );
        $this->ListName                 = $list[ 'ListName' ];
        $this->ListDescription          = $list[ 'ListDescription' ];
        $this->CreatedBy                = $list[ 'CreatedBy' ];
        $this->CreatedDate              = Carbon::parse( $list[ 'CreatedDate' ] );
        $this->LastModifiedBy           = $list[ 'LastModifiedBy' ];
        $this->LastModifiedDate         = Carbon::parse( $list[ 'LastModifiedDate' ] );
        $this->IsActive                 = $list[ 'IsActive' ];
        $this->VisibleToGroups          = $this->_splitCommaDelimitedString( $list[ 'VisibleToGroups' ] ); // Should this be an array?

        $records = [];
        foreach ( $list[ 'Records' ] as $record ):
            $RestrictedSecurity = new RestrictedSecurity( $record, $this->ListName );
            $uniqueId           = $RestrictedSecurity->getUniqueKeyForRecord( $list[ 'ListName' ] );
            $records[$uniqueId] = $RestrictedSecurity;
        endforeach;

        $this->parseAndAddRecords( $this->ListName, $records );
    }


    /**
     * @param string $listName
     * @param RestrictedSecurity[] $Records
     * @return void
     */
    public function parseAndAddRecords( string $listName, array $Records ): void {

        /**
         * @var RestrictedSecurity $SecurityRecord
         */
        foreach ( $Records as $SecurityRecord ):
            $uniqueKey                   = $SecurityRecord->getUniqueKeyForRecord( $listName );
            $this->Records[ $uniqueKey ] = $SecurityRecord;
        endforeach;
    }


    /**
     * From ComplySci:
     * Unique Identifiers:
     * List items don’t have record IDs or primary keys.
     * The system identifies their uniqueness based on Security Identifier + List Name + Start Date.
     *
     * @param string $listName
     * @param RestrictedSecurity $RestrictedSecurity
     * @return string
     */
//    public function getUniqueKeyForRecord( string $listName, RestrictedSecurity $RestrictedSecurity ): string {
//        $securityId = $RestrictedSecurity->SecurityId;
//        $startDate  = $RestrictedSecurity->StartDate->toDateString();
//
//        return md5( $securityId . $listName . $startDate );
//    }

    public function numRecords(): int {
        return count( $this->Records );
    }

}