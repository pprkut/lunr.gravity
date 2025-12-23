<?php

/**
 * SQLite database connection class.
 *
 * SPDX-FileCopyrightText: Copyright 2013 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\SQLite3;

use ArrayAccess;
use Lunr\Gravity\DatabaseConnection;
use Lunr\Gravity\Exceptions\ConnectionException;
use Lunr\Gravity\Exceptions\DefragmentationException;
use Psr\Log\LoggerInterface;

/**
 * SQLite database access class.
 *
 * @phpstan-type SQLite3Config array{
 *  'file'?: string,
 *  'driver': string,
 *  'errorReporting'?: int|bool
 * }
 * @phpstan-type SQLite3ConfigObject ArrayAccess<string, bool|int|string>
 */
class SQLite3Connection extends DatabaseConnection
{

    /**
     * Database config
     * @var SQLite3ConfigObject|SQLite3Config
     */
    protected ArrayAccess|array $config;

    /**
     * Database to connect to.
     * @var string
     */
    protected string $db;

    /**
     * Instance of the SQLite3 class
     * @var LunrSQLite3
     */
    protected $sqlite3;

    /**
     * Instance of the SQLite3QueryEscaper
     * @var SQLite3QueryEscaper
     */
    private readonly SQLite3QueryEscaper $escaper;

    /**
     * Constructor.
     *
     * @param SQLite3ConfigObject|SQLite3Config $config  Database config
     * @param LoggerInterface                   $logger  Shared instance of a logger class
     * @param LunrSQLite3                       $sqlite3 Instance of the LunrSQLite3 class
     */
    public function __construct(ArrayAccess|array $config, LoggerInterface $logger, LunrSQLite3 $sqlite3)
    {
        parent::__construct($logger);

        $this->config  = $config;
        $this->sqlite3 =& $sqlite3;

        $this->set_configuration();

        $this->sqlite3->enableExceptions($config['errorReporting'] ?? FALSE);
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        $this->disconnect();

        unset($this->sqlite3);
        unset($this->db);

        parent::__destruct();
    }

    /**
     * Set the config values.
     *
     * @return void
     */
    private function set_configuration(): void
    {
        $this->db = $this->config['file'] ?? ':memory:';
    }

    /**
     * Establishes a connection to the defined database.
     *
     * @return void
     */
    public function connect(): void
    {
        if ($this->connected === TRUE)
        {
            return;
        }

        if ($this->config['driver'] != 'sqlite3')
        {
            throw new ConnectionException('Cannot connect to a non-sqlite3 database connection!');
        }

        $flag = $this->readonly ? SQLITE3_OPEN_READONLY : SQLITE3_OPEN_READWRITE;

        $this->sqlite3->open($this->db, $flag | SQLITE3_OPEN_CREATE, '');

        if ($this->sqlite3->lastErrorCode() === 0)
        {
            $this->connected = TRUE;
        }

        if ($this->connected === FALSE)
        {
            throw new ConnectionException('Could not establish connection to the database!');
        }
    }

    /**
     * Disconnects from database.
     *
     * @return void
     */
    public function disconnect(): void
    {
        if ($this->connected !== TRUE)
        {
            return;
        }

        $this->sqlite3->close();

        $this->connected = FALSE;
    }

    /**
     * Change the default database for the current connection.
     *
     * @param string $db New default database
     *
     * @return bool True on success, False on Failure
     */
    public function change_database(string $db): bool
    {
        $this->db = $db;

        $this->disconnect();

        $this->connect();

        return $this->connected;
    }

    /**
     * Get the name of the database we're currently connected to.
     *
     * @return string Database name
     */
    public function get_database(): string
    {
        return $this->db;
    }

    /**
     * Return a new instance of a QueryBuilder object.
     *
     * @return SQLite3DMLQueryBuilder New SQLite3DMLQueryBuilder object instance
     */
    public function get_new_dml_query_builder_object(): SQLite3DMLQueryBuilder
    {
        return new SQLite3DMLQueryBuilder();
    }

    /**
     * Return a new instance of a QueryEscaper object.
     *
     * @return SQLite3QueryEscaper New SQLite3QueryEscaper object instance
     */
    public function get_query_escaper_object(): SQLite3QueryEscaper
    {
        if (isset($this->escaper) === FALSE)
        {
            $this->escaper = new SQLite3QueryEscaper($this);
        }

        return $this->escaper;
    }

    /**
     * Escape a string to be used in a SQL query.
     *
     * @param string $string The string to escape
     *
     * @return string The escaped string
     */
    public function escape_string(string $string): string
    {
        return $this->sqlite3->escapeString($string);
    }

    /**
     * Run a SQL query.
     *
     * @param string $sqlQuery The SQL query to run on the database
     *
     * @return SQLite3QueryResult $result Query Result
     */
    public function query(string $sqlQuery): SQLite3QueryResult
    {
        $this->connect();

        return new SQLite3QueryResult($sqlQuery, $this->sqlite3->query($sqlQuery), $this->sqlite3);
    }

    /**
     * Begins a transaction.
     *
     * @return bool
     */
    public function begin_transaction(): bool
    {
        $this->connect();

        return $this->sqlite3->exec('BEGIN TRANSACTION');
    }

    /**
     * Commits a transaction.
     *
     * @return bool
     */
    public function commit(): bool
    {
        $this->connect();

        return $this->sqlite3->exec('COMMIT TRANSACTION');
    }

    /**
     * Rolls back a transaction.
     *
     * @return bool
     */
    public function rollback(): bool
    {
        $this->connect();

        return $this->sqlite3->exec('ROLLBACK TRANSACTION');
    }

    /**
     * Ends a transaction.
     *
     * @return bool
     */
    public function end_transaction(): bool
    {
        $this->connect();

        return $this->sqlite3->exec('END TRANSACTION');
    }

    /**
     * Perform VACUUM operation to defragment the SQLite database.
     *
     * This function executes the VACUUM command, which optimizes the SQLite database file by reclaiming unused space.
     *
     * @param string $table This parameter is ignored when using SQLite, as VACUUM operates on the entire database.
     *
     * @return void
     */
    public function defragment(string $table = ''): void
    {
        $query = $this->query('VACUUM');

        if ($query->has_failed() === TRUE)
        {
            $context = [ 'query' => $query->query(), 'error' => $query->error_message() ];
            $this->logger->error('{query}; failed with error: {error}', $context);

            throw new DefragmentationException($query->error_number(), $query->error_message(), 'Database defragmentation failed.');
        }
    }

}

?>
