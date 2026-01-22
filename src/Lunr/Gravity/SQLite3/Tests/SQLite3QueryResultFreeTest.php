<?php

/**
 * Contains SQLite3QueryResultFreeTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\SQLite3\Tests;

/**
 * This class contains the tests for testing if the data is freed when there is a result.
 *
 * @covers Lunr\Gravity\SQLite3\SQLite3QueryResult
 */
class SQLite3QueryResultFreeTest extends SQLite3QueryResultTestCase
{

    /**
     * Override the default setUp with a setup with a result.
     */
    public function setUp(): void
    {
        $this->setUpWithResult();
    }

    /**
     * Test that free_result() does try to free the result data if freed is FALSE.
     *
     * @covers Lunr\Gravity\SQLite3\SQLite3QueryResult::free_result
     */
    public function testFreeResultFreesIfFreedIsFalse(): void
    {
        $this->sqlite3Result->expects($this->once())
                            ->method('finalize');

        $method = $this->getReflectionMethod('free_result');
        $method->invoke($this->class);
    }

    /**
     * Test that free_result() does not try to free the result data if freed is TRUE.
     *
     * @covers Lunr\Gravity\SQLite3\SQLite3QueryResult::free_result
     */
    public function testFreeResultDoesNotFreeIfFreedIsTrue(): void
    {
        $this->setReflectionPropertyValue('freed', TRUE);

        $this->sqlite3Result->expects($this->never())
                            ->method('finalize');

        $method = $this->getReflectionMethod('free_result');
        $method->invoke($this->class);

        $this->setReflectionPropertyValue('freed', FALSE);
    }

    /**
     * Test that result_array() will free the fetched data if freed is FALSE.
     *
     * @covers Lunr\Gravity\SQLite3\SQLite3QueryResult::result_array
     */
    public function testResultArrayFreesDataIfFreedIsFalse(): void
    {
        $this->sqlite3Result->expects($this->once())
                            ->method('finalize');

        $this->class->result_array();
    }

    /**
     * Test that result_array() will not free the fetched data if freed is TRUE.
     *
     * @covers Lunr\Gravity\SQLite3\SQLite3QueryResult::result_array
     */
    public function testResultArrayDoesNotFreeDataIfFreedIsTrue(): void
    {
        $this->setReflectionPropertyValue('freed', TRUE);

        $this->sqlite3Result->expects($this->never())
                            ->method('finalize');

        $this->class->result_array();
    }

    /**
     * Test that result_row() will free the fetched data if freed is FALSE.
     *
     * @covers Lunr\Gravity\SQLite3\SQLite3QueryResult::result_row
     */
    public function testResultRowFreesDataIfFreedIsFalse(): void
    {
        $this->sqlite3Result->expects($this->once())
                            ->method('fetchArray')
                            ->with(SQLITE3_ASSOC)
                            ->willReturn([[ 'col' => 'val' ]]);

        $this->sqlite3Result->expects($this->once())
                            ->method('finalize');

        $this->class->result_row();
    }

    /**
     * Test that result_row() will not free the fetched data if freed is TRUE.
     *
     * @covers Lunr\Gravity\SQLite3\SQLite3QueryResult::result_row
     */
    public function testResultRowDoesNotFreeDataIfFreedIsTrue(): void
    {
        $this->setReflectionPropertyValue('freed', TRUE);

        $this->sqlite3Result->expects($this->once())
                            ->method('fetchArray')
                            ->with(SQLITE3_ASSOC)
                            ->willReturn([[ 'col' => 'val' ]]);

        $this->sqlite3Result->expects($this->never())
                            ->method('finalize');

        $this->class->result_row();
    }

    /**
     * Test that result_column() will free the fetched data if freed is FALSE.
     *
     * @covers Lunr\Gravity\SQLite3\SQLite3QueryResult::result_column
     */
    public function testResultColumnFreesDataIfFreedIsFalse(): void
    {
        $this->sqlite3Result->expects($this->once())
                            ->method('finalize');

        $this->class->result_column('col');
    }

    /**
     * Test that result_column() will not free the fetched data if freed is TRUE.
     *
     * @covers Lunr\Gravity\SQLite3\SQLite3QueryResult::result_column
     */
    public function testResultColumnDoesNotFreeDataIfFreedIsTrue(): void
    {
        $this->setReflectionPropertyValue('freed', TRUE);

        $this->sqlite3Result->expects($this->never())
                            ->method('finalize');

        $this->class->result_column('col');
    }

    /**
     * Test that result_cell() will free the fetched data if freed is FALSE.
     *
     * @covers Lunr\Gravity\SQLite3\SQLite3QueryResult::result_cell
     */
    public function testResultCellFreesDataIfFreedIsFalse(): void
    {
        $this->sqlite3Result->expects($this->once())
                            ->method('finalize');

        $this->class->result_cell('cell');
    }

    /**
     * Test that result_cell() will not free the fetched data if freed is TRUE.
     *
     * @covers Lunr\Gravity\SQLite3\SQLite3QueryResult::result_cell
     */
    public function testResultCellDoesNotFreeDataIfFreedIsTrue(): void
    {
        $this->setReflectionPropertyValue('freed', TRUE);

        $this->sqlite3Result->expects($this->never())
                            ->method('finalize');

        $this->class->result_cell('cell');
    }

}

?>
