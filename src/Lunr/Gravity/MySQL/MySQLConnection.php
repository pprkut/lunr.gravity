<?php

/**
 * MySQL database connection class.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\MySQL;

use ArrayAccess;
use Lunr\Gravity\DatabaseConnection;
use Lunr\Gravity\Exceptions\ConnectionException;
use Lunr\Gravity\Exceptions\DefragmentationException;
use Lunr\Ticks\AnalyticsDetailLevel;
use MySQLi;
use Psr\Log\LoggerInterface;

/**
 * MySQL/MariaDB database access class.
 *
 * @phpstan-type MySQLConfig array{
 *  'rwHost': string,
 *  'roHost'?: string,
 *  'port'?: int,
 *  'socket'?: string,
 *  'sslKey'?: string,
 *  'sslCert'?: string,
 *  'caCert'?: string,
 *  'caPath'?: string,
 *  'cipher'?: string,
 *  'username': string,
 *  'password': string,
 *  'database': string,
 *  'driver': string,
 *  'errorReporting'?: int|bool
 * }
 * @phpstan-type MySQLConfigObject ArrayAccess<string, bool|int|string>
 */
class MySQLConnection extends DatabaseConnection
{

    /**
     * Database config
     * @var MySQLConfigObject|MySQLConfig
     */
    protected ArrayAccess|array $config;

    /**
     * Hostname of the database server (read/write access)
     * @var string|null
     */
    protected ?string $rwHost;

    /**
     * Hostname of the database server (readonly access)
     * @var string|null
     */
    protected ?string $roHost;

    /**
     * Username of the user used to connect to the database
     * @var string|null
     */
    protected ?string $user;

    /**
     * Password of the user used to connect to the database
     * @var string|null
     */
    protected ?string $pwd;

    /**
     * Database to connect to.
     * @var string|null
     */
    protected ?string $db;

    /**
     * Port to connect to the database server.
     * @var int
     */
    protected int $port;

    /**
     * Path to the UNIX socket for localhost connection
     * @var string
     */
    protected string $socket;

    /**
     * Instance of the MySQLi class
     * @var MySQLi
     */
    protected $mysqli;

    /**
     * SQL hint to send along with the query.
     * @var string
     */
    protected string $queryHint;

    /**
     * The path name to the key file.
     * @var string|null
     */
    protected ?string $sslKey;

    /**
     * The path name to the certificate file.
     * @var string|null
     */
    protected ?string $sslCert;

    /**
     * The path name to the certificate authority file.
     * @var string|null
     */
    protected ?string $caCert;

    /**
     * The pathname to a directory that contains trusted SSL CA certificates in PEM format.
     * @var string|null
     */
    protected ?string $caPath;

    /**
     * A list of allowable ciphers to use for SSL encryption.
     * @var string|null
     */
    protected ?string $cipher;

    /**
     * Mysqli options.
     * @var array
     */
    protected array $options;

    /**
     * Instance of the MySQLQueryEscaper
     * @var MySQLQueryEscaper
     */
    private readonly MySQLQueryEscaper $escaper;

    /**
     * Limit how often we automatically reconnect after failing to set a charset.
     * @var int
     */
    protected const RECONNECT_LIMIT = 4;

    /**
     * Maximum length of a query before we truncate it to generate a canonical query.
     * @var int
     */
    protected const ANALYTICS_QUERY_LENGTH_LIMIT = 4000;

    /**
     * Constructor.
     *
     * @param MySQLConfigObject|MySQLConfig $config Database config
     * @param LoggerInterface               $logger Shared instance of a logger class
     * @param MySQLi                        $mysqli Instance of the mysqli class
     */
    public function __construct(ArrayAccess|array $config, LoggerInterface $logger, MySQLi $mysqli)
    {
        parent::__construct($logger);

        $this->config = $config;
        $this->mysqli =& $mysqli;

        $this->queryHint                                  = '';
        $this->options[ MYSQLI_OPT_INT_AND_FLOAT_NATIVE ] = TRUE;

        $this->set_configuration();

        mysqli_report($config['errorReporting'] ?? MYSQLI_REPORT_ERROR);
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        if ($this->connected === TRUE)
        {
            $this->rollback();
            $this->disconnect();
        }

        unset($this->mysqli);
        unset($this->rwHost);
        unset($this->roHost);
        unset($this->user);
        unset($this->pwd);
        unset($this->db);
        unset($this->port);
        unset($this->socket);
        unset($this->queryHint);
        unset($this->sslKey);
        unset($this->sslCert);
        unset($this->caCert);
        unset($this->caPath);
        unset($this->cipher);

        parent::__destruct();
    }

    /**
     * Set the config values.
     *
     * @return void
     */
    private function set_configuration(): void
    {
        $this->rwHost  = $this->config['rwHost'];
        $this->roHost  = $this->config['roHost'] ?? $this->config['rwHost'];
        $this->port    = $this->config['port'] ?? (int) (ini_get('mysqli.default_port') ?: 3306 );
        $this->socket  = $this->config['socket'] ?? ini_get('mysqli.default_socket');
        $this->user    = $this->config['username'];
        $this->pwd     = $this->config['password'];
        $this->db      = $this->config['database'];
        $this->sslKey  = $this->config['sslKey'] ?? NULL;
        $this->sslCert = $this->config['sslCert'] ?? NULL;
        $this->caCert  = $this->config['caCert'] ?? NULL;
        $this->caPath  = $this->config['caPath'] ?? NULL;
        $this->cipher  = $this->config['cipher'] ?? NULL;
    }

    /**
     * Establishes a connection to the defined mysql-server.
     *
     * @param int $reconnectCount How often we already tried to connect.
     *
     * @return void
     */
    public function connect(int $reconnectCount = 0): void
    {
        if ($this->connected === TRUE)
        {
            return;
        }

        if ($this->config['driver'] != 'mysql')
        {
            throw new ConnectionException('Cannot connect to a non-mysql database connection!');
        }

        if ($reconnectCount > static::RECONNECT_LIMIT)
        {
            throw new ConnectionException('Could not establish connection to the database! Exceeded reconnect count!');
        }

        $host = ($this->readonly === TRUE) ? $this->roHost : $this->rwHost;

        if (isset($this->sslKey, $this->sslCert, $this->caCert))
        {
            $this->mysqli->ssl_set($this->sslKey, $this->sslCert, $this->caCert, $this->caPath, $this->cipher);
        }

        foreach ($this->options as $key => $value)
        {
            $this->mysqli->options($key, $value);
        }

        // Mysqli::connect() will return NULL on success before PHP 8.1, so we need this for backwards compatibility
        $this->connected = $this->mysqli->connect($host, $this->user, $this->pwd, $this->db, $this->port, $this->socket) !== FALSE;

        if ($this->connected === FALSE || $this->mysqli->connect_errno !== 0)
        {
            throw new ConnectionException('Could not establish connection to the database!');
        }

        if ($this->mysqli->set_charset('utf8mb4') !== FALSE)
        {
            return;
        }

        // manual re-connect
        $this->disconnect();
        $this->connect(++$reconnectCount);
    }

    /**
     * Disconnects from mysql-server.
     *
     * @return void
     */
    public function disconnect(): void
    {
        if ($this->connected !== TRUE)
        {
            return;
        }

        $this->mysqli->close();
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
        $this->connect();

        return $this->mysqli->select_db($db);
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
     * Set option for the current connection.
     *
     * @param int   $key   Mysqli option key.
     * @param mixed $value Mysqli option value.
     *
     * @return bool True on success, False on Failure
     */
    public function set_option(int $key, mixed $value): bool
    {
        if (is_null($value) === TRUE)
        {
            return FALSE;
        }

        $this->options[$key] = $value;

        return TRUE;
    }

    /**
     * Return a new instance of a QueryBuilder object.
     *
     * @param bool $simple Whether to return a simple query builder or an advanced one.
     *
     * @return ($simple is true ? MySQLSimpleDMLQueryBuilder : MySQLDMLQueryBuilder) New DatabaseDMLQueryBuilder object instance
     */
    public function get_new_dml_query_builder_object(bool $simple = TRUE): MySQLDMLQueryBuilder|MySQLSimpleDMLQueryBuilder
    {
        $querybuilder = new MySQLDMLQueryBuilder();
        if ($simple === TRUE)
        {
            return new MySQLSimpleDMLQueryBuilder($querybuilder, $this->get_query_escaper_object());
        }

        return $querybuilder;
    }

    /**
     * Return a new instance of a QueryEscaper object.
     *
     * @return MySQLQueryEscaper New MySQLQueryEscaper object instance
     */
    public function get_query_escaper_object(): MySQLQueryEscaper
    {
        if (isset($this->escaper) === FALSE)
        {
            $this->escaper = new MySQLQueryEscaper($this);
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
        $this->connect();

        return $this->mysqli->escape_string($string);
    }

    /**
     * When running the query on a replication setup, hint to run the next query on the master server.
     *
     * @param string $style What hint style to use.
     *
     * @return static $self Self reference
     */
    public function run_on_master($style = 'maxscale'): static
    {
        switch ($style)
        {
            case 'maxscale':
                $this->queryHint = '/* maxscale route to master */';
                break;
            default:
                break;
        }

        return $this;
    }

    /**
     * When running the query on a replication setup, hint to run the next query on the slave server.
     *
     * @param string $style What hint style to use.
     *
     * @return static $self Self reference
     */
    public function run_on_slave(string $style = 'maxscale'): static
    {
        switch ($style)
        {
            case 'maxscale':
                $this->queryHint = '/* maxscale route to slave */';
                break;
            default:
                break;
        }

        return $this;
    }

    /**
     * Run a SQL query.
     *
     * @param string $sqlQuery The SQL query to run on the database
     *
     * @return MySQLQueryResult $result Query Result
     */
    public function query($sqlQuery): MySQLQueryResult
    {
        $this->connect();

        $profilingHint = '';

        if ($this->analyticsDetailLevel->atLeast(AnalyticsDetailLevel::Info))
        {
            $this->tracingController->startChildSpan();

            $traceID = $this->tracingController->getTraceId();
            $spanID  = $this->tracingController->getSpanId();

            $profilingHint = "/* traceID=$traceID,spanID=$spanID */ ";
        }

        $sqlQuery        = $profilingHint . $this->queryHint . $sqlQuery;
        $this->queryHint = '';

        $this->logger->debug('query: {query}', [ 'query' => $sqlQuery ]);

        $startTimestamp = microtime(TRUE);
        $result         = $this->mysqli->query($sqlQuery);

        $endTimestamp  = microtime(TRUE);
        $executionTime = (float) bcsub((string) $endTimestamp, (string) $startTimestamp, 4);

        $this->logger->debug('Query executed in ' . $executionTime . ' seconds');

        $queryResult = new MySQLQueryResult($sqlQuery, $result, $this->mysqli);

        if (!$this->analyticsDetailLevel->atLeast(AnalyticsDetailLevel::Info))
        {
            return $queryResult;
        }

        if (strlen($sqlQuery) < static::ANALYTICS_QUERY_LENGTH_LIMIT)
        {
            $canonicalQuery = $queryResult->canonical_query();
        }
        else
        {
            $canonicalQuery = (new MySQLCanonicalQuery(substr($sqlQuery, 0, static::ANALYTICS_QUERY_LENGTH_LIMIT)))->get_canonical_query();
        }

        $message  = $queryResult->error_message();
        $warnings = $queryResult->warnings();

        $fields = [
            'startTimestamp' => $startTimestamp,
            'endTimestamp'   => $endTimestamp,
            'executionTime'  => $executionTime,
            'canonicalQuery' => $canonicalQuery,
            'numberOfRows'   => $queryResult->number_of_rows(),
            'errorMessage'   => !empty($message) ? $message : NULL,
            'warnings'       => !empty($warnings) ? json_encode($warnings) : NULL,
            'traceID'        => $traceID,
            'spanID'         => $spanID,
            'parentSpanID'   => $this->tracingController->getParentSpanId(),
        ];
        $tags   = [
            'digest'       => sha1($fields['canonicalQuery']),
            'databaseHost' => $this->getHost(),
            'successful'   => !$queryResult->has_failed(),
            'errorNumber'  => $queryResult->error_number(),
        ];

        if ($this->analyticsDetailLevel->atLeast(AnalyticsDetailLevel::Full))
        {
            $fields['query'] = $queryResult->query();
        }

        $event = $this->eventLogger->newEvent('mysql_query_log');

        $event->recordTimestamp();
        $event->addTags(array_merge($this->tracingController->getSpanSpecificTags(), $tags));
        $event->addFields($fields);
        $event->record();

        $this->tracingController->stopChildSpan();

        return $queryResult;
    }

    /**
     * Run an asynchronous SQL query.
     *
     * @param string $sqlQuery The SQL query to run on the database
     *
     * @return MySQLAsyncQueryResult $result Query Result
     */
    public function async_query(string $sqlQuery): MySQLAsyncQueryResult
    {
        $this->connect();

        $sqlQuery        = $this->queryHint . $sqlQuery;
        $this->queryHint = '';

        $this->logger->debug('query: {query}', [ 'query' => $sqlQuery ]);

        $this->mysqli->query($sqlQuery, MYSQLI_ASYNC);

        return new MySQLAsyncQueryResult($sqlQuery, $this->mysqli);
    }

    /**
     * Begins a transaction.
     *
     * @return bool
     */
    public function begin_transaction(): bool
    {
        $this->connect();

        return $this->mysqli->autocommit(FALSE);
    }

    /**
     * Commits a transaction.
     *
     * @return bool
     */
    public function commit(): bool
    {
        $this->connect();

        return $this->mysqli->commit();
    }

    /**
     * Rolls back a transaction.
     *
     * @return bool
     */
    public function rollback(): bool
    {
        $this->connect();

        return $this->mysqli->rollback();
    }

    /**
     * Ends a transaction.
     *
     * @return bool
     */
    public function end_transaction(): bool
    {
        $this->connect();

        return $this->mysqli->autocommit(TRUE);
    }

    /**
     * Run OPTIMIZE TABLE on a table.
     *
     * @param string $table The table name to defragment.
     *
     * @return void
     */
    public function defragment(string $table): void
    {
        $escaper = $this->get_query_escaper_object();

        $query = $this->query('OPTIMIZE TABLE ' . $escaper->table($table));

        if ($query->has_failed() === TRUE)
        {
            $context = [ 'query' => $query->query(), 'error' => $query->error_message() ];
            $this->logger->error('{query}; failed with error: {error}', $context);

            throw new DefragmentationException($query->error_number(), $query->error_message(), "Failed to optimize table: $table");
        }
    }

    /**
     * Get the hostname of the server the last query ran on.
     *
     * @return string|null Hostname
     */
    protected function getHost(): ?string
    {
        $result = $this->mysqli->query('/* maxscale route to last */ SELECT @@hostname');

        if (!is_object($result))
        {
            return NULL;
        }

        $row = $result->fetch_row();

        if (!is_array($row) || empty($row))
        {
            return NULL;
        }

        return $row[0];
    }

}

?>
