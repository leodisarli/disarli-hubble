<?php

$router->get(
    '/',
    [
        'uses' => 'HealthApiController@process',
    ]
);

$router->get(
    '/health/api',
    [
        'uses' => 'HealthApiController@process',
    ]
);

$router->get(
    '/health/db',
    [
        'uses' => 'HealthDbController@process',
    ]
);

$router->get(
    '/health/key',
    function () {
        return \Illuminate\Support\Str::random(32);
    }
);
