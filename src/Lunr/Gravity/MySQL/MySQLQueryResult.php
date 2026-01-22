<?php

/**
 * MySQL query result class.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\MySQL;

use Lunr\Gravity\DatabaseQueryResultInterface;
use MySQLi;

/**
 * MySQL/MariaDB query result class.
 */
class MySQLQueryResult implements DatabaseQueryResultInterface
{
    /**
     * The MySQL error code for transaction deadlock.
     * @var int
     */
    private const DEADLOCK_ERR_CODE = 1213;

    /**
     * The MySQL error code for transaction lock timeout.
     * @var int
     */
    private const LOCK_TIMEOUT_ERR_CODE = 1205;

    /**
     * The query string that was executed.
     * @var string
     */
    protected $query;

    /**
     * The canonicalized query string that was executed.
     * @var string
     */
    protected readonly string $canonicalQuery;

    /**
     * Return value from mysqli->query().
     * @var mixed
     */
    protected $result;

    /**
     * Shared instance of the mysqli class.
     * @var MySQLi
     */
    protected $mysqli;

    /**
     * Flag whether the query was successful or not.
     * @var bool
     */
    protected $success;

    /**
     * Flag whether the memory has been freed or not.
     * @var bool
     */
    protected $freed;

    /**
     * Description of the error.
     * @var string
     */
    protected $errorMessage;

    /**
     * Error code.
     * @var int
     */
    protected $errorNumber;

    /**
     * Warnings from Mysqli or NULL if no warnings
     * @var ?array
     */
    protected $warnings;

    /**
     * Autoincremented ID generated on last insert.
     * @var mixed
     */
    protected $insertID;

    /**
     * Number of affected rows.
     * @var int|numeric-string
     */
    protected int|string $affectedRows;

    /**
     * Number of rows in the result set.
     * @var int|numeric-string
     */
    protected int|string $numRows;

    /**
     * Constructor.
     *
     * @param string $query  Executed query
     * @param mixed  $result Query result
     * @param MySQLi $mysqli Shared instance of the MySQLi class
     * @param bool   $async  Whether this query was run asynchronous or not
     */
    public function __construct($query, $result, $mysqli, $async = FALSE)
    {
        if (is_object($result))
        {
            $this->success = TRUE;
            $this->freed   = FALSE;
        }
        else
        {
            $this->success = $result;
            $this->freed   = TRUE;
        }

        $this->result = $result;
        $this->mysqli = $mysqli;
        $this->query  = $query;

        if ($async !== FALSE)
        {
            return;
        }

        $this->errorMessage = $this->mysqli->error;
        $this->errorNumber  = $this->mysqli->errno;
        $this->insertID     = $this->mysqli->insert_id;
        $this->affectedRows = mysqli_affected_rows($this->mysqli);
        $this->numRows      = is_object($this->result) ? mysqli_num_rows($result) : $this->affectedRows;

        $this->set_warnings();
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        $this->free_result();

        unset($this->mysqli);
        unset($this->result);
        unset($this->success);
        unset($this->freed);
        unset($this->errorMessage);
        unset($this->errorNumber);
        unset($this->insertID);
        unset($this->query);
        unset($this->warnings);
    }

    /**
     * Free memory associated with a result.
     *
     * @return void
     */
    protected function free_result()
    {
        if ($this->freed !== FALSE)
        {
            return;
        }

        $this->result->free();
        $this->freed = TRUE;
    }

    /**
     * Check whether the query has failed or not.
     *
     * @return bool $return TRUE if it failed, FALSE otherwise
     */
    public function has_failed()
    {
        return !$this->success;
    }

    /**
     * Check whether the query has a deadlock or not.
     *
     * @return bool $return TRUE if it failed, FALSE otherwise
     */
    public function has_deadlock()
    {
        return ($this->errorNumber == self::DEADLOCK_ERR_CODE) ? TRUE : FALSE;
    }

    /**
     * Check whether the query has a lock timeout or not.
     *
     * @return bool the timeout lock status for the query
     */
    public function has_lock_timeout()
    {
        return $this->errorNumber == self::LOCK_TIMEOUT_ERR_CODE;
    }

    /**
     * Get string description of the error, if there was one.
     *
     * @return string $message Error Message
     */
    public function error_message()
    {
        return $this->errorMessage;
    }

    /**
     * Get numerical error code of the error, if there was one.
     *
     * @return int $code Error Code
     */
    public function error_number()
    {
        return $this->errorNumber;
    }

    /**
     * Get array of mysqli_warning, if there are any
     *
     * @return ?array $warnings If there are warnings it's an array of mysqli_warning
     *                         otherwise its NULL
     */
    public function warnings()
    {
        return $this->warnings;
    }

    /**
     * Set the warnings property
     *
     * @return void
     */
    protected function set_warnings()
    {
        $mysqliWarnings = $this->mysqli->get_warnings();

        if ($mysqliWarnings == FALSE)
        {
            $this->warnings = NULL;
            return;
        }

        do
        {
            $warning['message']  = $mysqliWarnings->message;
            $warning['sqlstate'] = $mysqliWarnings->sqlstate;
            $warning['errno']    = $mysqliWarnings->errno;

            $this->warnings[] = $warning;
        }
        while ($mysqliWarnings->next());
    }

    /**
     * Get autoincremented ID generated on last insert.
     *
     * @return int|string $id If the number is greater than maximal int value it's a string
     *                        otherwise an int
     */
    public function insert_id()
    {
        return $this->insertID;
    }

    /**
     * Get the executed query.
     *
     * @return string $query The executed query
     */
    public function query()
    {
        return $this->query;
    }

    /**
     * Returns the number of rows affected by the last query.
     *
     * @return int|numeric-string Number of rows in the result set.
     *                            This is usually an integer, unless the number is > MAXINT.
     *                            Then it is a string.
     */
    public function affected_rows(): int|string
    {
        return $this->affectedRows;
    }

    /**
     * Returns the number of rows in the result set.
     *
     * @return int|numeric-string Number of rows in the result set.
     *                            This is usually an integer, unless the number is > MAXINT.
     *                            Then it is a string.
     */
    public function number_of_rows(): int|string
    {
        return $this->numRows;
    }

    /**
     * Get the entire result set as an array.
     *
     * @param bool $associative TRUE for returning rows as associative arrays,
     *                          FALSE for returning rows as enumerated arrays
     *
     * @return list<array<string, scalar|null>> Result set as array
     */
    public function result_array(bool $associative = TRUE): array
    {
        $output = [];

        $returnType = $associative ? MYSQLI_ASSOC : MYSQLI_NUM;

        if (!is_object($this->result))
        {
            return $output;
        }

        $output = $this->result->fetch_all($returnType);

        $this->free_result();

        return $output;
    }

    /**
     * Get the first row of the result set.
     *
     * @return array<string, scalar|null> First result row as array
     */
    public function result_row(): array
    {
        $output = is_object($this->result) ? $this->result->fetch_assoc() : [];

        $this->free_result();

        return $output;
    }

    /**
     * Get a specific column of the result set.
     *
     * @param string $column Column or Alias name
     *
     * @return list<scalar|null> Result column as array
     */
    public function result_column(string $column): array
    {
        $output = [];

        if (!is_object($this->result))
        {
            return $output;
        }

        while ($row = $this->result->fetch_assoc())
        {
            $output[] = $row[$column];
        }

        $this->free_result();

        return $output;
    }

    /**
     * Get a specific column of the first row of the result set.
     *
     * @param string $column Column or Alias name
     *
     * @return scalar|null NULL if it does not exist, the value otherwise
     */
    public function result_cell(string $column): bool|float|int|string|null
    {
        if (!is_object($this->result))
        {
            return NULL;
        }

        $line = $this->result->fetch_assoc();

        $this->free_result();

        return isset($line[$column]) ? $line[$column] : NULL;
    }

    /**
     * Get the executed query canonicalized.
     *
     * @return string $canonicalized_query The executed query canonicalized
     */
    public function canonical_query(): string
    {
        if (isset($this->canonicalQuery) === FALSE)
        {
            $this->canonicalQuery = new MySQLCanonicalQuery($this->query());
        }

        return $this->canonicalQuery;
    }

}

?>
