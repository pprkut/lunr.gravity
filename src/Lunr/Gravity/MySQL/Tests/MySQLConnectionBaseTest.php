<?php

/**
 * This file contains the MySQLConnectionBaseTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\MySQL\Tests;

use Lunr\Halo\PropertyTraits\PsrLoggerTestTrait;
use MySQLi_Driver;

/**
 * This class contains basic tests for the MySQLConnection class.
 *
 * @covers Lunr\Gravity\MySQL\MySQLConnection
 */
class MySQLConnectionBaseTest extends MySQLConnectionTestCase
{

    use PsrLoggerTestTrait;

    /**
     * Test that the Configuration class is passed by reference.
     */
    public function testConfigurationIsPassedByReference(): void
    {
        $this->assertPropertySame('config', $this->configuration);
    }

    /**
     * Test that the mysqli class was passed correctly.
     */
    public function testMysqliPassed(): void
    {
        $this->assertPropertySame('mysqli', $this->mysqli);
    }

    /**
     * Test that by default we don't have a QueryEscaper instance.
     */
    public function testEscaperIsUnset(): void
    {
        $this->assertPropertyUnset('escaper');
    }

    /**
     * Test that rw_host is set correctly.
     */
    public function testRWHostIsSetCorrectly(): void
    {
        $this->assertPropertyEquals('rwHost', 'rwHost');
    }

    /**
     * Test that ro_host is set to rw_host.
     */
    public function testROHostIsSetToRWHost(): void
    {
        $this->assertPropertyEquals('roHost', 'rwHost');
    }

    /**
     * Test that username is set correctly.
     */
    public function testUsernameIsSetCorrectly(): void
    {
        $this->assertPropertyEquals('user', 'username');
    }

    /**
     * Test that password is set correctly.
     */
    public function testPasswordIsSetCorrectly(): void
    {
        $this->assertPropertyEquals('pwd', 'password');
    }

    /**
     * Test that database is set correctly.
     */
    public function testDatabaseIsSetCorrectly(): void
    {
        $this->assertPropertyEquals('db', 'database');
    }

    /**
     * Test that the value for port is taken from the php ini.
     */
    public function testPortMatchesValueInPHPIni(): void
    {
        $this->assertPropertyEquals('port', ini_get('mysqli.default_port'));
    }

    /**
     * Test that the value for socket is taken from the php ini.
     */
    public function testSocketMatchesValueInPHPIni(): void
    {
        $this->assertPropertyEquals('socket', ini_get('mysqli.default_socket'));
    }

    /**
     * Test that database is set correctly.
     */
    public function testQueryHintIsEmpty(): void
    {
        $this->assertPropertySame('queryHint', '');
    }

    /**
     * Test that ssl_key is set as NULL if not set in the configuration.
     */
    public function testSSLKeyIsSetCorrectly(): void
    {
        $this->assertPropertyEquals('sslKey', NULL);
    }

    /**
     * Test that ssl_cert is set as NULL if not set in the configuration.
     */
    public function testSSLCertIsSetCorrectly(): void
    {
        $this->assertPropertyEquals('sslCert', NULL);
    }

    /**
     * Test that ca_cert is set as NULL if not set in the configuration.
     */
    public function testCACertIsSetCorrectly(): void
    {
        $this->assertPropertyEquals('caCert', NULL);
    }

    /**
     * Test that ca_path is set as NULL if not set in the configuration.
     */
    public function testCAPathIsSetCorrectly(): void
    {
        $this->assertPropertyEquals('caPath', NULL);
    }

    /**
     * Test that cipher is set as NULL if not set in the configuration.
     */
    public function testCipherIsSetCorrectly(): void
    {
        $this->assertPropertyEquals('cipher', NULL);
    }

    /**
     * Test that options is set correctly.
     */
    public function testOptionsIsSetCorrectly(): void
    {
        $this->assertPropertyEquals('options', [ MYSQLI_OPT_INT_AND_FLOAT_NATIVE => TRUE ]);
    }

    /**
     * Test that options is set correctly.
     */
    public function testErrorReportingIsSetCorrectly(): void
    {
        $driver = new MySQLi_Driver();

        // phpcs:ignore Lunr.NamingConventions.CamelCapsVariableName.NotCamelCaps
        $this->assertEquals($driver->report_mode, MYSQLI_REPORT_ERROR);
    }

    /**
     * Test that get_new_dml_query_builder_object() returns a new object.
     *
     * @covers Lunr\Gravity\MySQL\MySQLConnection::get_new_dml_query_builder_object
     */
    public function testGetNewDMLQueryBuilderObjectSimpleReturnsObject(): void
    {
        $value = $this->class->get_new_dml_query_builder_object();

        $this->assertInstanceOf('Lunr\Gravity\MySQL\MySQLSimpleDMLQueryBuilder', $value);
    }

    /**
     * Test that get_new_dml_query_builder_object() returns a new object.
     *
     * @covers Lunr\Gravity\MySQL\MySQLConnection::get_new_dml_query_builder_object
     */
    public function testGetNewDMLQueryBuilderObjectReturnsObject(): void
    {
        $value = $this->class->get_new_dml_query_builder_object(FALSE);

        $this->assertInstanceOf('Lunr\Gravity\DatabaseDMLQueryBuilder', $value);
        $this->assertInstanceOf('Lunr\Gravity\MySQL\MySQLDMLQueryBuilder', $value);
        $this->assertNotInstanceOf('Lunr\Gravity\MySQL\MySQLSimpleDMLQueryBuilder', $value);
    }

    /**
     * Test that get_query_escaper_object() returns a new object.
     *
     * @covers Lunr\Gravity\MySQL\MySQLConnection::get_query_escaper_object
     */
    public function testGetQueryEscaperObjectReturnsObject(): void
    {
        $value = $this->class->get_query_escaper_object();

        $this->assertInstanceOf('Lunr\Gravity\DatabaseQueryEscaper', $value);
        $this->assertInstanceOf('Lunr\Gravity\MySQL\MySQLQueryEscaper', $value);
    }

    /**
     * Test that get_query_escaper_object() returns a new object.
     *
     * @covers Lunr\Gravity\MySQL\MySQLConnection::get_query_escaper_object
     */
    public function testGetQueryEscaperObjectCachesObject(): void
    {
        $this->assertPropertyUnset('escaper');

        $this->class->get_query_escaper_object();

        $property = $this->getReflectionProperty('escaper');
        $instance = 'Lunr\Gravity\MySQL\MySQLQueryEscaper';
        $this->assertInstanceOf($instance, $property->getValue($this->class));
    }

    /**
     * Test that get_query_escaper_object() returns a new object.
     *
     * @covers Lunr\Gravity\MySQL\MySQLConnection::get_query_escaper_object
     */
    public function testGetQueryEscaperObjectReturnsCachedObject(): void
    {
        $value1 = $this->class->get_query_escaper_object();
        $value2 = $this->class->get_query_escaper_object();

        $this->assertInstanceOf('Lunr\Gravity\MySQL\MySQLQueryEscaper', $value1);
        $this->assertSame($value1, $value2);
    }

    /**
     * Test that get_database() returns the database name.
     *
     * @covers Lunr\Gravity\MySQL\MySQLConnection::get_database
     */
    public function testGetDatabase(): void
    {
        $value = $this->class->get_database();

        $this->assertSame('database', $value);
    }

}

?>
