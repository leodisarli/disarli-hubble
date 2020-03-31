<?php

namespace App\Repositories;

use Mockery;
use PHPUnit\Framework\TestCase;
use Ulid\Ulid;

class BaseElasticRepositoryTest extends TestCase
{
    /**
     * @covers \App\Repositories\BaseElasticRepository::__construct
     */
    public function testCreateBaseElasticRepository()
    {
        $ulidSpy = Mockery::spy(Ulid::class);
        Mockery::spy(
            'overload:SimpleElasticsearch\SimpleElasticsearch'
        );

        $baseElasticRepository = $this->getMockForAbstractClass(
            BaseElasticRepository::class,
            [
                $ulidSpy
            ]
        );
        $this->assertInstanceOf(
            BaseElasticRepository::class,
            $baseElasticRepository
        );
    }

    /**
     * @covers \App\Repositories\BaseElasticRepository::getIndexById
     */
    public function testGetIndexById()
    {
        $id = '01E4DWNQ04V6CRWAKSZP76N4CR';
        $index = 'test-2020-03';
        $return = [
            'hits' => [
                'hits' => [
                    [
                        '_index' => $index,
                    ]
                ],
            ],
        ];

        $ulidSpy = Mockery::spy(Ulid::class);
        $elasticMock = Mockery::mock(
            'overload:SimpleElasticsearch\SimpleElasticsearch'
        );

        $elasticMock->shouldReceive('searchDocuments')
            ->once()
            ->andReturn($return)
            ->getMock();

        $baseElasticRepository = $this->getMockForAbstractClass(
            BaseElasticRepository::class,
            [
                $ulidSpy
            ]
        );

        $result = $baseElasticRepository->getIndexById(
            $id
        );

        $this->assertEquals(
            $index,
            $result
        );
    }

    /**
     * @covers \App\Repositories\BaseElasticRepository::getById
     */
    public function testGetById()
    {
        $id = '01E4DWNQ04V6CRWAKSZP76N4CR';
        $return = [
            'hits' => [
                'hits' => [
                    [
                        '_source' => [
                            'id' => $id,
                        ],
                    ],
                ],
            ],
        ];
        $expectation = [
            'id' => $id,
        ];

        $ulidSpy = Mockery::spy(Ulid::class);
        $elasticMock = Mockery::mock(
            'overload:SimpleElasticsearch\SimpleElasticsearch'
        );

        $elasticMock->shouldReceive('searchDocuments')
            ->once()
            ->andReturn($return)
            ->getMock();

        $baseElasticRepository = $this->getMockForAbstractClass(
            BaseElasticRepository::class,
            [
                $ulidSpy
            ]
        );

        $result = $baseElasticRepository->getById(
            $id
        );

        $this->assertEquals(
            $expectation,
            $result
        );
    }

    /**
     * @covers \App\Repositories\BaseElasticRepository::getDeadById
     */
    public function testGetDeadById()
    {
        $id = '01E4DWNQ04V6CRWAKSZP76N4CR';
        $return = [
            'hits' => [
                'hits' => [
                    [
                        '_source' => [
                            'id' => $id,
                        ],
                    ],
                ],
            ],
        ];
        $expectation = [
            'id' => $id,
        ];

        $ulidSpy = Mockery::spy(Ulid::class);
        $elasticMock = Mockery::mock(
            'overload:SimpleElasticsearch\SimpleElasticsearch'
        );

        $elasticMock->shouldReceive('searchDocuments')
            ->once()
            ->andReturn($return)
            ->getMock();

        $baseElasticRepository = $this->getMockForAbstractClass(
            BaseElasticRepository::class,
            [
                $ulidSpy
            ]
        );

        $result = $baseElasticRepository->getDeadById(
            $id
        );

        $this->assertEquals(
            $expectation,
            $result
        );
    }

    /**
     * @covers \App\Repositories\BaseElasticRepository::getList
     */
    public function testGetList()
    {
        $id = '01E4DWNQ04V6CRWAKSZP76N4CR';
        $id2 = '01E4DXXZXQ6K5E0ZXCBZJVVCSK';
        $return = [
            'hits' => [
                'hits' => [
                    [
                        '_source' => [
                            'id' => $id,
                        ],
                    ],
                    [
                        '_source' => [
                            'id' => $id2,
                        ],
                    ],
                ],
            ],
        ];
        $expectation = [
            [
                'id' => $id,
            ],
            [
                'id' => $id2,
            ],
        ];

        $ulidSpy = Mockery::spy(Ulid::class);
        $elasticMock = Mockery::mock(
            'overload:SimpleElasticsearch\SimpleElasticsearch'
        );

        $elasticMock->shouldReceive('searchDocuments')
            ->once()
            ->andReturn($return)
            ->getMock();

        $baseElasticRepository = $this->getMockForAbstractClass(
            BaseElasticRepository::class,
            [
                $ulidSpy
            ]
        );

        $result = $baseElasticRepository->getList(
            [
                'page' => 2,
            ]
        );

        $this->assertEquals(
            $expectation,
            $result
        );
    }

    /**
     * @covers \App\Repositories\BaseElasticRepository::getAggregate
     */
    public function testGetAggregate()
    {
        $return = [
            'hits' => [
                'total' => [
                    'value' => 16,
                ],
            ],
            'aggregations' => [
                'total_visits' => [
                    'value' => 80,
                ],
            ],
        ];
        $expectation = [
            'total' => 16,
            'aggregations' => [
                'total_visits' => [
                    'value' => 80,
                ],
            ],
        ];

        $ulidSpy = Mockery::spy(Ulid::class);
        $elasticMock = Mockery::mock(
            'overload:SimpleElasticsearch\SimpleElasticsearch'
        );

        $elasticMock->shouldReceive('aggregateDocuments')
            ->once()
            ->andReturn($return)
            ->getMock();

        $baseElasticRepository = $this->getMockForAbstractClass(
            BaseElasticRepository::class,
            [
                $ulidSpy
            ]
        );

        $result = $baseElasticRepository->getAggregate(
            [],
            []
        );

        $this->assertEquals(
            $expectation,
            $result
        );
    }

    /**
     * @covers \App\Repositories\BaseElasticRepository::getDeadList
     */
    public function testGetDeadList()
    {
        $id = '01E4DWNQ04V6CRWAKSZP76N4CR';
        $id2 = '01E4DXXZXQ6K5E0ZXCBZJVVCSK';
        $return = [
            'hits' => [
                'hits' => [
                    [
                        '_source' => [
                            'id' => $id,
                        ],
                    ],
                    [
                        '_source' => [
                            'id' => $id2,
                        ],
                    ],
                ],
            ],
        ];
        $expectation = [
            [
                'id' => $id,
            ],
            [
                'id' => $id2,
            ],
        ];

        $ulidSpy = Mockery::spy(Ulid::class);
        $elasticMock = Mockery::mock(
            'overload:SimpleElasticsearch\SimpleElasticsearch'
        );

        $elasticMock->shouldReceive('searchDocuments')
            ->once()
            ->andReturn($return)
            ->getMock();

        $baseElasticRepository = $this->getMockForAbstractClass(
            BaseElasticRepository::class,
            [
                $ulidSpy
            ]
        );

        $result = $baseElasticRepository->getDeadList(
            [
                'page' => 2
            ]
        );

        $this->assertEquals(
            $expectation,
            $result
        );
    }

    /**
     * @covers \App\Repositories\BaseElasticRepository::getBulk
     */
    public function testGetBulk()
    {
        $id = '01E4DWNQ04V6CRWAKSZP76N4CR';
        $id2 = '01E4DXXZXQ6K5E0ZXCBZJVVCSK';
        $return = [
            'hits' => [
                'hits' => [
                    [
                        '_source' => [
                            'id' => $id,
                        ],
                    ],
                    [
                        '_source' => [
                            'id' => $id2,
                        ],
                    ],
                ],
            ],
        ];
        $expectation = [
            [
                'id' => $id,
            ],
            [
                'id' => $id2,
            ],
        ];

        $ulidSpy = Mockery::spy(Ulid::class);
        $elasticMock = Mockery::mock(
            'overload:SimpleElasticsearch\SimpleElasticsearch'
        );

        $elasticMock->shouldReceive('searchDocuments')
            ->once()
            ->andReturn($return)
            ->getMock();

        $baseElasticRepository = $this->getMockForAbstractClass(
            BaseElasticRepository::class,
            [
                $ulidSpy
            ]
        );

        $result = $baseElasticRepository->getBulk(
            [
                '*' => [
                    $id,
                    $id2,
                ]
            ],
            [
                'page' => 2,
            ]
        );

        $this->assertEquals(
            $expectation,
            $result
        );
    }

    /**
     * @covers \App\Repositories\BaseElasticRepository::insert
     */
    public function testInsertWithDate()
    {
        $id = '01E4DWNQ04V6CRWAKSZP76N4CR';
        $data = [
            'id' => $id,
            'created' => '2020-01-05 23:34:11',
        ];

        $ulidMock = Mockery::mock(Ulid::class);
        $ulidMock->shouldReceive('generate')
            ->once()
            ->andReturn($id);

        $elasticMock = Mockery::mock(
            'overload:SimpleElasticsearch\SimpleElasticsearch'
        );

        $elasticMock->shouldReceive('postDocument')
            ->once()
            ->andReturn(true);

        $baseElasticRepository = $this->getMockForAbstractClass(
            BaseElasticRepository::class,
            [
                $ulidMock
            ]
        );

        $result = $baseElasticRepository->insert(
            $data
        );

        $this->assertEquals(
            $id,
            $result
        );
    }

    /**
     * @covers \App\Repositories\BaseElasticRepository::insert
     */
    public function testInsert()
    {
        $id = '01E4DWNQ04V6CRWAKSZP76N4CR';
        $data = [
            'id' => $id,
        ];

        $ulidMock = Mockery::mock(Ulid::class);
        $ulidMock->shouldReceive('generate')
            ->once()
            ->andReturn($id);

        $elasticMock = Mockery::mock(
            'overload:SimpleElasticsearch\SimpleElasticsearch'
        );

        $elasticMock->shouldReceive('postDocument')
            ->once()
            ->andReturn(true);

        $baseElasticRepository = $this->getMockForAbstractClass(
            BaseElasticRepository::class,
            [
                $ulidMock
            ]
        );

        $result = $baseElasticRepository->insert(
            $data
        );

        $this->assertEquals(
            $id,
            $result
        );
    }

    /**
     * @covers \App\Repositories\BaseElasticRepository::update
     */
    public function testUpdate()
    {
        $id = '01E4DWNQ04V6CRWAKSZP76N4CR';
        $data = [
            'id' => $id,
        ];
        $index = 'test-2020-03';

        $ulidSpy = Mockery::spy(Ulid::class);
        $elasticMock = Mockery::mock(
            'overload:SimpleElasticsearch\SimpleElasticsearch'
        );

        $elasticMock->shouldReceive('postDocument')
            ->once()
            ->andReturn(true);

        $baseElasticRepository = $this->getMockForAbstractClass(
            BaseElasticRepository::class,
            [
                $ulidSpy
            ]
        );

        $result = $baseElasticRepository->update(
            $data,
            $id,
            $index
        );

        $this->assertTrue(
            $result
        );
    }

    /**
     * @covers \App\Repositories\BaseElasticRepository::delete
     */
    public function testDelete()
    {
        $id = '01E4DWNQ04V6CRWAKSZP76N4CR';
        $data = [
            'id' => $id,
        ];
        $index = 'test-2020-03';

        $ulidSpy = Mockery::spy(Ulid::class);
        $elasticMock = Mockery::mock(
            'overload:SimpleElasticsearch\SimpleElasticsearch'
        );

        $elasticMock->shouldReceive('postDocument')
            ->once()
            ->andReturn(true);

        $baseElasticRepository = $this->getMockForAbstractClass(
            BaseElasticRepository::class,
            [
                $ulidSpy
            ]
        );

        $result = $baseElasticRepository->delete(
            $data,
            $id,
            $index
        );

        $this->assertTrue(
            $result
        );
    }

    /**
     * @covers \App\Repositories\BaseElasticRepository::decodeMultipleResults
     */
    public function testDecodeMultipleResults()
    {
        $id = '01E4DWNQ04V6CRWAKSZP76N4CR';
        $id2 = '01E4DXXZXQ6K5E0ZXCBZJVVCSK';

        $return = [
            'hits' => [
                'hits' => [
                    [
                        '_source' => [
                            'id' => $id,
                        ],
                    ],
                    [
                        '_source' => [
                            'id' => $id2,
                        ],
                    ],
                ],
            ],
        ];
        $expectation = [
            [
                'id' => $id,
            ],
            [
                'id' => $id2,
            ],
        ];

        $ulidSpy = Mockery::spy(Ulid::class);
        Mockery::spy(
            'overload:SimpleElasticsearch\SimpleElasticsearch'
        );

        $baseElasticRepository = $this->getMockForAbstractClass(
            BaseElasticRepository::class,
            [
                $ulidSpy
            ]
        );

        $decodeMultipleResults = $baseElasticRepository->decodeMultipleResults(
            $return
        );

        $this->assertEquals(
            $expectation,
            $decodeMultipleResults
        );
    }

    /**
     * @covers \App\Repositories\BaseElasticRepository::decodeAggregateResults
     */
    public function testDecodeAggregateResults()
    {
        $return = [
            'hits' => [
                'total' => [
                    'value' => 16,
                ],
            ],
            'aggregations' => [
                'total_visits' => [
                    'value' => 80,
                ],
            ],
        ];
        $expectation = [
            'total' => 16,
            'aggregations' => [
                'total_visits' => [
                    'value' => 80,
                ],
            ],
        ];

        $ulidSpy = Mockery::spy(Ulid::class);
        $elasticMock = Mockery::mock(
            'overload:SimpleElasticsearch\SimpleElasticsearch'
        );

        $baseElasticRepository = $this->getMockForAbstractClass(
            BaseElasticRepository::class,
            [
                $ulidSpy
            ]
        );

        $result = $baseElasticRepository->decodeAggregateResults(
            $return
        );

        $this->assertEquals(
            $expectation,
            $result
        );
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
