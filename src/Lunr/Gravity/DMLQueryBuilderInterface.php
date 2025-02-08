<?php

/**
 * DML query builder interface.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity;

/**
 * This interface defines the DML query builder primitives.
 */
interface DMLQueryBuilderInterface
{

    /**
     * Construct and return a SELECT query.
     *
     * @return string The constructed query string.
     */
    public function get_select_query(): string;

    /**
     * Construct and return a DELETE query.
     *
     * @return string The constructed query string.
     */
    public function get_delete_query(): string;

    /**
     * Construct and return a INSERT query.
     *
     * @return string The constructed query string.
     */
    public function get_insert_query(): string;

    /**
     * Construct and return a REPLACE query.
     *
     * @return string The constructed query string.
     */
    public function get_replace_query(): string;

    /**
     * Construct and return an UPDATE query.
     *
     * @return string The constructed query string.
     */
    public function get_update_query(): string;

    /**
     * Define the mode of the DELETE clause.
     *
     * @param string $mode The delete mode you want to use
     *
     * @return $this Self reference
     */
    public function delete_mode($mode): static;

    /**
     * Define a DELETE clause.
     *
     * @param string $delete The table references to delete from
     *
     * @return $this Self reference
     */
    public function delete($delete): static;

    /**
     * Define the mode of the INSERT clause.
     *
     * @param string $mode The insert mode you want to use
     *
     * @return $this Self reference
     */
    public function insert_mode($mode): static;

    /**
     * Define the mode of the REPLACE clause.
     *
     * @param string $mode The replace mode you want to use
     *
     * @return $this Self reference
     */
    public function replace_mode($mode): static;

    /**
     * Define INTO clause of the SQL statement.
     *
     * @param string $table Table name
     *
     * @return $this Self reference
     */
    public function into($table): static;

    /**
     * Define a Select statement for Insert statement.
     *
     * @param string $select SQL Select statement to be used in Insert
     *
     * @return $this Self reference
     */
    public function select_statement($select): static;

    /**
     * Define SET clause of the SQL statement.
     *
     * @param array $set Array containing escaped key->value pairs to be set
     *
     * @return $this Self reference
     */
    public function set($set): static;

    /**
     * Define Column names of the affected by Insert or Update SQL statement.
     *
     * @param array $keys Array containing escaped field names to be set
     *
     * @return $this Self reference
     */
    public function column_names($keys): static;

    /**
     * Define Values for Insert or Update SQL statement.
     *
     * @param array $values Array containing escaped values to be set
     *
     * @return $this Self reference
     */
    public function values($values): static;

    /**
     * Define the mode of the SELECT clause.
     *
     * @param string $mode The select mode you want to use
     *
     * @return $this Self reference
     */
    public function select_mode($mode): static;

    /**
     * Define a SELECT clause.
     *
     * @param string|null $select The columns to select
     *
     * @return $this Self reference
     */
    public function select($select): static;

    /**
     * Define FROM clause of the SQL statement.
     *
     * @param string $table_reference Table name
     *
     * @return $this Self reference
     */
    public function from($table_reference): static;

    /**
     * Define JOIN clause of the SQL statement.
     *
     * @param string $table_reference Table reference to join with.
     * @param string $type            Type of JOIN operation to perform.
     *
     * @return $this Self reference
     */
    public function join($table_reference, $type = 'INNER'): static;

    /**
     * Define ON part of a JOIN clause of the SQL statement.
     *
     * @param string $left     Left expression
     * @param string $right    Right expression
     * @param string $operator Comparison operator
     *
     * @return $this Self reference
     */
    public function on($left, $right, $operator = '='): static;

    /**
     * Define ON part of a JOIN clause with LIKE comparator of the SQL statement.
     *
     * @param string $left   Left expression
     * @param string $right  Right expression
     * @param bool   $negate Whether to negate the comparison or not
     *
     * @return $this Self reference
     */
    public function on_like($left, $right, $negate = FALSE): static;

    /**
    * Define ON part of a JOIN clause with IN comparator of the SQL statement.
    *
    * @param string $left   Left expression
    * @param string $right  Right expression
    * @param bool   $negate Whether to negate the comparison or not
    *
    * @return $this Self reference
    */
    public function on_in(string $left, string $right, bool $negate = FALSE): static;

    /**
     * Define ON part of a JOIN clause with BETWEEN comparator of the SQL statement.
     *
     * @param string $left   Left expression
     * @param string $lower  The lower bound of the between condition
     * @param string $upper  The upper bound of the between condition
     * @param bool   $negate Whether to negate the comparison or not
     *
     * @return $this Self reference
     */
    public function on_between($left, $lower, $upper, $negate = FALSE): static;

    /**
    * Define ON part of a JOIN clause with REGEXP comparator of the SQL statement.
    *
    * @param string $left   Left expression
    * @param string $right  Right expression
    * @param bool   $negate Whether to negate the comparison or not
    *
    * @return $this Self reference
    */
    public function on_regexp($left, $right, $negate = FALSE): static;

    /**
     * Define ON part of a JOIN clause with the NULL condition.
     *
     * @param string $left   Left expression
     * @param bool   $negate Whether to negate the condition or not
     *
     * @return $this Self reference
     */
    public function on_null($left, $negate = FALSE): static;

    /**
     * Open ON group.
     *
     * @return $this Self reference
     */
    public function start_on_group(): static;

    /**
     * Close ON group.
     *
     * @return $this Self reference
     */
    public function end_on_group(): static;

    /**
     * Define WHERE clause of the SQL statement.
     *
     * @param string $left     Left expression
     * @param string $right    Right expression
     * @param string $operator Comparison operator
     *
     * @return $this Self reference
     */
    public function where($left, $right, $operator = '='): static;

    /**
     * Open WHERE group.
     *
     * @return $this Self reference
     */
    public function start_where_group(): static;

    /**
     * Close WHERE group.
     *
     * @return $this Self reference
     */
    public function end_where_group(): static;

    /**
     * Define WHERE clause with LIKE comparator of the SQL statement.
     *
     * @param string $left   Left expression
     * @param string $right  Right expression
     * @param bool   $negate Whether to negate the comparison or not
     *
     * @return $this Self reference
     */
    public function where_like($left, $right, $negate = FALSE): static;

    /**
    * Define WHERE clause with the IN condition of the SQL statement.
    *
    * @param string $left   Left expression
    * @param string $right  Right expression
    * @param bool   $negate Whether to negate the condition or not
    *
    * @return $this Self reference
    */
    public function where_in(string $left, string $right, bool $negate = FALSE): static;

    /**
     * Define WHERE clause with the BETWEEN condition of the SQL statement.
     *
     * @param string $left   Left expression
     * @param string $lower  The lower bound of the between condition
     * @param string $upper  The upper bound of the between condition
     * @param bool   $negate Whether to negate the condition or not
     *
     * @return $this Self reference
     */
    public function where_between($left, $lower, $upper, $negate = FALSE): static;

    /**
    * Define WHERE clause with the REGEXP condition of the SQL statement.
    *
    * @param string $left   Left expression
    * @param string $right  Right expression
    * @param bool   $negate Whether to negate the condition or not
    *
    * @return $this Self reference
    */
    public function where_regexp($left, $right, $negate = FALSE): static;

    /**
     * Define WHERE clause with the NULL condition.
     *
     * @param string $left   Left expression
     * @param bool   $negate Whether to negate the condition or not
     *
     * @return $this Self reference
     */
    public function where_null($left, $negate = FALSE): static;

    /**
     * Define a GROUP BY clause of the SQL statement.
     *
     * @param string $expr Expression to group by
     *
     * @return $this Self reference
     */
    public function group_by(string $expr): static;

    /**
     * Define HAVING clause of the SQL statement.
     *
     * @param string $left     Left expression
     * @param string $right    Right expression
     * @param string $operator Comparison operator
     *
     * @return $this Self reference
     */
    public function having($left, $right, $operator = '='): static;

    /**
     * Define HAVING clause with LIKE comparator of the SQL statement.
     *
     * @param string $left   Left expression
     * @param string $right  Right expression
     * @param bool   $negate Whether to negate the comparison or not
     *
     * @return $this Self reference
     */
    public function having_like($left, $right, $negate = FALSE): static;

    /**
    * Define HAVING clause with IN comparator of the SQL statement.
    *
    * @param string $left   Left expression
    * @param string $right  Right expression
    * @param bool   $negate Whether to negate the comparison or not
    *
    * @return $this Self reference
    */
    public function having_in(string $left, string $right, bool $negate = FALSE): static;

    /**
     * Define HAVING clause with BETWEEN comparator of the SQL statement.
     *
     * @param string $left   Left expression
     * @param string $lower  The lower bound of the between condition
     * @param string $upper  The upper bound of the between condition
     * @param bool   $negate Whether to negate the comparison or not
     *
     * @return $this Self reference
     */
    public function having_between($left, $lower, $upper, $negate = FALSE): static;

    /**
    * Define HAVING clause with REGEXP comparator of the SQL statement.
    *
    * @param string $left   Left expression
    * @param string $right  Right expression
    * @param bool   $negate Whether to negate the comparison or not
    *
    * @return $this Self reference
    */
    public function having_regexp($left, $right, $negate = FALSE): static;

    /**
     * Define HAVING clause with the NULL condition.
     *
     * @param string $left   Left expression
     * @param bool   $negate Whether to negate the condition or not
     *
     * @return $this Self reference
     */
    public function having_null($left, $negate = FALSE): static;

    /**
     * Open HAVING group.
     *
     * @return $this Self reference
     */
    public function start_having_group(): static;

    /**
     * Close HAVING group.
     *
     * @return $this Self reference
     */
    public function end_having_group(): static;

    /**
     * Define a ORDER BY clause of the SQL statement.
     *
     * @param string $expr Expression to order by
     * @param bool   $asc  Order ASCending/TRUE or DESCending/FALSE
     *
     * @return $this Self reference
     */
    public function order_by($expr, $asc = TRUE): static;

    /**
     * Define a LIMIT clause of the SQL statement.
     *
     * @param int $amount The amount of elements to retrieve
     * @param int $offset Start retrieving elements from a specific index
     *
     * @return $this Self reference
     */
    public function limit($amount, $offset = -1): static;

    /**
     * Define a UNION or UNION ALL clause of the SQL statement.
     *
     * @param string $sql_query SQL query reference
     * @param string $type      Type of UNION operation to perform.
     *
     * @return $this Self reference
     */
    public function union(string $sql_query, string $type): static;

    /**
     * Define the lock mode for a transaction.
     *
     * @param string $mode The lock mode you want to use
     *
     * @return $this Self reference
     */
    public function lock_mode($mode): static;

    /**
     * Set logical connector 'AND'.
     *
     * @return $this Self reference
     */
    public function sql_and(): static;

    /**
     * Set logical connector 'OR'.
     *
     * @return $this Self reference
     */
    public function sql_or(): static;

    /**
     * Define a with clause.
     *
     * @param string $alias        The alias of the WITH statement
     * @param string $sql_query    Sql query reference
     * @param array  $column_names An optional parameter to give the result columns a name
     *
     * @return $this Self reference
     */
    public function with($alias, $sql_query, $column_names = NULL): static;

    /**
     * Define a recursive WITH clause.
     *
     * @param string $alias           The alias of the WITH statement
     * @param string $anchor_query    The initial select statement
     * @param string $recursive_query The select statement that selects recursively out of the initial query
     * @param bool   $union_all       True for UNION ALL false for UNION
     * @param array  $column_names    An optional parameter to give the result columns a name
     *
     * @return $this Self reference
     */
    public function with_recursive($alias, $anchor_query, $recursive_query, $union_all = FALSE, $column_names = NULL): static;

}

?>
