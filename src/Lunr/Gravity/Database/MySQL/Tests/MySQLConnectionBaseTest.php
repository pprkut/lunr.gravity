<?php

/**
 * This file contains the MySQLConnectionBaseTest class.
 *
 * @package    Lunr\Gravity\Database\MySQL
 * @author     Heinz Wiesinger <heinz@m2mobi.com>
 * @copyright  2012-2018, M2Mobi BV, Amsterdam, The Netherlands
 * @license    http://lunr.nl/LICENSE MIT License
 */

namespace Lunr\Gravity\Database\MySQL\Tests;

use Lunr\Halo\PropertyTraits\PsrLoggerTestTrait;

/**
 * This class contains basic tests for the MySQLConnection class.
 *
 * @covers Lunr\Gravity\Database\MySQL\MySQLConnection
 */
class MySQLConnectionBaseTest extends MySQLConnectionTest
{

    use PsrLoggerTestTrait;

    /**
     * Test that the mysqli class was passed correctly.
     */
    public function testMysqliPassed(): void
    {
        $this->assertPropertySame('mysqli', $this->mysqli);
    }

    /**
     * Test that rw_host is set correctly.
     */
    public function testRWHostIsSetCorrectly(): void
    {
        $this->assertPropertyEquals('rw_host', 'rw_host');
    }

    /**
     * Test that ro_host is set to rw_host.
     */
    public function testROHostIsSetToRWHost(): void
    {
        $this->assertPropertyEquals('ro_host', 'rw_host');
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
        $this->assertPropertySame('query_hint', '');
    }

    /**
     * Test that ssl_key is set as NULL if not set in the configuration.
     */
    public function testSSLKeyIsSetCorrectly(): void
    {
        $this->assertPropertyEquals('ssl_key', NULL);
    }

    /**
     * Test that ssl_cert is set as NULL if not set in the configuration.
     */
    public function testSSLCertIsSetCorrectly(): void
    {
        $this->assertPropertyEquals('ssl_cert', NULL);
    }

    /**
     * Test that ca_cert is set as NULL if not set in the configuration.
     */
    public function testCACertIsSetCorrectly(): void
    {
        $this->assertPropertyEquals('ca_cert', NULL);
    }

    /**
     * Test that ca_path is set as NULL if not set in the configuration.
     */
    public function testCAPathIsSetCorrectly(): void
    {
        $this->assertPropertyEquals('ca_path', NULL);
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
     * Test that get_new_dml_query_builder_object() returns a new object.
     *
     * @covers Lunr\Gravity\Database\MySQL\MySQLConnection::get_new_dml_query_builder_object
     */
    public function testGetNewDMLQueryBuilderObjectSimpleReturnsObject(): void
    {
        $value = $this->class->get_new_dml_query_builder_object();

        $this->assertInstanceOf('Lunr\Gravity\Database\MySQL\MySQLSimpleDMLQueryBuilder', $value);
    }

    /**
     * Test that get_new_dml_query_builder_object() returns a new object.
     *
     * @covers Lunr\Gravity\Database\MySQL\MySQLConnection::get_new_dml_query_builder_object
     */
    public function testGetNewDMLQueryBuilderObjectReturnsObject(): void
    {
        $value = $this->class->get_new_dml_query_builder_object(FALSE);

        $this->assertInstanceOf('Lunr\Gravity\Database\DatabaseDMLQueryBuilder', $value);
        $this->assertInstanceOf('Lunr\Gravity\Database\MySQL\MySQLDMLQueryBuilder', $value);
        $this->assertNotInstanceOf('Lunr\Gravity\Database\MySQL\MySQLSimpleDMLQueryBuilder', $value);
    }

    /**
     * Test that get_query_escaper_object() returns a new object.
     *
     * @covers Lunr\Gravity\Database\MySQL\MySQLConnection::get_query_escaper_object
     */
    public function testGetQueryEscaperObjectReturnsObject(): void
    {
        $value = $this->class->get_query_escaper_object();

        $this->assertInstanceOf('Lunr\Gravity\Database\DatabaseQueryEscaper', $value);
        $this->assertInstanceOf('Lunr\Gravity\Database\MySQL\MySQLQueryEscaper', $value);
    }

    /**
     * Test that get_query_escaper_object() returns a new object.
     *
     * @covers Lunr\Gravity\Database\MySQL\MySQLConnection::get_query_escaper_object
     */
    public function testGetQueryEscaperObjectCachesObject(): void
    {
        $property = $this->get_accessible_reflection_property('escaper');
        $this->assertNull($property->getValue($this->class));

        $this->class->get_query_escaper_object();

        $instance = 'Lunr\Gravity\Database\MySQL\MySQLQueryEscaper';
        $this->assertInstanceOf($instance, $property->getValue($this->class));
    }

    /**
     * Test that get_query_escaper_object() returns a new object.
     *
     * @covers Lunr\Gravity\Database\MySQL\MySQLConnection::get_query_escaper_object
     */
    public function testGetQueryEscaperObjectReturnsCachedObject(): void
    {
        $value1 = $this->class->get_query_escaper_object();
        $value2 = $this->class->get_query_escaper_object();

        $this->assertInstanceOf('Lunr\Gravity\Database\MySQL\MySQLQueryEscaper', $value1);
        $this->assertSame($value1, $value2);
    }

    /**
     * Test that get_database() returns the database name.
     *
     * @covers Lunr\Gravity\Database\MySQL\MySQLConnection::get_database
     */
    public function testGetDatabase(): void
    {
        $value = $this->class->get_database();

        $this->assertSame('database', $value);
    }

}

?>
