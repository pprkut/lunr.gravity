<?php

/**
 * This file contains the MySQLQueryResultFreeTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\MySQL\Tests;

/**
 * This class contains tests for the freeing of data in the MySQLQueryResult class.
 *
 * @covers Lunr\Gravity\MySQL\MySQLQueryResult
 */
class MySQLQueryResultFreeTest extends MySQLQueryResultTestCase
{

    /**
     * TestCase Constructor.
     */
    public function setUp(): void
    {
        $this->mockFunction('mysqli_affected_rows', fn() => 10);
        $this->mockFunction('mysqli_num_rows', fn() => 10);

        $this->resultSetSetup();

        $this->unmockFunction('mysqli_affected_rows');
        $this->unmockFunction('mysqli_num_rows');
    }

    /**
     * Test that free_result() does try to free the result data if freed is FALSE.
     *
     * @covers Lunr\Gravity\MySQL\MySQLQueryResult::free_result
     */
    public function testFreeResultFreesIfFreedIsFalse(): void
    {
        $this->queryResult->expects($this->once())
                    ->method('free');

        $method = $this->getReflectionMethod('free_result');

        $method->invoke($this->class);
    }

    /**
     * Test that free_result() does not try to free the result data if freed is TRUE.
     *
     * @covers Lunr\Gravity\MySQL\MySQLQueryResult::free_result
     */
    public function testFreeResultDoesNotFreeIfFreedIsTrue(): void
    {
        $this->setReflectionPropertyValue('freed', TRUE);

        $this->queryResult->expects($this->never())
                    ->method('free');

        $method = $this->getReflectionMethod('free_result');

        $method->invoke($this->class);

        // $this->setReflectionPropertyValue('freed', FALSE);
    }

    /**
     * Test that result_array() will free the fetched data if freed is FALSE.
     *
     * @covers Lunr\Gravity\MySQL\MySQLQueryResult::result_array
     */
    public function testResultArrayFreesDataIfFreedIsFalse(): void
    {
        $this->queryResult->expects($this->once())
                          ->method('free');

        $this->class->result_array();
    }

    /**
     * Test that result_array() will not free the fetched data if freed is TRUE.
     *
     * @covers Lunr\Gravity\MySQL\MySQLQueryResult::result_array
     */
    public function testResultArrayDoesNotFreeDataIfFreedIsTrue(): void
    {
        $this->setReflectionPropertyValue('freed', TRUE);

        $this->queryResult->expects($this->never())
                          ->method('free');

        $this->class->result_array();
    }

    /**
     * Test that result_row() will free the fetched data if freed is FALSE.
     *
     * @covers Lunr\Gravity\MySQL\MySQLQueryResult::result_row
     */
    public function testResultRowFreesDataIfFreedIsFalse(): void
    {
        $this->queryResult->expects($this->once())
                          ->method('fetch_assoc')
                          ->willReturn([[ 'col' => 'val' ]]);

        $this->queryResult->expects($this->once())
                          ->method('free');

        $this->class->result_row();
    }

    /**
     * Test that result_row() will not free the fetched data if freed is TRUE.
     *
     * @covers Lunr\Gravity\MySQL\MySQLQueryResult::result_row
     */
    public function testResultRowDoesNotFreeDataIfFreedIsTrue(): void
    {
        $this->setReflectionPropertyValue('freed', TRUE);

        $this->queryResult->expects($this->once())
                          ->method('fetch_assoc')
                          ->willReturn([[ 'col' => 'val' ]]);

        $this->queryResult->expects($this->never())
                          ->method('free');

        $this->class->result_row();
    }

    /**
     * Test that result_column() will free the fetched data if freed is FALSE.
     *
     * @covers Lunr\Gravity\MySQL\MySQLQueryResult::result_column
     */
    public function testResultColumnFreesDataIfFreedIsFalse(): void
    {
        $this->queryResult->expects($this->once())
                          ->method('free');

        $this->class->result_column('col');
    }

    /**
     * Test that result_column() will not free the fetched data if freed is TRUE.
     *
     * @covers Lunr\Gravity\MySQL\MySQLQueryResult::result_column
     */
    public function testResultColumnDoesNotFreeDataIfFreedIsTrue(): void
    {
        $this->setReflectionPropertyValue('freed', TRUE);

        $this->queryResult->expects($this->never())
                          ->method('free');

        $this->class->result_column('col');
    }

    /**
     * Test that result_cell() will free the fetched data if freed is FALSE.
     *
     * @covers Lunr\Gravity\MySQL\MySQLQueryResult::result_cell
     */
    public function testResultCellFreesDataIfFreedIsFalse(): void
    {
        $this->queryResult->expects($this->once())
                          ->method('free');

        $this->class->result_cell('cell');
    }

    /**
     * Test that result_cell() will not free the fetched data if freed is TRUE.
     *
     * @covers Lunr\Gravity\MySQL\MySQLQueryResult::result_cell
     */
    public function testResultCellDoesNotFreeDataIfFreedIsTrue(): void
    {
        $this->setReflectionPropertyValue('freed', TRUE);

        $this->queryResult->expects($this->never())
                          ->method('free');

        $this->class->result_cell('cell');
    }

}

?>
