<?php

/**
 * MySQL/MariaDB database query builder class.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\MySQL;

use Lunr\Gravity\SQLDMLQueryBuilder;

/**
 * This is a SQL query builder class for generating queries
 * suitable for either MySQL or MariaDB.
 */
class MySQLDMLQueryBuilder extends SQLDMLQueryBuilder
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        parent::__destruct();
    }

    /**
     * Define the mode of the DELETE clause.
     *
     * @param string $mode The delete mode you want to use
     *
     * @return $this Self reference
     */
    public function delete_mode($mode): static
    {
        $mode = strtoupper($mode);

        switch ($mode)
        {
            case 'LOW_PRIORITY':
            case 'QUICK':
            case 'IGNORE':
                $this->delete_mode[] = $mode;
            default:
                break;
        }

        return $this;
    }

    /**
     * Define the mode of the INSERT clause.
     *
     * @param string $mode The insert mode you want to use
     *
     * @return $this Self reference
     */
    public function insert_mode($mode): static
    {
        $mode = strtoupper($mode);

        switch ($mode)
        {
            case 'IGNORE':
                $this->insert_mode['errors'] = $mode;
                break;
            case 'HIGH_PRIORITY':
            case 'LOW_PRIORITY':
            case 'DELAYED':
                $this->insert_mode['priority'] = $mode;
            default:
                break;
        }

        return $this;
    }

    /**
     * Define the mode of the REPLACE clause.
     *
     * @param string $mode The replace mode you want to use
     *
     * @return $this Self reference
     */
    public function replace_mode($mode): static
    {
        return $this->insert_mode($mode);
    }

    /**
     * Define the mode of the SELECT clause.
     *
     * @param string $mode The select mode you want to use
     *
     * @return $this Self reference
     */
    public function select_mode($mode): static
    {
        $mode = strtoupper($mode);

        switch ($mode)
        {
            case 'ALL':
            case 'DISTINCT':
            case 'DISTINCTROW':
                $this->select_mode['duplicates'] = $mode;
                break;
            case 'SQL_CACHE':
            case 'SQL_NO_CACHE':
                $this->select_mode['cache'] = $mode;
                break;
            case 'HIGH_PRIORITY':
            case 'STRAIGHT_JOIN':
            case 'SQL_SMALL_RESULT':
            case 'SQL_BIG_RESULT':
            case 'SQL_BUFFER_RESULT':
            case 'SQL_CALC_FOUND_ROWS':
                $this->select_mode[] = $mode;
            default:
                break;
        }

        return $this;
    }

    /**
     * Define the mode of the UPDATE clause.
     *
     * @param string $mode The update mode you want to use
     *
     * @return $this Self reference
     */
    public function update_mode($mode): static
    {
        $mode = strtoupper($mode);

        switch ($mode)
        {
            case 'LOW_PRIORITY':
            case 'IGNORE':
                $this->update_mode[] = $mode;
            default:
                break;
        }

        return $this;
    }

    /**
     * Define ON part of a JOIN clause with REGEXP comparator of the SQL statement.
     *
     * @param string $left   Left expression
     * @param string $right  Right expression
     * @param bool   $negate Whether to negate the comparison or not
     *
     * @return $this Self reference
     */
    public function on_regexp($left, $right, $negate = FALSE): static
    {
        $operator = ($negate === FALSE) ? 'REGEXP' : 'NOT REGEXP';
        $this->sql_condition($left, $right, $operator, 'ON');
        return $this;
    }

    /**
     * Define WHERE clause with the REGEXP condition of the SQL statement.
     *
     * @param string $left   Left expression
     * @param string $right  Right expression
     * @param bool   $negate Whether to negate the condition or not
     *
     * @return $this Self reference
     */
    public function where_regexp($left, $right, $negate = FALSE): static
    {
        $operator = ($negate === FALSE) ? 'REGEXP' : 'NOT REGEXP';
        $this->sql_condition($left, $right, $operator);
        return $this;
    }

    /**
     * Define GROUP BY clause of the SQL statement.
     *
     * @param string    $expr  Expression to group by
     * @param bool|null $order Order ASCending/TRUE or DESCending/FALSE, default no order/NULL
     *
     * @return $this Self reference
     */
    public function group_by(string $expr, ?bool $order = NULL): static
    {
        $this->sql_group_by($expr);

        if ($order !== NULL)
        {
            $direction       = ($order === TRUE) ? ' ASC' : ' DESC';
            $this->group_by .= $direction;
        }

        return $this;
    }

    /**
     * Define HAVING clause with REGEXP comparator of the SQL statement.
     *
     * @param string $left   Left expression
     * @param string $right  Right expression
     * @param bool   $negate Whether to negate the comparison or not
     *
     * @return $this Self reference
     */
    public function having_regexp($left, $right, $negate = FALSE): static
    {
        $operator = ($negate === FALSE) ? 'REGEXP' : 'NOT REGEXP';
        $this->sql_condition($left, $right, $operator, 'HAVING');
        return $this;
    }

    /**
     * Define the lock mode for a transaction.
     *
     * @param string $mode The lock mode you want to use
     *
     * @return $this Self reference
     */
    public function lock_mode($mode): static
    {
        $mode = strtoupper($mode);

        switch ($mode)
        {
            case 'FOR UPDATE':
            case 'LOCK IN SHARE MODE':
                $this->lock_mode = $mode;
            default:
                break;
        }

        return $this;
    }

    /**
     * Set logical connector 'XOR'.
     *
     * @deprecated Use `xor()` instead
     *
     * @return $this Self reference
     */
    public function sql_xor(): static
    {
        return $this->xor();
    }

    /**
     * Set logical connector 'XOR'.
     *
     * @return $this Self reference
     */
    public function xor(): static
    {
        $this->sql_connector('XOR');
        return $this;
    }

    /**
     * Set ON DUPLICATE KEY UPDATE clause.
     *
     * @param string $set Action to perform on conflict
     *
     * @return $this Self reference
     */
    public function on_duplicate_key_update($set): static
    {
        $this->sql_upsert('ON DUPLICATE KEY UPDATE', $set);
        return $this;
    }

}

?>
