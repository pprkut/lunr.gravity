<?php

/**
 * Abstract database connection class.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity;

use Lunr\Ticks\AnalyticsDetailLevel;
use Lunr\Ticks\EventLogging\EventLoggerInterface;
use Lunr\Ticks\TracingControllerInterface;
use Lunr\Ticks\TracingInfoInterface;
use Psr\Log\LoggerInterface;

/**
 * This class defines abstract database access.
 *
 * @phpstan-type DatabaseConfig array{
 *  'driver': string,
 *  'errorReporting'?: int|bool
 * }
 */
abstract class DatabaseConnection implements DatabaseStringEscaperInterface
{

    /**
     * Connection status
     * @var bool
     */
    protected bool $connected;

    /**
     * Whether there's write access to the database or not
     * @var bool
     */
    protected bool $readonly;

    /**
     * The detail level for query profiling
     * @var AnalyticsDetailLevel
     */
    protected AnalyticsDetailLevel $analyticsDetailLevel;

    /**
     * Shared instance of a Logger class
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * Shared instance of an EventLogger class
     * @var EventLoggerInterface
     */
    protected readonly EventLoggerInterface $eventLogger;

    /**
     * Shared instance of a tracing controller
     * @var TracingControllerInterface&TracingInfoInterface
     */
    protected readonly TracingControllerInterface&TracingInfoInterface $tracingController;

    /**
     * Constructor.
     *
     * @param LoggerInterface $logger Shared instance of a logger class
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->connected = FALSE;
        $this->readonly  = FALSE;

        $this->analyticsDetailLevel = AnalyticsDetailLevel::None;

        $this->logger = $logger;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->logger);
        unset($this->readonly);
        unset($this->analyticsDetailLevel);
        unset($this->connected);
    }

    /**
     * Toggle readonly flag on the connection.
     *
     * @param bool $switch Whether to make the connection readonly or not
     *
     * @return void
     */
    public function set_readonly(bool $switch): void
    {
        $this->readonly = $switch;
    }

    /**
     * Enable SQL query profiling.
     *
     * @param EventLoggerInterface                            $eventLogger Instance of an event logger
     * @param TracingControllerInterface&TracingInfoInterface $controller  Instance of a tracing controller
     * @param AnalyticsDetailLevel                            $level       Analytics detail level (defaults to Info)
     *
     * @return void
     */
    public function enableAnalytics(
        EventLoggerInterface $eventLogger,
        TracingControllerInterface&TracingInfoInterface $controller,
        AnalyticsDetailLevel $level = AnalyticsDetailLevel::Info,
    ): void
    {
        $this->eventLogger          = $eventLogger;
        $this->tracingController    = $controller;
        $this->analyticsDetailLevel = $level;
    }

    /**
     * Establishes a connection to the defined database server.
     *
     * @return void
     */
    abstract public function connect(): void;

    /**
     * Disconnects from database server.
     *
     * @return void
     */
    abstract public function disconnect(): void;

    /**
     * Change the default database for the current connection.
     *
     * @param string $db New default database
     *
     * @return bool True on success, False on Failure
     */
    abstract public function change_database(string $db): bool;

    /**
     * Get the name of the database we're currently connected to.
     *
     * @return string Database name
     */
    abstract public function get_database(): string;

    /**
     * Return a new instance of a QueryBuilder object.
     *
     * @return DatabaseDMLQueryBuilder $builder New DatabaseDMLQueryBuilder object instance
     */
    abstract public function get_new_dml_query_builder_object();

    /**
     * Return a new instance of a QueryEscaper object.
     *
     * @return DatabaseQueryEscaper New DatabaseQueryEscaper object instance
     */
    abstract public function get_query_escaper_object(): DatabaseQueryEscaper;

    /**
     * Escape a string to be used in a SQL query.
     *
     * @param string $string The string to escape
     *
     * @return string The escaped string
     */
    abstract public function escape_string(string $string): string;

    /**
     * Run a SQL query.
     *
     * @param string $sqlQuery The SQL query to run on the database
     *
     * @return DatabaseQueryResultInterface $result Query Result
     */
    abstract public function query(string $sqlQuery): DatabaseQueryResultInterface;

    /**
     * Begin a transaction.
     *
     * @return bool
     */
    abstract public function begin_transaction(): bool;

    /**
     * Commit a transaction.
     *
     * @return bool
     */
    abstract public function commit(): bool;

    /**
     * Roll back a transaction.
     *
     * @return bool
     */
    abstract public function rollback(): bool;

    /**
     * Ends a transaction.
     *
     * @return bool
     */
    abstract public function end_transaction(): bool;

    /**
     * Run OPTIMIZE TABLE on a table.
     *
     * @param string $table The table to defragment.
     *
     * @return void
     */
    abstract public function defragment(string $table): void;

}

?>
