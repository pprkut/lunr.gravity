<?php

/**
 * MySQL asynchronous query result class.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\MySQL;

use MySQLi;

/**
 * MySQL/MariaDB asynchronous query result class.
 */
class MySQLAsyncQueryResult extends MySQLQueryResult
{

    /**
     * Flag whether the result was fetched or not.
     * @var bool
     */
    protected $fetched;

    /**
     * Constructor.
     *
     * @param string $query  Executed query
     * @param MySQLi $mysqli Shared instance of the MySQLi class
     */
    public function __construct($query, $mysqli)
    {
        parent::__construct($query, FALSE, $mysqli, TRUE);
        $this->fetched = FALSE;
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->fetched);

        parent::__destruct();
    }

    /**
     * Retrieve results for an asynchronous query.
     *
     * @return void
     */
    protected function fetch_result()
    {
        if ($this->fetched === TRUE)
        {
            return;
        }

        $this->result = $this->mysqli->reap_async_query();

        if (is_object($this->result))
        {
            $this->success = TRUE;
            $this->freed   = FALSE;
        }
        else
        {
            $this->success = $this->result;
            $this->freed   = TRUE;
        }

        $this->fetched = TRUE;

        $this->errorMessage = $this->mysqli->error;
        $this->errorNumber  = $this->mysqli->errno;
        $this->insertID     = $this->mysqli->insert_id;
        $this->affectedRows = mysqli_affected_rows($this->mysqli);
        $this->numRows      = is_object($this->result) ? mysqli_num_rows($this->result) : $this->affectedRows;
    }

    /**
     * Check whether the query has failed or not.
     *
     * @return bool $return TRUE if it failed, FALSE otherwise
     */
    public function has_failed()
    {
        $this->fetch_result();
        return parent::has_failed();
    }

    /**
     * Get string description of the error, if there was one.
     *
     * @return string $message Error Message
     */
    public function error_message()
    {
        $this->fetch_result();
        return parent::error_message();
    }

    /**
     * Get numerical error code of the error, if there was one.
     *
     * @return int $code Error Code
     */
    public function error_number()
    {
        $this->fetch_result();
        return parent::error_number();
    }

    /**
     * Get autoincremented ID generated on last insert.
     *
     * @return mixed $id If the number is greater than maximal int value it's a String
     *                   otherwise an Integer
     */
    public function insert_id()
    {
        $this->fetch_result();
        return parent::insert_id();
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
        $this->fetch_result();
        return parent::affected_rows();
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
        $this->fetch_result();
        return parent::number_of_rows();
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
        $this->fetch_result();
        return parent::result_array($associative);
    }

    /**
     * Get the first row of the result set.
     *
     * @return array<string, scalar|null> First result row as array
     */
    public function result_row(): array
    {
        $this->fetch_result();
        return parent::result_row();
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
        $this->fetch_result();
        return parent::result_column($column);
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
        $this->fetch_result();
        return parent::result_cell($column);
    }

}

?>
