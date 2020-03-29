<?php

namespace App\TestFiles;

use App\Http\Validators\BaseValidator;

class TestListValidator extends BaseValidator
{
    /**
     * get rules for this request
     * @return array
     */
    public function getRules() : array
    {
        return [
            'class' => 'string|in:"asc","desc"',
            'fields' => 'string',
            'order' => 'string',
            'page' => 'integer|min:1',
            'filter_field' => 'string|filter',
            'filter_created' => 'string|filter',
            'filter_modified' => 'string|filter',
        ];
    }
}
