<?php

namespace App\Repositories;

use SimpleElasticsearch\SimpleElasticsearch;
use Ulid\Ulid;

abstract class BaseElasticRepository
{
    protected $index;
    protected $elastic;
    protected $ulid;

    /**
     * constructor
     * @param Ulid $ulid
     * @return void
     */
    public function __construct(
        Ulid $ulid
    ) {
        $config = $this->getConfig('elasticsearch');
        $this->elastic = $this->newSimpleElasticsearch(
            $config['host']
        );
        $this->ulid = $ulid;
    }

    /**
     * get index by id
     * @param string $id
     * @return array
     */
    public function getIndexById(
        string $id
    ): string {
        $query = [
            'bool' => [
                'must' => [
                    [
                        'term' => [
                            'id.keyword' => [
                                'value' => $id,
                                'boost' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $list = $this->elastic->searchDocuments(
            $this->index . '-*',
            $query
        );
        return $list['hits']['hits'][0]['_index'] ?? '';
    }

    /**
     * get by id
     * @param string $id
     * @return array
     */
    public function getById(
        string $id
    ): array {
        $query = [
            'bool' => [
                'must' => [
                    [
                        'bool' => [
                            'must_not' => [
                                'exists' => [
                                    'field' => 'deleted',
                                    'boost' => 1,
                                ],
                            ]
                        ]

                    ],
                    [
                        'term' => [
                            'id.keyword' => [
                                'value' => $id,
                                'boost' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $list = $this->elastic->searchDocuments(
            $this->index . '-*',
            $query
        );
        return $list['hits']['hits'][0]['_source'] ?? [];
    }

    /**
     * get dead by id
     * @param string $id
     * @return array
     */
    public function getDeadById(
        string $id
    ): array {
        $query = [
            'bool' => [
                'must' => [
                    [
                        'exists' => [
                            'field' => 'deleted',
                            'boost' => 1,
                        ],
                    ],
                    [
                        'term' => [
                            'id.keyword' => [
                                'value' => $id,
                                'boost' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $list = $this->elastic->searchDocuments(
            $this->index . '-*',
            $query
        );
        return $list['hits']['hits'][0]['_source'] ?? [];
    }

    /**
     * get data list
     * @param array $query
     * @return array
     */
    public function getList(
        array $query
    ): array {
        $elasticQuery = [
            'bool' => [
                'must_not' => [
                    [
                        'exists' => [
                            'field' => 'deleted',
                            'boost' => 1,
                        ],
                    ],
                ],
            ],
        ];
        $page = 1;
        if (isset($query['page'])) {
            $page = (int) $query['page'];
        }

        $list = $this->elastic->searchDocuments(
            $this->index . '-*',
            $elasticQuery,
            $page
        );
        $list = $this->decodeMultipleResults(
            $list
        );
        return $list;
    }

    /**
     * get aggregated data
     * @param array $query
     * @return array
     */
    public function getAggregate(
        array $aggregations,
        array $query
    ): array {
        $result = $this->elastic->aggregateDocuments(
            $this->index . '-*',
            $aggregations,
            $query
        );
        return $this->decodeAggregateResults(
            $result
        );
    }

    /**
     * get data dead list
     * @param array $query
     * @return array
     */
    public function getDeadList(
        array $query
    ): array {
        $elasticQuery = [
            'bool' => [
                'must' => [
                    [
                        'exists' => [
                            'field' => 'deleted',
                            'boost' => 1,
                        ],
                    ],
                ],
            ],
        ];
        $page = 1;
        if (isset($query['page'])) {
            $page = (int) $query['page'];
        }

        $list = $this->elastic->searchDocuments(
            $this->index . '-*',
            $elasticQuery,
            $page
        );
        $list = $this->decodeMultipleResults(
            $list
        );
        return $list;
    }

    /**
     * get bulk list
     * @param string $id
     * @param array $query
     * @return array
     */
    public function getBulk(
        array $ids,
        array $query
    ): array {
        $page = 1;
        if (isset($query['page'])) {
            $page = $query['page'];
        }
        $query = [
            'ids' => [
                'values' => $ids['*'],
            ],
        ];

        $list = $this->elastic->searchDocuments(
            $this->index . '-*',
            $query,
            $page
        );
        $list = $this->decodeMultipleResults(
            $list
        );
        return $list;
    }

    /**
     * insert data
     * @param array $data
     * @return string
     */
    public function insert(
        array $data,
        string $id = null
    ): string {
        if (!$id) {
            $id = $this->ulid->generate();
        }

        $data['id'] = $id;
        $data['created'] = date('Y-m-d H:i:s');
        $data['modified'] = date('Y-m-d H:i:s');

        $this->elastic->postDocument(
            $this->index . '-' . date('Y-m'),
            $data,
            $id
        );
        return $id;
    }

    /**
     * update data
     * @param array $data
     * @param string $id
     * @param string $exactIndex
     * @return bool
     */
    public function update(
        array $data,
        string $id,
        string $exactIndex
    ): bool {
        $data['modified'] = date('Y-m-d H:i:s');

        $this->elastic->postDocument(
            $exactIndex,
            $data,
            $id
        );
        return true;
    }

    /**
     * delete data
     * @param array $data
     * @param string $id
     * @param array $exactIndex
     * @return bool
     */
    public function delete(
        array $data,
        string $id,
        string $exactIndex
    ): bool {
        $data['modified'] = date('Y-m-d H:i:s');
        $data['deleted'] = date('Y-m-d H:i:s');

        $this->elastic->postDocument(
            $exactIndex,
            $data,
            $id
        );
        return true;
    }

    /**
     * decode multiple results
     * @param array $result
     * @return array
     */
    public function decodeMultipleResults(
        array $result
    ): array {
        $finalResult = [];
        foreach ($result['hits']['hits'] as $item) {
            $finalResult[] = $item['_source'];
        }
        return $finalResult;
    }

    /**
     * decode aggregate results
     * @param array $result
     * @return array
     */
    public function decodeAggregateResults(
        array $result
    ): array {
        $finalResult = [];
        $finalResult['total'] = $result['hits']['total']['value'] ?? 0;
        $finalResult['aggregations'] = $result['aggregations'] ?? [];
        return $finalResult;
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
