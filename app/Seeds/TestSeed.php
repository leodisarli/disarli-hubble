<?php

namespace App\Seeds;

class TestSeed
{
    /**
     * return seed
     * @return array
     */
    public function getSeed()
    {
        return [
            'index' => 'test',
            'fields' => [
                'name' => [
                    'type' => 'name',
                ],
                'year' => [
                    'type' => 'integer',
                    'min' => 2007,
                    'max' => 2020,
                ],
                'origin_sector' => [
                    'type' => 'list',
                    'values' => [
                        'sector1',
                        'sector2',
                        'sector3',
                        'sector4',
                        'sector5',
                    ]
                ],
                'visits' => [
                    'type' => 'integer',
                    'min' => 0,
                    'max' => 100,
                ],
            ],
        ];
    }
}
