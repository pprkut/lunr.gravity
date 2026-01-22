<?php

/**
 * Abstract database access class.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity;

use Lunr\Gravity\Exceptions\DeadlockException;
use Lunr\Gravity\Exceptions\LockTimeoutException;
use Lunr\Gravity\Exceptions\QueryException;
use Psr\Log\LoggerInterface;

/**
 * This class provides a way to access databases.
 */
abstract class DatabaseAccessObject
{

    /**
     * Database connection handler.
     * @var DatabaseConnection
     */
    private DatabaseConnection $db;

    /**
     * Shared instance of a Logger class.
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * Constructor.
     *
     * @param DatabaseConnection $connection Shared instance of a database connection class
     * @param LoggerInterface    $logger     Shared instance of a Logger class
     */
    public function __construct(DatabaseConnection $connection, LoggerInterface $logger)
    {
        $this->db     = $connection;
        $this->logger = $logger;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->db);
        unset($this->logger);
    }

    /**
     * Swap the currently used database connection with a new one.
     *
     * @param DatabaseConnection $connection Shared instance of a database connection class
     *
     * @return void
     */
    protected function swap_generic_connection(DatabaseConnection $connection): void
    {
        $this->db = $connection;
    }

    /**
     * Throws an exception in case the query failed.
     *
     * @param DatabaseQueryResultInterface $query The result of the run query
     *
     * @return void
     */
    public function verify_query_success(DatabaseQueryResultInterface $query): void
    {
        $warnings = $query->warnings();

        if ($warnings !== NULL)
        {
            $warningsString = '{query}; had {warning_count} warnings:';
            $formatString   = "\n%s (%d): %s";

            foreach ($warnings as $warning)
            {
                $warningsString .= sprintf($formatString, $warning['sqlstate'], $warning['errno'], $warning['message']);
            }

            $context = [ 'query' => $query->query(), 'warning_count' => count($warnings) ];
            $this->logger->warning($warningsString, $context);
        }

        if ($query->has_failed() !== TRUE)
        {
            return;
        }

        $context = [ 'query' => $query->query(), 'error' => $query->error_message() ];
        $this->logger->error('{query}; failed with error: {error}', $context);

        if ($query->has_lock_timeout() === TRUE)
        {
            throw new LockTimeoutException($query, 'Database query lock timeout!');
        }

        if ($query->has_deadlock() === TRUE)
        {
            throw new DeadlockException($query, 'Database query deadlock!');
        }

        throw new QueryException($query, 'Database query error!');
    }

    /**
     * Get affected rows of the run query.
     *
     * @param DatabaseQueryResultInterface $query The result of the run query
     *
     * @return int|numeric-string Number of affected rows in the result set
     */
    protected function get_affected_rows(DatabaseQueryResultInterface $query): int|string
    {
        $this->verify_query_success($query);

        return $query->affected_rows();
    }

    /**
     * Get query result as an indexed array.
     *
     * @param DatabaseQueryResultInterface $query  The result of the run query
     * @param string                       $column Column to use as index
     *
     * @return array<string, array<string, scalar|null>> Indexed result array
     */
    protected function indexed_result_array(DatabaseQueryResultInterface $query, string $column): array
    {
        $this->verify_query_success($query);

        if ($query->number_of_rows() == 0)
        {
            return [];
        }

        $result = [];

        foreach ($query->result_array() as $row)
        {
            $result[$row[$column]] = $row;
        }

        return $result;
    }

    /**
     * Get query result as array.
     *
     * @param DatabaseQueryResultInterface $query       The result of the run query
     * @param bool                         $associative TRUE for returning rows as associative arrays,
     *                                                  FALSE for returning rows as enumerated arrays
     *
     * @return list<array<string, scalar|null>> Result array
     */
    protected function result_array(DatabaseQueryResultInterface $query, bool $associative = TRUE): array
    {
        $this->verify_query_success($query);

        if ($query->number_of_rows() == 0)
        {
            return [];
        }

        return $query->result_array($associative);
    }

    /**
     * Get first row of query result.
     *
     * @param DatabaseQueryResultInterface $query The result of the run query
     *
     * @return array<string, scalar|null> Result array
     */
    protected function result_row(DatabaseQueryResultInterface $query): array
    {
        $this->verify_query_success($query);

        if ($query->number_of_rows() == 0)
        {
            return [];
        }

        return $query->result_row();
    }

    /**
     * Get specific column of query result.
     *
     * @param DatabaseQueryResultInterface $query  The result of the run query
     * @param string                       $column The title of the requested column
     *
     * @return list<scalar|null> Result array
     */
    protected function result_column(DatabaseQueryResultInterface $query, string $column): array
    {
        $this->verify_query_success($query);

        if ($query->number_of_rows() == 0)
        {
            return [];
        }

        return $query->result_column($column);
    }

    /**
     * Get specific cell of the first row of the query result.
     *
     * @param DatabaseQueryResultInterface $query The result of the run query
     * @param string                       $cell  The title of the requested cell
     *
     * @return scalar|null Result value
     */
    protected function result_cell(DatabaseQueryResultInterface $query, string $cell): bool|float|int|string|null
    {
        $this->verify_query_success($query);

        if ($query->number_of_rows() == 0)
        {
            return '';
        }

        return $query->result_cell($cell);
    }

    /**
     * Retry executing the query in case of deadlock error.
     *
     * @param DatabaseQueryResultInterface $query      The result of the run query
     * @param int                          $retryCount The max amount of re-executing the query
     *
     * @return DatabaseQueryResultInterface Result value
     */
    protected function result_retry(DatabaseQueryResultInterface $query, int $retryCount = 5): DatabaseQueryResultInterface
    {
        for ($i = 0; $i < $retryCount; $i++)
        {
            if ($query->has_deadlock() === FALSE && $query->has_lock_timeout() === FALSE)
            {
                return $query;
            }

            $query = $this->db->query($query->query());
        }

        return $query;
    }

    /**
     * Check whether the query has failed or not.
     *
     * @param DatabaseQueryResultInterface $query The result of the run query
     *
     * @return bool TRUE on success
     */
    protected function result_boolean(DatabaseQueryResultInterface $query): bool
    {
        $this->verify_query_success($query);

        return TRUE;
    }

    /**
     * Trigger defragmentation operation for the specified table.
     *
     * @param string $table The table to defragment.
     *
     * @return void
     */
    public function defragment(string $table): void
    {
        $this->db->defragment($table);
    }

}

?>
