<?php

/**
 * This file contains the TransactionalMariaDBAccessObjectTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2023 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\MariaDB\Tests;

use Lunr\Gravity\MariaDB\TransactionalMariaDBAccessObject;
use Lunr\Gravity\MariaDB\MariaDBConnection;
use Lunr\Gravity\MySQL\MySQLQueryEscaper;
use Lunr\Halo\LunrBaseTest;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use ReflectionClass;

/**
 * This class contains the tests for the TransactionalMariaDBAccessObject class.
 *
 * @covers Lunr\Gravity\MariaDB\TransactionalMariaDBAccessObject
 */
abstract class TransactionalMariaDBAccessObjectTest extends LunrBaseTest
{

    /**
     * Mock instance of a MariaDBConnection
     * @var MariaDBConnection&MockObject&Stub
     */
    protected MariaDBConnection $db;

    /**
     * Mock instance of the Logger class.
     * @var LoggerInterface&MockObject&Stub
     */
    protected LoggerInterface $logger;

    /**
     * Instance of the tested class.
     * @var TransactionalMariaDBAccessObject&MockObject&Stub
     */
    protected TransactionalMariaDBAccessObject&MockObject&Stub $class;

    /**
     * Testcase Constructor.
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

        $this->db = $this->getMockBuilder(MariaDBConnection::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $escaper = $this->getMockBuilder(MySQLQueryEscaper::class)
                        ->disableOriginalConstructor()
                        ->getMock();

        $this->db->expects($this->once())
                 ->method('get_query_escaper_object')
                 ->willReturn($escaper);

        $this->class = $this->getMockBuilder(TransactionalMariaDBAccessObject::class)
                            ->setConstructorArgs([ $this->db, $this->logger ])
                            ->getMockForAbstractClass();

        parent::baseSetUp($this->class);
    }

    /**
     * Testcase Destructor.
     */
    public function tearDown(): void
    {
        unset($this->db);
        unset($this->logger);
        unset($this->class);

        parent::tearDown();
    }

}

?>
