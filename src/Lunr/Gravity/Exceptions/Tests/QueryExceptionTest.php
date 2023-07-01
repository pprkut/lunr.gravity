<?php

/**
 * This file contains the QueryExceptionTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2019 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\Exceptions\Tests;

use Lunr\Gravity\Exceptions\QueryException;
use Lunr\Halo\LunrBaseTest;
use Exception;
use ReflectionClass;

/**
 * This class contains common setup routines, providers
 * and shared attributes for testing the QueryException class.
 */
abstract class QueryExceptionTest extends LunrBaseTest
{

    /**
     * Mock instance of a query result.
     * @var \Lunr\Gravity\DatabaseQueryResultInterface
     */
    protected $result;

    /**
     * TestCase Constructor.
     */
    public function setUp(): void
    {
        $this->result = $this->getMockBuilder('Lunr\Gravity\DatabaseQueryResultInterface')
                             ->disableOriginalConstructor()
                             ->getMock();

        $this->result->expects($this->once())
                     ->method('query')
                     ->willReturn('SQL query');

        $this->result->expects($this->once())
                     ->method('error_number')
                     ->willReturn(1024);

        $this->result->expects($this->once())
                     ->method('error_message')
                     ->willReturn("There's an error in your query.");

        $this->class      = new QueryException($this->result, 'Exception Message');
        $this->reflection = new ReflectionClass('Lunr\Gravity\Exceptions\QueryException');
    }

    /**
     * TestCase Destructor.
     */
    public function tearDown(): void
    {
        unset($this->result);
        unset($this->reflection);
        unset($this->class);
    }

}

?>
