<?php

namespace DPRMC\ComplySciApi\Objects\InsertableObjects;


use Carbon\Carbon;

class InsertableRestrictedSecurity {

    public readonly string  $symbol;
    public readonly ?Carbon $startDate;
    public readonly string  $listName;
    public readonly string  $listAdministrator;
    public readonly array   $groups;
    public readonly array   $employees;


    public function __construct( string $symbol,
                                 Carbon $startDate,
                                 string $listName,
                                 string $listAdministrator,
                                 array  $groups = [],
                                 array  $employees = [] ) {
        $this->symbol            = $symbol;
        $this->startDate         = $startDate;
        $this->listName          = $listName;
        $this->listAdministrator = $listAdministrator;
        $this->groups            = $groups;
        $this->employees         = $employees;
    }


    public function getArrayToInsert(): array {
        return [
            'Symbol'            => $this->symbol,
//            'StartDate'         => $this->startDate->toISOString(),
            'StartDate'         => $this->startDate->toDateString(),
            'ListName'          => $this->listName,
            'ListAdministrator' => $this->listAdministrator,
            'Groups'            => $this->groups,
            'Employees'         => $this->employees,
        ];
    }


    /**
     * @param InsertableRestrictedSecurity[] $insertableRestrictedSecurities
     * @return array
     */
    public static function getArrayOfRestrictedSecuritiesToInsert( array $insertableRestrictedSecurities ): array {
        $array = [];
        foreach ( $insertableRestrictedSecurities as $insertableRestrictedSecurity ):
            $array[] = $insertableRestrictedSecurity->getArrayToInsert();
        endforeach;
        return $array;
    }


}