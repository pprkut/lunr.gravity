<?php

/**
 * This file contains the DatabaseConnectionPoolBaseTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\Tests;

use ArrayAccess;

/**
 * This class contains basic tests for the DatabaseConnectionPool class.
 *
 * @covers Lunr\Gravity\DatabaseConnectionPool
 */
class DatabaseConnectionPoolBaseTest extends DatabaseConnectionPoolTestCase
{

    /**
     * TestCase Constructor.
     */
    public function setUp(): void
    {
        $this->emptySetup();
    }

    /**
     * Test that the configuration was passed correctly.
     */
    public function testConfigurationPassedByReference(): void
    {
        $property = $this->poolReflection->getProperty('config');
        $property->setAccessible(TRUE);

        $value = $property->getValue($this->pool);

        $this->assertInstanceOf(ArrayAccess::class, $value);
        $this->assertSame($this->configuration, $value);
    }

    /**
     * Test that the Logger class was passed correctly.
     */
    public function testLoggerPassedByReference(): void
    {
        $property = $this->poolReflection->getProperty('logger');
        $property->setAccessible(TRUE);

        $value = $property->getValue($this->pool);

        $this->assertInstanceOf('Psr\Log\LoggerInterface', $value);
        $this->assertSame($this->logger, $value);
    }

    /**
     * Test that the roPool was setup correctly.
     */
    public function testReadonlyPoolSetupCorrectly(): void
    {
        $property = $this->poolReflection->getProperty('roPool');
        $property->setAccessible(TRUE);

        $value = $property->getValue($this->pool);

        $this->assertIsArray($value);
        $this->assertEmpty($value);
    }

    /**
     * Test that the rwPool was setup correctly.
     */
    public function testReadWritePoolSetupCorrectly(): void
    {
        $property = $this->poolReflection->getProperty('rwPool');
        $property->setAccessible(TRUE);

        $value = $property->getValue($this->pool);

        $this->assertIsArray($value);
        $this->assertEmpty($value);
    }

}

?>
