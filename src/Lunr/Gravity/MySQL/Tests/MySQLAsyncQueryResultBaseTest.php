<?php

/**
 * This file contains the MySQLAsyncQueryResultBaseTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\MySQL\Tests;

/**
 * This class contains basic tests for the MySQLAsyncQueryResult class.
 *
 * @covers Lunr\Gravity\MySQL\MySQLAsyncQueryResult
 */
class MySQLAsyncQueryResultBaseTest extends MySQLAsyncQueryResultTestCase
{

    /**
     * Test that error message is empty on successful query.
     */
    public function testErrorMessageIsEmpty(): void
    {
        $property = $this->reflection->getProperty('errorMessage');
        $property->setAccessible(TRUE);

        $this->assertNull($property->getValue($this->class));
    }

    /**
     * Test that error number is zero on successful query.
     */
    public function testErrorNumberIsZero(): void
    {
        $property = $this->reflection->getProperty('errorNumber');
        $property->setAccessible(TRUE);

        $this->assertNull($property->getValue($this->class));
    }

    /**
     * Test that error number is zero on successful query.
     */
    public function testInsertIDIsZero(): void
    {
        $property = $this->reflection->getProperty('insertID');
        $property->setAccessible(TRUE);

        $this->assertNull($property->getValue($this->class));
    }

    /**
     * Test that affected rows is a number on successful query.
     */
    public function testAffectedRowsIsUninitialized(): void
    {
        $this->assertPropertyUnset('affectedRows');
    }

    /**
     * Test that number of rows is a number on successful query.
     */
    public function testNumberOfRowsIsNumber(): void
    {
        $this->assertPropertyUnset('numRows');
    }

    /**
     * Test that error message is empty on successful query.
     */
    public function testQueryIsPassedCorrectly(): void
    {
        $property = $this->reflection->getProperty('query');
        $property->setAccessible(TRUE);

        $this->assertEquals($this->query, $property->getValue($this->class));
    }

    /**
     * Test that the mysqli class is passed by reference.
     */
    public function testMysqliIsPassedByReference(): void
    {
        $property = $this->reflection->getProperty('mysqli');
        $property->setAccessible(TRUE);

        $value = $property->getValue($this->class);

        $this->assertInstanceOf('Lunr\Gravity\MySQL\Tests\MockMySQLiSuccessfulConnection', $value);
        $this->assertSame($this->mysqli, $value);
    }

    /**
     * Test that the fetched flag is FALSE by default.
     */
    public function testFetchedIsFalseByDefault(): void
    {
        $property = $this->reflection->getProperty('fetched');
        $property->setAccessible(TRUE);

        $this->assertFalse($property->getValue($this->class));
    }

    /**
     * Test that fethc_result() does not try to refetch the result if it was already fetched.
     *
     * @covers Lunr\Gravity\MySQL\MySQLAsyncQueryResult::fetch_result
     */
    public function testFetchResultDoesNotRefetchIfFetchedIsTrue(): void
    {
        $property = $this->reflection->getProperty('fetched');
        $property->setAccessible(TRUE);
        $property->setValue($this->class, TRUE);

        $this->mysqli->expects($this->never())
                     ->method('reap_async_query');

        $method = $this->reflection->getMethod('fetch_result');
        $method->setAccessible(TRUE);

        $method->invoke($this->class);

        $this->assertTrue($property->getValue($this->class));
    }

    /**
     * Test that fetch_result() stores the result correctly.
     *
     * @requires extension mysqli
     * @covers   Lunr\Gravity\MySQL\MySQLAsyncQueryResult::fetch_result
     */
    public function testFetchResultStoresResult(): void
    {
        $this->mockFunction('mysqli_affected_rows', fn() => 0);
        $this->mockFunction('mysqli_num_rows', fn() => 0);

        $result = $this->getMockBuilder('mysqli_result')
                       ->disableOriginalConstructor()
                       ->getMock();

        $this->mysqli->expects($this->once())
                     ->method('reap_async_query')
                     ->willReturn($result);

        $method = $this->reflection->getMethod('fetch_result');
        $method->setAccessible(TRUE);

        $method->invoke($this->class);

        $property = $this->reflection->getProperty('result');
        $property->setAccessible(TRUE);

        $this->assertInstanceOf('mysqli_result', $property->getValue($this->class));

        $this->unmockFunction('mysqli_affected_rows');
        $this->unmockFunction('mysqli_num_rows');
    }

    /**
     * Test that fetch_result() sets the fetched flag to TRUE.
     *
     * @requires extension mysqli
     * @covers   Lunr\Gravity\MySQL\MySQLAsyncQueryResult::fetch_result
     */
    public function testFetchResultSetsFetchedToTrue(): void
    {
        $this->mockFunction('mysqli_affected_rows', fn() => 0);

        $this->mysqli->expects($this->once())
                     ->method('reap_async_query')
                     ->willReturn(FALSE);

        $method = $this->reflection->getMethod('fetch_result');
        $method->setAccessible(TRUE);

        $method->invoke($this->class);

        $property = $this->reflection->getProperty('fetched');
        $property->setAccessible(TRUE);

        $this->assertTrue($property->getValue($this->class));

        $this->unmockFunction('mysqli_affected_rows');
    }

    /**
     * Test that fetch_result() sets the success flag to TRUE if the result value is TRUE.
     *
     * @requires extension mysqli
     * @covers   Lunr\Gravity\MySQL\MySQLAsyncQueryResult::fetch_result
     */
    public function testFetchedResultSetsSuccessTrueIfResultIsTrue(): void
    {
        $this->mockFunction('mysqli_affected_rows', fn() => 0);

        $this->mysqli->expects($this->once())
                     ->method('reap_async_query')
                     ->willReturn(TRUE);

        $method = $this->reflection->getMethod('fetch_result');
        $method->setAccessible(TRUE);

        $method->invoke($this->class);

        $property = $this->reflection->getProperty('success');
        $property->setAccessible(TRUE);

        $this->assertTrue($property->getValue($this->class));

        $this->unmockFunction('mysqli_affected_rows');
    }

    /**
     * Test that fetch_result() sets the success flag to FALSE if the result value is FALSE.
     *
     * @requires extension mysqli
     * @covers   Lunr\Gravity\MySQL\MySQLAsyncQueryResult::fetch_result
     */
    public function testFetchedResultSetsSuccessFalseIfResultIsFalse(): void
    {
        $this->mockFunction('mysqli_affected_rows', fn() => 0);

        $this->mysqli->expects($this->once())
                     ->method('reap_async_query')
                     ->willReturn(FALSE);

        $method = $this->reflection->getMethod('fetch_result');
        $method->setAccessible(TRUE);

        $method->invoke($this->class);

        $property = $this->reflection->getProperty('success');
        $property->setAccessible(TRUE);

        $this->assertFalse($property->getValue($this->class));

        $this->unmockFunction('mysqli_affected_rows');
    }

    /**
     * Test that fetch_result() sets the success flag to TRUE if the result is of type MySQLi_Result.
     *
     * @requires extension mysqli
     * @covers   Lunr\Gravity\MySQL\MySQLAsyncQueryResult::fetch_result
     */
    public function testFetchedResultSetsSuccessTrueIfResultIsMysqliResult(): void
    {
        $this->mockFunction('mysqli_affected_rows', fn() => 0);
        $this->mockFunction('mysqli_num_rows', fn() => 0);

        $result = $this->getMockBuilder('mysqli_result')
                       ->disableOriginalConstructor()
                       ->getMock();

        $this->mysqli->expects($this->once())
                     ->method('reap_async_query')
                     ->willReturn($result);

        $method = $this->reflection->getMethod('fetch_result');
        $method->setAccessible(TRUE);

        $method->invoke($this->class);

        $property = $this->reflection->getProperty('success');
        $property->setAccessible(TRUE);

        $this->assertTrue($property->getValue($this->class));

        $this->unmockFunction('mysqli_affected_rows');
        $this->unmockFunction('mysqli_num_rows');
    }

    /**
     * Test that fetch_result() sets the error message.
     *
     * @requires extension mysqli
     * @covers   Lunr\Gravity\MySQL\MySQLAsyncQueryResult::fetch_result
     */
    public function testFetchResultSetsErrorMessage(): void
    {
        $this->mockFunction('mysqli_affected_rows', fn() => 0);

        $this->mysqli->expects($this->once())
                     ->method('reap_async_query')
                     ->willReturn(FALSE);

        $method = $this->reflection->getMethod('fetch_result');
        $method->setAccessible(TRUE);

        $method->invoke($this->class);

        $property = $this->reflection->getProperty('errorMessage');
        $property->setAccessible(TRUE);

        $this->assertEquals('', $property->getValue($this->class));

        $this->unmockFunction('mysqli_affected_rows');
    }

    /**
     * Test that fetch_result() sets the error number.
     *
     * @requires extension mysqli
     * @covers   Lunr\Gravity\MySQL\MySQLAsyncQueryResult::fetch_result
     */
    public function testFetchResultSetsErrorNumber(): void
    {
        $this->mockFunction('mysqli_affected_rows', fn() => 0);

        $this->mysqli->expects($this->once())
                     ->method('reap_async_query')
                     ->willReturn(FALSE);

        $method = $this->reflection->getMethod('fetch_result');
        $method->setAccessible(TRUE);

        $method->invoke($this->class);

        $property = $this->reflection->getProperty('errorNumber');
        $property->setAccessible(TRUE);

        $this->assertEquals(0, $property->getValue($this->class));

        $this->unmockFunction('mysqli_affected_rows');
    }

    /**
     * Test that fetch_result() sets insert id.
     *
     * @requires extension mysqli
     * @covers   Lunr\Gravity\MySQL\MySQLAsyncQueryResult::fetch_result
     */
    public function testFetchResultSetsInsertID(): void
    {
        $this->mockFunction('mysqli_affected_rows', fn() => 0);

        $this->mysqli->expects($this->once())
                     ->method('reap_async_query')
                     ->willReturn(FALSE);

        $method = $this->reflection->getMethod('fetch_result');
        $method->setAccessible(TRUE);

        $method->invoke($this->class);

        $property = $this->reflection->getProperty('insertID');
        $property->setAccessible(TRUE);

        $this->assertEquals(0, $property->getValue($this->class));

        $this->unmockFunction('mysqli_affected_rows');
    }

    /**
     * Test that fetch_result() sets affected rows.
     *
     * @requires extension mysqli
     * @covers   Lunr\Gravity\MySQL\MySQLAsyncQueryResult::fetch_result
     */
    public function testFetchResultSetsAffectedRows(): void
    {
        $this->mockFunction('mysqli_affected_rows', fn() => 10);

        $this->mysqli->expects($this->once())
                     ->method('reap_async_query')
                     ->willReturn(FALSE);

        $method = $this->reflection->getMethod('fetch_result');
        $method->setAccessible(TRUE);

        $method->invoke($this->class);

        $property = $this->reflection->getProperty('affectedRows');
        $property->setAccessible(TRUE);

        $this->assertEquals(10, $property->getValue($this->class));

        $this->unmockFunction('mysqli_affected_rows');
    }

    /**
     * Test that fetch_result() sets number of rows.
     *
     * @requires extension mysqli
     * @covers   Lunr\Gravity\MySQL\MySQLAsyncQueryResult::fetch_result
     */
    public function testFetchResultSetsNumberOfRows(): void
    {
        $this->mockFunction('mysqli_affected_rows', fn() => 10);

        $this->mysqli->expects($this->once())
                     ->method('reap_async_query')
                     ->willReturn(FALSE);

        $method = $this->reflection->getMethod('fetch_result');
        $method->setAccessible(TRUE);

        $method->invoke($this->class);

        $property = $this->reflection->getProperty('numRows');
        $property->setAccessible(TRUE);

        $this->assertEquals(10, $property->getValue($this->class));

        $this->unmockFunction('mysqli_affected_rows');
    }

}

?>
