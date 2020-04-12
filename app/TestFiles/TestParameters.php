<?php

namespace App\TestFiles;

use App\Http\Parameters\BaseParameters;

class TestParameters extends BaseParameters
{
    /**
     * set allowed fields for this domain
     */
    public $fields = [
        'id',
        'field',
        'created',
        'modified',
        'deleted',
    ];

    /**
     * set allowed orders for this domain
     */
    public $order = [
        'id',
        'field',
        'created',
        'modified',
        'deleted',
    ];
}
