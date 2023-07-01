<?php

/**
 * This file contains the MariaDBConnectionBaseTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2018 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\MariaDB\Tests;

use Lunr\Gravity\MariaDB\Tests\MariaDBConnectionTest;

/**
 * This class contains basic tests for the MariaDBConnection class
 *
 * @covers Lunr\Gravity\MariaDB\MariaDBConnection
 */
class MariaDBConnectionBaseTest extends MariaDBConnectionTest
{

    /**
     * Test getting a new DMLQueryBuilder.
     *
     * @covers Lunr\Gravity\MariaDB\MariaDBConnection::get_new_dml_query_builder_object
     */
    public function testGetDMLQueryBuilder(): void
    {
        $querybuilder = $this->class->get_new_dml_query_builder_object(FALSE);

        $instance = 'Lunr\Gravity\MariaDB\MariaDBDMLQueryBuilder';
        $this->assertInstanceOf($instance, $querybuilder);
    }

    /**
     * Test getting a new DMLQueryBuilder.
     *
     * @covers Lunr\Gravity\MariaDB\MariaDBConnection::get_new_dml_query_builder_object
     */
    public function testGetSimpleDMLQueryBuilder(): void
    {
        $querybuilder = $this->class->get_new_dml_query_builder_object(TRUE);

        $instance = 'Lunr\Gravity\MariaDB\MariaDBSimpleDMLQueryBuilder';
        $this->assertInstanceOf($instance, $querybuilder);
    }

}

?>
