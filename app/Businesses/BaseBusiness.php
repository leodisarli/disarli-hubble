<?php

namespace App\Businesses;

abstract class BaseBusiness
{
    /**
     * decodeJsonFields
     * @param array $data
     * @param array $fields
     * @return array
     */
    public function decodeJsonFields(
        array $data,
        array $fields
    ): array {
        foreach ($fields as $field) {
            $data[$field] = json_decode($data[$field], true) ?? [];
        }
        return $data;
    }
}
