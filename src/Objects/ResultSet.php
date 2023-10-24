<?php

namespace DPRMC\ComplySciApi\Objects;


use Carbon\Carbon;


/**
 * This object contains the RestrictedLists in an array, and has some helper functions to count records in those Lists.
 */
class ResultSet {

    use DebugTrait;

    /**
     * @var RestrictedList[]
     */
    public array $Lists;


    /**
     * @param array $arrayFromApi
     */
    public function __construct( array $arrayFromApi = [] ) {

        if ( empty( $arrayFromApi ) ):
            return;
        endif;

        $lists = $arrayFromApi[ 'Lists' ];
        $this->_debug( "Found " . count( $lists ) . " lists in the response from the last request." );

        /**
         * $lists =>
         *   0 => array:10 [
         *     "MonitoringManagingGroups" => " Supervisors, Restricted List Supervision"
         *     "ListName" => "Restricted Securities List"
         *     "ListDescription" => "List updated through FTP"
         *     "CreatedBy" => "ComplySciDeerParkRD"
         *     "CreatedDate" => "2021-08-19T17:09:32.033"
         *     "LastModifiedBy" => "CSIAdmin"
         *     "LastModifiedDate" => "2021-10-12T20:56:21.957"
         *     "IsActive" => true
         *     "VisibleToGroups" => "All Employees"
         *     "Records" => array:100 [
         *         0 => array:31 [...
         */

        /**
         * @var array $list Format shown in the comment above
         */
        foreach ( $lists as $i => $list ):
            $listName = $list[ 'ListName' ];
            $RestrictedList = new RestrictedList( $list );
            $this->_debug( "List (" . $i . ") is " . $listName );
            if ( ! isset( $this->Lists[ $listName ] ) ):
                $this->_debug( "List doesn't exist yet, so adding it to the listsByName array." );
                $this->Lists[ $listName ] = $RestrictedList;
            else:
                $this->_debug( "List already existed, so adding another [" . count( $list[ 'Records' ] ) . "] Restricted Securities to my copy of that list in memory." );
                // @var RestrictedList $listsByListName[$listName]
                $this->Lists[ $listName ]->parseAndAddRecords( $listName, $RestrictedList->Records );
            endif;

            $this->_debug( "There are now this many records in that list: " . count( $this->Lists[ $listName ]->Records ) );
        endforeach; // End looping through potentially multiple lists returned in result set.
    }


    /**
     * @param ResultSet $ResultSet
     * @return $this
     */
    public function mergeResultSet( ResultSet $ResultSet ): ResultSet {
        /**
         * @var $RestrictedList $RestrictedList
         */
        foreach ( $ResultSet->Lists as $listName => $RestrictedList ):
            if ( ! isset( $this->Lists[ $listName ] ) ):
                $this->Lists[ $listName ] = $RestrictedList;
            else:
                $this->Lists[ $listName ]->parseAndAddRecords( $listName, $RestrictedList->Records );
            endif;
        endforeach;
        return $this;

    }


    /**
     * This method returns the number of Records in all the Lists held by this object.
     * @return int
     */
    public function numSecuritiesInAllLists(): int {
        $numSecuritiesInAllLists = 0;
        /**
         * @var $RestrictedList $RestrictedList
         */
        foreach ( $this->Lists as $listName => $RestrictedList ):
            $numSecuritiesInAllLists += $RestrictedList->numRecords();
        endforeach;

        return $numSecuritiesInAllLists;
    }


    /**
     * Given a list name, this method returns the number of Records in that List.
     * @param string $listName
     * @return int
     * @throws \Exception
     */
    public function numSecuritiesInList( string $listName ): int {
        if ( ! isset( $this->Lists[ $listName ] ) ):
            throw new \Exception( "The list named [$listName] does not exist in the Lists array." );
        endif;

        return $this->Lists[ $listName ]->numRecords();
    }


}