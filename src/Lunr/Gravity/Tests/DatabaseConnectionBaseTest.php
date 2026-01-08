<?php

/**
 * This file contains the DatabaseConnectionBaseTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\Tests;

use Lunr\Halo\PropertyTraits\PsrLoggerTestTrait;

/**
 * This class contains the tests for the DatabaseConnection class.
 *
 * @covers Lunr\Gravity\DatabaseConnection
 */
class DatabaseConnectionBaseTest extends DatabaseConnectionTestCase
{

    use PsrLoggerTestTrait;

    /**
     * Test that the connected flag is set to FALSE by default.
     */
    public function testConnectedIsFalse(): void
    {
        $this->assertFalse($this->getReflectionPropertyValue('connected'));
    }

    /**
     * Test that the readonly flag is set to TRUE by default.
     */
    public function testReadonlyIsFalseByDefault(): void
    {
        $this->assertFalse($this->getReflectionPropertyValue('readonly'));
    }

    /**
     * Test that set_readonly sets the readonly flag when passed TRUE.
     *
     * @depends testReadonlyIsFalseByDefault
     * @covers  Lunr\Gravity\DatabaseConnection::set_readonly
     */
    public function testSetReadonlySetsReadonlyWhenPassedTrue(): void
    {
        $this->class->set_readonly(TRUE);

        $this->assertTrue($this->getReflectionPropertyValue('readonly'));
    }

    /**
     * Test that set_readonly unsets the readonly flag when passed FALSE.
     *
     * @depends testSetReadonlySetsReadonlyWhenPassedTrue
     * @covers  Lunr\Gravity\DatabaseConnection::set_readonly
     */
    public function testSetReadonlySetsReadwriteWhenPassedFalse(): void
    {
        $this->class->set_readonly(TRUE);

        $this->assertTrue($this->getReflectionPropertyValue('readonly'));

        $this->class->set_readonly(FALSE);

        $this->assertFalse($this->getReflectionPropertyValue('readonly'));
    }

}

?>
