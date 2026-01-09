<?php

/**
 * This file contains the DatabaseConnectionPoolTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\Tests;

use Lunr\Core\Configuration;
use Lunr\Gravity\DatabaseConnectionPool;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * This class contains common constructors/destructors and data providers
 * for testing the DatabaseConnectionPool class.
 *
 * @covers Lunr\Gravity\DatabaseConnectionPool
 */
abstract class DatabaseConnectionPoolTestCase extends TestCase
{

    /**
     * Instance of the DatabaseConnectionPool class.
     * @var DatabaseConnectionPool
     */
    protected $pool;

    /**
     * Reflection instance of the DatabaseConnectionPool class.
     * @var ReflectionClass
     */
    protected $poolReflection;

    /**
     * Mock instance of the Configuration class.
     * @var Configuration&MockObject
     */
    protected Configuration&MockObject $configuration;

    /**
     * Mock instance of a Logger class.
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * TestCase Constructor.
     *
     * @return void
     */
    public function emptySetup(): void
    {
        $this->configuration = $this->getMockBuilder('Lunr\Core\Configuration')->getMock();

        $this->logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();

        $this->pool = new DatabaseConnectionPool($this->configuration, $this->logger);

        $this->poolReflection = new ReflectionClass('Lunr\Gravity\DatabaseConnectionPool');
    }

    /**
     * TestCase Constructor.
     *
     * @return void
     */
    public function unsupportedSetup(): void
    {
        $this->configuration = $this->getMockBuilder('Lunr\Core\Configuration')->getMock();

        $map = [
            [ 'rw_host', 'rw_host' ],
            [ 'username', 'username' ],
            [ 'password', 'password' ],
            [ 'database', 'database' ],
            [ 'driver', 'unsupported' ],
        ];

        $this->configuration->expects($this->any())
                            ->method('offsetGet')
                            ->willReturnMap($map);

        $this->logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();

        $this->pool = new DatabaseConnectionPool($this->configuration, $this->logger);

        $this->poolReflection = new ReflectionClass('Lunr\Gravity\DatabaseConnectionPool');
    }

    /**
     * TestCase Constructor.
     *
     * @return void
     */
    public function supportedSetup(): void
    {
        $this->configuration = $this->getMockBuilder('Lunr\Core\Configuration')->getMock();

        $map = [
            [ 'rw_host', 'rw_host' ],
            [ 'username', 'username' ],
            [ 'password', 'password' ],
            [ 'database', 'database' ],
            [ 'driver', 'mysql' ],
        ];

        $this->configuration->expects($this->any())
                            ->method('offsetGet')
                            ->willReturnMap($map);

        $this->logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();

        $this->pool = new DatabaseConnectionPool($this->configuration, $this->logger);

        $this->poolReflection = new ReflectionClass('Lunr\Gravity\DatabaseConnectionPool');
    }

    /**
     * TestCase Destructor.
     */
    public function tearDown(): void
    {
        unset($this->pool);
        unset($this->poolReflection);
        unset($this->configuration);
        unset($this->logger);
    }

}

?>
