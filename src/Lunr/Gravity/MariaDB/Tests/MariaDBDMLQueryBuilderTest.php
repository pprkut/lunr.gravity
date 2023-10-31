<?php

/**
 * This file contains the MariaDMLQueryBuilderTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2018 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\MariaDB\Tests;

use Lunr\Gravity\MariaDB\MariaDBDMLQueryBuilder;
use Lunr\Halo\LunrBaseTest;
use ReflectionClass;

/**
 * This class contains common setup routines, providers
 * and shared attributes for testing the MariaDBDMLQueryBuilder class.
 *
 * @covers \Lunr\Gravity\MariaDB\MariaDBDMLQueryBuilder
 */
abstract class MariaDBDMLQueryBuilderTest extends LunrBaseTest
{

    /**
     * Instance of the tested class.
     * @var MariaDBDMLQueryBuilder
     */
    protected MariaDBDMLQueryBuilder $class;

    /**
     * TestCase Constructor.
     */
    public function setUp(): void
    {
        $this->class = new MariaDBDMLQueryBuilder();

        parent::baseSetUp($this->class);
    }

    /**
     * TestCase Destructor.
     */
    public function tearDown(): void
    {
        unset($this->class);

        parent::tearDown();
    }
}

?>
