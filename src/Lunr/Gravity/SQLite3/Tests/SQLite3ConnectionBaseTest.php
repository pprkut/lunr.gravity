<?php

/**
 * This file contains the SQLite3ConnectionBaseTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2013 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\SQLite3\Tests;

/**
 * This class contains basic tests for the SQLite3Connection class.
 *
 * @covers Lunr\Gravity\SQLite3\SQLite3Connection
 */
class SQLite3ConnectionBaseTest extends SQLite3ConnectionTestCase
{

    /**
     * Test that the Configuration class is passed by reference.
     */
    public function testConfigurationIsPassedByReference(): void
    {
        $this->assertPropertySame('config', $this->configuration);
    }

    /**
     * Test that the SQLite3 class was passed correctly.
     */
    public function testSQLite3Passed(): void
    {
        $value = $this->getReflectionPropertyValue('sqlite3');

        $this->assertInstanceOf('Lunr\Gravity\SQLite3\LunrSQLite3', $value);
    }

    /**
     * Test that by default we don't have a QueryEscaper instance.
     */
    public function testEscaperIsUnset(): void
    {
        $this->assertPropertyUnset('escaper');
    }

    /**
     * Test that database is set correctly.
     */
    public function testDatabaseIsSetCorrectly(): void
    {
        $this->assertEquals('/tmp/test.db', $this->getReflectionPropertyValue('db'));
    }

    /**
     * Test that get_new_dml_query_builder_object() returns a new object.
     *
     * @covers Lunr\Gravity\SQLite3\SQLite3Connection::get_new_dml_query_builder_object
     */
    public function testGetNewDMLQueryBuilderObjectReturnsObject(): void
    {
        $value = $this->class->get_new_dml_query_builder_object();

        $this->assertInstanceOf('Lunr\Gravity\DatabaseDMLQueryBuilder', $value);
        $this->assertInstanceOf('Lunr\Gravity\SQLite3\SQLite3DMLQueryBuilder', $value);
    }

    /**
     * Test that get_query_escaper_object() returns a new object.
     *
     * @covers Lunr\Gravity\SQLite3\SQLite3Connection::get_query_escaper_object
     */
    public function testGetQueryEscaperObjectReturnsObject(): void
    {
        $value = $this->class->get_query_escaper_object();

        $this->assertInstanceOf('Lunr\Gravity\DatabaseQueryEscaper', $value);
        $this->assertInstanceOf('Lunr\Gravity\SQLite3\SQLite3QueryEscaper', $value);
    }

    /**
     * Test that get_query_escaper_object() returns a new object.
     *
     * @covers Lunr\Gravity\SQLite3\SQLite3Connection::get_query_escaper_object
     */
    public function testGetQueryEscaperObjectCachesObject(): void
    {
        $this->assertPropertyUnset('escaper');

        $this->class->get_query_escaper_object();

        $property = $this->getReflectionProperty('escaper');
        $instance = 'Lunr\Gravity\SQLite3\SQLite3QueryEscaper';
        $this->assertInstanceOf($instance, $property->getValue($this->class));
    }

    /**
     * Test that get_query_escaper_object() returns a new object.
     *
     * @covers Lunr\Gravity\SQLite3\SQLite3Connection::get_query_escaper_object
     */
    public function testGetQueryEscaperObjectReturnsCachedObject(): void
    {
        $value1 = $this->class->get_query_escaper_object();
        $value2 = $this->class->get_query_escaper_object();

        $this->assertInstanceOf('Lunr\Gravity\SQLite3\SQLite3QueryEscaper', $value1);
        $this->assertSame($value1, $value2);
    }

    /**
     * Test that get_database() returns the database name.
     *
     * @covers Lunr\Gravity\SQLite3\SQLite3Connection::get_database
     */
    public function testGetDatabase(): void
    {
        $value = $this->class->get_database();

        $this->assertSame('/tmp/test.db', $value);
    }

}

?>
