<?php

namespace App\Domains\Health\Http\Controllers;

use App\Http\Controllers\BaseController;
use Exception;
use Illuminate\Database\DatabaseManager;
use SimpleElasticsearch\SimpleElasticsearch;

class HealthDbController extends BaseController
{
    private $databaseManager;
    private $elastic;

    /**
     * constructor
     * @param DatabaseManager $databaseManager
     * @param SimpleElasticsearch $SimpleElasticsearch
     * @return void
     */
    public function __construct(
        DatabaseManager $databaseManager,
        SimpleElasticsearch $elastic
    ) {
        $this->databaseManager = $databaseManager;
        $this->elastic = $elastic;
    }

    public function process()
    {
        $config = $this->getConfig('elasticsearch');
        $this->databaseManager->connection()->getPdo();
        $this->elastic = $this->newSimpleElasticsearch(
            $config['host']
        );

        $connected = $this->elastic->isConnected();

        if (!$connected) {
            throw new Exception(
                "Unable to connect with elastic",
                500
            );
        }
        return response()->json(
            [
                'status' => 'online',
                'version' => config('version.info'),
            ],
            200
        );
    }

    /**
     * @codeCoverageIgnore
     * create new SimpleElasticsearch instance
     * @return SimpleElasticsearch
     */
    public function newSimpleElasticsearch(
        string $host
    ): SimpleElasticsearch {
        return new SimpleElasticsearch($host);
    }

    /**
     * @codeCoverageIgnore
     * get lumen config
     * @param string $config
     * @return array
     */
    public function getConfig(
        string $config
    ): array {
        return config($config) ?? [];
    }
}
