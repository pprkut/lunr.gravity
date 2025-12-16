<?php

/**
 * This file contains the MySQLDatabaseAccessObjectTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2014 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\MySQL\Tests\Helpers;

use Lunr\Gravity\DatabaseStringEscaperInterface;
use Lunr\Gravity\MySQL\MySQLConnection;
use Lunr\Gravity\MySQL\MySQLDMLQueryBuilder;
use Lunr\Gravity\MySQL\MySQLQueryEscaper;
use Lunr\Gravity\MySQL\MySQLQueryResult;
use Lunr\Gravity\MySQL\MySQLSimpleDMLQueryBuilder;
use Lunr\Gravity\Tests\Helpers\DatabaseAccessObjectBaseTestCase;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use MySQLi;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

/**
 * This class contains setup and tear down methods for DAOs using MySQL access.
 */
abstract class MySQLDatabaseAccessObjectTestCase extends DatabaseAccessObjectBaseTestCase
{

    use MockeryPHPUnitIntegration;

    /**
     * Mock instance of the MySQLConnection class.
     * @var MySQLConnection&MockInterface
     */
    protected MySQLConnection&MockInterface $db;

    /**
     * Mock instance of the Logger class
     * @var LoggerInterface&MockObject
     */
    protected LoggerInterface&MockObject $logger;

    /**
     * Real instance of the DMLQueryBuilder class
     * @var MySQLDMLQueryBuilder
     */
    protected MySQLDMLQueryBuilder $realBuilder;

    /**
     * Real instance of the SimpleDMLQueryBuilder class
     * @var MySQLSimpleDMLQueryBuilder
     */
    protected MySQLSimpleDMLQueryBuilder $realSimpleBuilder;

    /**
     * Real instance of the QueryEscaper class
     * @var MySQLQueryEscaper
     */
    protected MySQLQueryEscaper $realEscaper;

    /**
     * Mock instance of the QueryResult class
     * @var MySQLQueryResult&MockInterface
     */
    protected MySQLQueryResult&MockInterface $result;

    /**
     * Testcase Constructor.
     */
    public function setUp(): void
    {
        $mockEscaper = $this->getMockBuilder(DatabaseStringEscaperInterface::class)
                            ->getMock();

        $mockEscaper->expects($this->any())
                    ->method('escape_string')
                    ->willReturnArgument(0);

        $this->realBuilder = new MySQLDMLQueryBuilder();
        $this->realEscaper = new MySQLQueryEscaper($mockEscaper);

        $this->realSimpleBuilder = new MySQLSimpleDMLQueryBuilder($this->realBuilder, $this->realEscaper);

        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $mysqli = $this->getMockBuilder(MySQLi::class)
                       ->disableOriginalConstructor()
                       ->getMock();

        $config = [
            'rwHost'   => 'localhost',
            'username' => 'user',
            'password' => 'pass',
            'database' => 'db',
            'driver'   => 'mysql',
        ];

        $this->db = Mockery::mock(MySQLConnection::class, [ $config, $this->logger, $mysqli ]);

        $this->result = Mockery::mock(MySQLQueryResult::class);

        $this->db->shouldReceive('get_query_escaper_object')
                 ->once()
                 ->andReturn($this->realEscaper);
    }

    /**
     * Testcase Destructor.
     */
    public function tearDown(): void
    {
        unset($this->db);
        unset($this->logger);
        unset($this->result);
        unset($this->realEscaper);
        unset($this->realBuilder);
        unset($this->realSimpleBuilder);

        parent::tearDown();
    }

}

?>
