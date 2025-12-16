<?php

/**
 * This file contains the MariaDBDatabaseAccessObjectTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2019 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\MariaDB\Tests\Helpers;

use Lunr\Gravity\DatabaseStringEscaperInterface;
use Lunr\Gravity\MariaDB\MariaDBConnection;
use Lunr\Gravity\MariaDB\MariaDBDMLQueryBuilder;
use Lunr\Gravity\MariaDB\MariaDBSimpleDMLQueryBuilder;
use Lunr\Gravity\MySQL\MySQLQueryEscaper;
use Lunr\Gravity\MySQL\MySQLQueryResult;
use Lunr\Gravity\Tests\Helpers\DatabaseAccessObjectBaseTestCase;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use MySQLi;
use Psr\Log\LoggerInterface;

/**
 * This class contains setup and tear down methods for DAOs using MariaDB access.
 */
abstract class MariaDBDatabaseAccessObjectTestCase extends DatabaseAccessObjectBaseTestCase
{

    use MockeryPHPUnitIntegration;

    /**
     * Mock instance of the MariaDBConnection class.
     * @var MariaDBConnection&MockInterface
     */
    protected MariaDBConnection&MockInterface $db;

    /**
     * Mock instance of the Logger class
     * @var LoggerInterface&MockObject
     */
    protected LoggerInterface&MockObject $logger;

    /**
     * Real instance of the DMLQueryBuilder class
     * @var MariaDBDMLQueryBuilder
     */
    protected MariaDBDMLQueryBuilder $realBuilder;

    /**
     * Real instance of the SimpleDMLQueryBuilder class
     * @var MariaDBSimpleDMLQueryBuilder
     */
    protected MariaDBSimpleDMLQueryBuilder $realSimpleBuilder;

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

        $this->realBuilder = new MariaDBDMLQueryBuilder();
        $this->realEscaper = new MySQLQueryEscaper($mockEscaper);

        $this->realSimpleBuilder = new MariaDBSimpleDMLQueryBuilder($this->realBuilder, $this->realEscaper);

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

        $this->db = Mockery::mock(MariaDBConnection::class, [ $config, $this->logger, $mysqli ]);

        $this->result = Mockery::mock(MySQLQueryResult::class);

        $this->db->expects($this->once())
                 ->method('get_query_escaper_object')
                 ->willReturn($this->realEscaper);
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
