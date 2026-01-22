<?php

/**
 * Database query result interface.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity;

/**
 * Database query result interface.
 */
interface DatabaseQueryResultInterface
{

    /**
     * Check whether the query has failed or not.
     *
     * @return bool $return TRUE if it failed, FALSE otherwise
     */
    public function has_failed();

    /**
     * Check whether the query has a deadlock or not.
     *
     * @return bool $return TRUE if it failed, FALSE otherwise
     */
    public function has_deadlock();

    /**
     * Check whether the query has a lock timeout or not.
     *
     * @return bool the timeout lock status for the query
     */
    public function has_lock_timeout();

    /**
     * Get string description of the error, if there was one.
     *
     * @return string $message Error Message
     */
    public function error_message();

    /**
     * Get numerical error code of the error, if there was one.
     *
     * @return int $code Error Code
     */
    public function error_number();

    /**
     * Get array of mysqli_warning, if there are any
     *
     * @return ?array $warnings If there are warnings it's an array of mysqli_warning
     *                          otherwise its NULL
     */
    public function warnings();

    /**
     * Get autoincremented ID generated on last insert.
     *
     * @return int|string $id If the number is greater than maximal int value it's a String
     *                        otherwise an Integer
     */
    public function insert_id();

    /**
     * Get the executed query.
     *
     * @return string $query The executed query
     */
    public function query();

    /**
     * Returns the number of rows affected by the last query.
     *
     * @return int|numeric-string Number of affected rows in the result set.
     */
    public function affected_rows(): int|string;

    /**
     * Returns the number of rows in the query.
     *
     * @return int|numeric-string Number of rows in the result set.
     */
    public function number_of_rows(): int|string;

    /**
     * Get the entire result set as an array.
     *
     * @param bool $associative TRUE for returning rows as associative arrays,
     *                          FALSE for returning rows as enumerated arrays
     *
     * @return list<array<string, scalar|null>> Result set as array
     */
    public function result_array(bool $associative = TRUE): array;

    /**
     * Get the first row of the result set.
     *
     * @return array<string, scalar|null> First result row as array
     */
    public function result_row(): array;

    /**
     * Get a specific column of the result set.
     *
     * @param string $column Column or Alias name
     *
     * @return list<scalar|null> Result column as array
     */
    public function result_column(string $column): array;

    /**
     * Get a specific column of the first row of the result set.
     *
     * @param string $column Column or Alias name
     *
     * @return scalar|null NULL if it does not exist, the value otherwise
     */
    public function result_cell(string $column): bool|float|int|string|null;

}

?>
