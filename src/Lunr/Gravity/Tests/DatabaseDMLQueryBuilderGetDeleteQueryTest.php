<?php

/**
 * This file contains the DatabaseDMLQueryBuilderGetDeleteQueryTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\Tests;

/**
 * This class contains the tests for the setup and the final query creation.
 *
 * @covers Lunr\Gravity\DatabaseDMLQueryBuilder
 */
class DatabaseDMLQueryBuilderGetDeleteQueryTest extends DatabaseDMLQueryBuilderTest
{

    /**
     * Test getting a delete query.
     *
     * @depends Lunr\Gravity\Tests\DatabaseDMLQueryBuilderImplodeQueryTest::testImplodeQueryWithDuplicateDeleteModes
     * @covers  Lunr\Gravity\DatabaseDMLQueryBuilder::get_delete_query
     */
    public function testGetDeleteQuery(): void
    {
        $this->set_reflection_property_value('from', 'FROM table');
        $this->set_reflection_property_value('delete_mode', [ 'QUICK', 'IGNORE' ]);
        $this->set_reflection_property_value('delete', 'table.*');
        $this->set_reflection_property_value('join', 'INNER JOIN table1');
        $this->set_reflection_property_value('where', 'WHERE a = b');

        $string = 'DELETE QUICK IGNORE table.* FROM table INNER JOIN table1 WHERE a = b';

        $this->assertEquals($string, $this->class->get_delete_query());
    }

    /**
     * Test getting a delete query with undefined FROM.
     *
     * @covers Lunr\Gravity\DatabaseDMLQueryBuilder::get_delete_query
     */
    public function testGetDeleteQueryWithUndefinedFrom(): void
    {
        $this->expectException('\Lunr\Gravity\Exceptions\MissingTableReferenceException');
        $this->expectExceptionMessage('No from() in delete query!');

        $this->set_reflection_property_value('delete_mode', [ 'QUICK', 'IGNORE' ]);
        $this->set_reflection_property_value('delete', 'table.*');

        $this->class->get_delete_query();
    }

    /**
     * Test getting a delete query with empty selection.
     *
     * @depends Lunr\Gravity\Tests\DatabaseDMLQueryBuilderImplodeQueryTest::testImplodeQueryWithDuplicateDeleteModes
     * @covers  Lunr\Gravity\DatabaseDMLQueryBuilder::get_delete_query
     */
    public function testGetEmptyDeleteQuery(): void
    {
        $this->set_reflection_property_value('from', 'FROM table');
        $this->set_reflection_property_value('delete_mode', [ 'QUICK', 'IGNORE' ]);

        $string = 'DELETE QUICK IGNORE FROM table';

        $this->assertEquals($string, $this->class->get_delete_query());
    }

    /**
     * Test getting a delete query with limit and orderBy.
     *
     * @depends Lunr\Gravity\Tests\DatabaseDMLQueryBuilderImplodeQueryTest::testImplodeQueryWithDuplicateDeleteModes
     * @covers  Lunr\Gravity\DatabaseDMLQueryBuilder::get_delete_query
     */
    public function testGetEmptyDeleteLimitOrderQuery(): void
    {
        $this->set_reflection_property_value('from', 'FROM table');
        $this->set_reflection_property_value('limit', 'LIMIT 10 OFFSET 0');
        $this->set_reflection_property_value('order_by', 'ORDER BY col ASC');

        $string = 'DELETE FROM table ORDER BY col ASC LIMIT 10 OFFSET 0';

        $this->assertEquals($string, $this->class->get_delete_query());
    }

    /**
     * Test it is not possible to get a delete query with limit and orderBy when delete is not ''.
     *
     * @depends Lunr\Gravity\Tests\DatabaseDMLQueryBuilderImplodeQueryTest::testImplodeQueryWithDuplicateDeleteModes
     * @covers  Lunr\Gravity\DatabaseDMLQueryBuilder::get_delete_query
     */
    public function testGetDeleteLimitOrderQuery(): void
    {
        $this->set_reflection_property_value('delete', 'table.*');
        $this->set_reflection_property_value('from', 'FROM table');
        $this->set_reflection_property_value('limit', 'LIMIT 10 OFFSET 0');
        $this->set_reflection_property_value('order_by', 'ORDER BY col ASC');

        $string = 'DELETE table.* FROM table';

        $this->assertEquals($string, $this->class->get_delete_query());
    }

    /**
     * Test it is not possible to get a delete query with limit and orderBy when join is not ''.
     *
     * @depends Lunr\Gravity\Tests\DatabaseDMLQueryBuilderImplodeQueryTest::testImplodeQueryWithDuplicateDeleteModes
     * @covers  Lunr\Gravity\DatabaseDMLQueryBuilder::get_delete_query
     */
    public function testGetDeleteLimitOrderQueryWithJoin(): void
    {
        $this->set_reflection_property_value('join', 'INNER JOIN table1');
        $this->set_reflection_property_value('from', 'FROM table');
        $this->set_reflection_property_value('limit', 'LIMIT 10 OFFSET 0');
        $this->set_reflection_property_value('order_by', 'ORDER BY col ASC');

        $string = 'DELETE FROM table INNER JOIN table1';

        $this->assertEquals($string, $this->class->get_delete_query());
    }

}

?>
