<?php

/**
 * This file contains the MariaDBSimpleDMLQueryBuilderTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2018 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\MariaDB\Tests;

use ReflectionClass;
use Lunr\Gravity\MySQL\MySQLQueryEscaper;
use Lunr\Gravity\MariaDB\MariaDBDMLQueryBuilder;
use Lunr\Gravity\MariaDB\MariaDBSimpleDMLQueryBuilder;
use Lunr\Halo\LunrBaseTest;

/**
 * This class contains common setup routines, providers
 * and shared attributes for testing the MariaDBSimpleDMLQueryBuilder class.
 *
 * @covers Lunr\Gravity\MariaDB\MMariaDBSimpleDMLQueryBuilder
 */
abstract class MariaDBSimpleDMLQueryBuilderTest extends LunrBaseTest
{

    /**
     * MySQL query escaper instance.
     * @var MySQLQueryEscaper
     */
    protected $escaper;

    /**
     * Instance of the simple  query builder.
     * @var MariaDBDMLQueryBuilder
     */
    protected $builder;

    /**
     * Testcase Constructor.
     */
    public function setUp(): void
    {
        $this->escaper = $this->getMockBuilder('Lunr\Gravity\MySQL\MySQLQueryEscaper')
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->builder = $this->getMockBuilder('Lunr\Gravity\MariaDB\MariaDBDMLQueryBuilder')
                              ->getMock();

        $this->class      = new MariaDBSimpleDMLQueryBuilder($this->builder, $this->escaper);
        $this->reflection = new ReflectionClass('Lunr\Gravity\MariaDB\MariaDBSimpleDMLQueryBuilder');
    }

    /**
     * Testcase Destructor.
     */
    public function tearDown(): void
    {
        unset($this->escaper);
        unset($this->builder);

        parent::tearDown();
    }

    /**
    * Unit test data provider for tested union operators.
    *
    * @return array $compound operators for union query
    */
    public function compoundOperatorProvider(): array
    {
        $operators   = [];
        $operators[] = [ '' ];
        $operators[] = [ 'ALL' ];
        $operators[] = [ 'DISTINCT' ];
        $operators[] = [ TRUE ];
        $operators[] = [ FALSE ];

        return $operators;
    }

}

?>
