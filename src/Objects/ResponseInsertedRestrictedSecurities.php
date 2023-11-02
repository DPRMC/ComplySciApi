<?php

namespace DPRMC\ComplySciApi\Objects;


use Carbon\Carbon;


/**
 * This object contains just a little info about how the Insert operation was executed.
 */
class ResponseInsertedRestrictedSecurities {

    use DebugTrait;

    public readonly int $totalCount;
    public readonly int $insert;
    public readonly int $error;

    /**
     * @param array $arrayFromApi
     */
    public function __construct( array $arrayFromApi = [] ) {
        $this->totalCount = $arrayFromApi[ 'TotalCount' ];
        $this->insert     = $arrayFromApi[ 'Insert' ];
        $this->error      = $arrayFromApi[ 'Error' ];
    }


}