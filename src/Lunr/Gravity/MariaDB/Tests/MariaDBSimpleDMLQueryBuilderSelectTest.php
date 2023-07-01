<?php

/**
 * This file contains the MariaDBSimpleDMLQueryBuilderSelectTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\MariaDB\Tests;

/**
 * This class contains select tests for the MariaDBSimpleDMLQueryBuilder class.
 *
 * @covers Lunr\Gravity\MariaDB\MariaDBSimpleDMLQueryBuilder
 */
class MariaDBSimpleDMLQueryBuilderSelectTest extends MariaDBSimpleDMLQueryBuilderTest
{

    /**
     * Test intersect().
     *
     * @param mixed $operators Operators to test
     *
     * @dataProvider compoundOperatorProvider
     * @covers Lunr\Gravity\MariaDB\MariaDBSimpleDMLQueryBuilder::intersect
     */
    public function testIntersect($operators): void
    {
        $this->escaper->expects($this->once())
                      ->method('query_value')
                      ->with('query')
                      ->willReturn('(query)');

        $this->builder->expects($this->once())
                      ->method('intersect')
                      ->with('(query)', $operators)
                      ->willReturnSelf();

        $this->class->intersect('query', $operators);
    }

    /**
     * Test except().
     *
     * @param mixed $operators Operators to test
     *
     * @dataProvider compoundOperatorProvider
     * @covers Lunr\Gravity\MariaDB\MariaDBSimpleDMLQueryBuilder::except
     */
    public function testExcept($operators): void
    {
        $this->escaper->expects($this->once())
                      ->method('query_value')
                      ->with('query')
                      ->willReturn('(query)');

        $this->builder->expects($this->once())
                      ->method('except')
                      ->with('(query)', $operators)
                      ->willReturnSelf();

        $this->class->except('query', $operators);
    }

}

?>
