<?php

/**
 * This file contains the MariaDBSimpleDMLQueryBuilderBaseTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2018 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\MariaDB\Tests;

use ReflectionClass;
use Lunr\Gravity\MySQL\MySQLQueryEscaper;
use Lunr\Gravity\MariaDB\MariaDBDMLQueryBuilder;
use Lunr\Gravity\MariaDB\Tests\MariaDBSimpleDMLQueryBuilderTest;

/**
 * This class contains basic tests for the MariaDBSimpleDMLQueryBuilder class.
 *
 * @covers Lunr\Gravity\MariaDB\MariaDBSimpleDMLQueryBuilder
 */
class MariaDBSimpleDMLQueryBuilderBaseTest extends MariaDBSimpleDMLQueryBuilderTest
{

    /**
     * Test the builder class is passed correctly.
     */
    public function testBuilderIsPassedCorrectly(): void
    {
        $instance = 'Lunr\Gravity\MariaDB\MariaDBDMLQueryBuilder';
        $this->assertInstanceOf($instance, $this->builder);
    }

    /**
     * Test the QueryEscaper class is passed correctly.
     */
    public function testEscaperIsPassedCorrectly(): void
    {
        $instance = 'Lunr\Gravity\MySQL\MySQLQueryEscaper';
        $this->assertInstanceOf($instance, $this->escaper);
    }

}

?>
