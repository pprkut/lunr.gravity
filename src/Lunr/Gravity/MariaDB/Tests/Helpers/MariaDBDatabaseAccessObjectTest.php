<?php

/**
 * This file contains the MariaDBDatabaseAccessObjectTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2019 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\MariaDB\Tests\Helpers;

use Lunr\Halo\LunrBaseTest;
use ReflectionClass;

/**
 * This class contains setup and tear down methods for DAOs using MariaDB access.
 */
abstract class MariaDBDatabaseAccessObjectTest extends LunrBaseTest
{

    /**
     * Mock instance of the MariaDBConnection class.
     * @var \Lunr\Gravity\MariaDB\MariaDBConnection
     */
    protected $db;

    /**
     * Mock instance of the Logger class
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Mock instance of the DMLQueryBuilder class
     * @var \Lunr\Gravity\MariaDB\MariaDBDMLQueryBuilder
     */
    protected $builder;

    /**
     * Mock instance of the QueryEscaper class
     * @var \Lunr\Gravity\MySQL\MySQLQueryEscaper
     */
    protected $escaper;

    /**
     * Mock instance of the QueryResult class
     * @var \Lunr\Gravity\MySQL\MySQLQueryResult
     */
    protected $result;

    /**
     * Testcase Constructor.
     */
    public function setUp(): void
    {
        $this->db = $this->getMockBuilder('Lunr\Gravity\MariaDB\MariaDBConnection')
                         ->disableOriginalConstructor()
                         ->getMock();

        $this->builder = $this->getMockBuilder('Lunr\Gravity\MariaDB\MariaDBDMLQueryBuilder')
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->escaper = $this->getMockBuilder('Lunr\Gravity\MySQL\MySQLQueryEscaper')
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->result = $this->getMockBuilder('Lunr\Gravity\MySQL\MySQLQueryResult')
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();

        $this->db->expects($this->once())
                 ->method('get_query_escaper_object')
                 ->will($this->returnValue($this->escaper));
    }

    /**
     * Testcase Destructor.
     */
    public function tearDown(): void
    {
        unset($this->db);
        unset($this->logger);
        unset($this->builder);
        unset($this->escaper);
        unset($this->result);

        parent::tearDown();
    }

}

?>
