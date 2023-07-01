<?php

/**
 * This file contains the SQLDMLQueryBuilderUpdateTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2013 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\Tests;

/**
 * This class contains the tests for the query parts necessary to build
 * update queries.
 *
 * @covers Lunr\Gravity\SQLDMLQueryBuilder
 */
class SQLDMLQueryBuilderUpdateTest extends SQLDMLQueryBuilderTest
{

    /**
     * Test specifying the SELECT part of a query.
     *
     * @depends Lunr\Gravity\Tests\DatabaseDMLQueryBuilderQueryPartsUpdateTest::testInitialUpdate
     * @depends Lunr\Gravity\Tests\DatabaseDMLQueryBuilderQueryPartsUpdateTest::testIncrementalUpdate
     * @covers  Lunr\Gravity\SQLDMLQueryBuilder::update
     */
    public function testUpdate(): void
    {
        $this->class->update('table');
        $value = $this->get_reflection_property_value('update');

        $this->assertEquals('table', $value);
    }

    /**
     * Test fluid interface of the update method.
     *
     * @covers Lunr\Gravity\SQLDMLQueryBuilder::update
     */
    public function testUpdateReturnsSelfReference(): void
    {
        $return = $this->class->update('table');

        $this->assertInstanceOf('Lunr\Gravity\SQLDMLQueryBuilder', $return);
        $this->assertSame($this->class, $return);
    }

    /**
     * Test fluid interface of the set method.
     *
     * @covers Lunr\Gravity\SQLDMLQueryBuilder::set
     */
    public function testSetReturnsSelfReference(): void
    {
        $return = $this->class->set([ 'column1' => 'value1' ]);

        $this->assertInstanceOf('Lunr\Gravity\SQLDMLQueryBuilder', $return);
        $this->assertSame($this->class, $return);
    }

}

?>
