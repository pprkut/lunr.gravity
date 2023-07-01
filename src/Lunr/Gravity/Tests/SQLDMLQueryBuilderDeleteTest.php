<?php

/**
 * This file contains the SQLDMLQueryBuilderDeleteTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2013 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\Tests;

/**
 * This class contains the tests for the query parts necessary to build
 * delete queries.
 *
 * @covers Lunr\Gravity\SQLDMLQueryBuilder
 */
class SQLDMLQueryBuilderDeleteTest extends SQLDMLQueryBuilderTest
{

    /**
     * Test specifying the DELETE part of a query.
     *
     * @depends Lunr\Gravity\Tests\DatabaseDMLQueryBuilderQueryPartsDeleteTest::testInitialDelete
     * @depends Lunr\Gravity\Tests\DatabaseDMLQueryBuilderQueryPartsDeleteTest::testIncrementalDelete
     * @covers  Lunr\Gravity\SQLDMLQueryBuilder::delete
     */
    public function testDelete(): void
    {
        $this->class->delete('table');
        $value = $this->get_reflection_property_value('delete');

        $this->assertEquals('table', $value);
    }

    /**
     * Test fluid interface of the delete method.
     *
     * @covers Lunr\Gravity\SQLDMLQueryBuilder::delete
     */
    public function testDeleteReturnsSelfReference(): void
    {
        $return = $this->class->delete('table');

        $this->assertInstanceOf('Lunr\Gravity\SQLDMLQueryBuilder', $return);
        $this->assertSame($this->class, $return);
    }

}

?>
