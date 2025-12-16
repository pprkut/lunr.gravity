<?php

/**
 * This file contains the DatabaseAccessObjectSelectQueryTestTrait.
 *
 * SPDX-FileCopyrightText: Copyright 2014 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity\Tests\Helpers;

use Lunr\Gravity\Exceptions\QueryException;

/**
 * This trait contains helper methods to test general success and error cases of SELECT queries.
 */
trait DatabaseAccessObjectQueryTestTrait
{

    /**
     * Expect that a query returns successful results.
     *
     * @param mixed  $data   Result data
     * @param string $format Return result as 'array', 'row', 'column' or 'cell'
     *
     * @return void
     */
    public function expectResultOnSuccess($data, $format = 'array'): void
    {
        if (property_exists($this, 'realSimpleBuilder'))
        {
            $this->db->shouldReceive('get_new_dml_query_builder_object')
                     ->atLeast()
                     ->once()
                     ->andReturnUsing(fn(bool $argument = TRUE) => $argument ? $this->realSimpleBuilder : $this->realBuilder);
        }
        else
        {
            $this->db->shouldReceive('get_new_dml_query_builder_object')
                     ->atLeast()
                     ->once()
                     ->andReturn($this->realBuilder);
        }

        $this->db->shouldReceive('query')
                 ->once()
                 ->andReturn($this->result);

        $this->result->shouldReceive('warnings')
                     ->once()
                     ->andReturn(NULL);

        $this->result->shouldReceive('has_failed')
                     ->once()
                     ->andReturn(FALSE);

        $count = $format === 'cell' ? 1 : count($data);

        $this->result->shouldReceive('number_of_rows')
                     ->once()
                     ->andReturn($count);

        $this->result->shouldReceive('result_' . $format)
                     ->once()
                     ->andReturn($data);
    }

    /**
     * Expect that a query returns no results.
     *
     * @param string $format Return result as 'array', 'row', 'column' or 'cell'
     *
     * @return void
     */
    public function expectNoResultsFound($format = 'array'): void
    {
        if (property_exists($this, 'realSimpleBuilder'))
        {
            $this->db->shouldReceive('get_new_dml_query_builder_object')
                     ->atLeast()
                     ->once()
                     ->andReturnUsing(fn(bool $argument = TRUE) => $argument ? $this->realSimpleBuilder : $this->realBuilder);
        }
        else
        {
            $this->db->shouldReceive('get_new_dml_query_builder_object')
                     ->atLeast()
                     ->once()
                     ->andReturn($this->realBuilder);
        }

        $this->db->shouldReceive('query')
                 ->once()
                 ->andReturn($this->result);

        $this->result->shouldReceive('warnings')
                     ->once()
                     ->andReturn(NULL);

        $this->result->shouldReceive('has_failed')
                     ->once()
                     ->andReturn(FALSE);

        $this->result->shouldReceive('number_of_rows')
                     ->once()
                     ->andReturn(0);

        $this->result->shouldReceive('result_' . $format)
                     ->never();
    }

    /**
     * Expect that a query returns an error.
     *
     * @return void
     */
    protected function expectQueryError(): void
    {
        if (property_exists($this, 'realSimpleBuilder'))
        {
            $this->db->shouldReceive('get_new_dml_query_builder_object')
                     ->atLeast()
                     ->once()
                     ->andReturnUsing(fn(bool $argument = TRUE) => $argument ? $this->realSimpleBuilder : $this->realBuilder);
        }
        else
        {
            $this->db->shouldReceive('get_new_dml_query_builder_object')
                     ->atLeast()
                     ->once()
                     ->andReturn($this->realBuilder);
        }

        $this->db->shouldReceive('query')
                 ->once()
                 ->andReturn($this->result);

        $this->result->shouldReceive('warnings')
                     ->once()
                     ->andReturn(NULL);

        $this->result->shouldReceive('has_failed')
                     ->once()
                     ->andReturn(TRUE);

        $this->result->shouldReceive('error_number')
                     ->once()
                     ->andReturn(1);

        $this->result->shouldReceive('error_message')
                     ->twice()
                     ->andReturn('Error!');

        $this->result->shouldReceive('query')
                     ->twice()
                     ->andReturn('QUERY;');

        $this->result->shouldReceive('has_deadlock')
                     ->zeroOrMoreTimes()
                     ->andReturn(FALSE);

        $this->result->shouldReceive('has_lock_timeout')
                     ->zeroOrMoreTimes()
                     ->andReturn(FALSE);

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('Database query error!');
    }

    /**
     * Expect that a query is successful.
     *
     * @return void
     */
    protected function expectQuerySuccess(): void
    {
        if (property_exists($this, 'realSimpleBuilder'))
        {
            $this->db->shouldReceive('get_new_dml_query_builder_object')
                     ->atLeast()
                     ->once()
                     ->andReturnUsing(fn(bool $argument = TRUE) => $argument ? $this->realSimpleBuilder : $this->realBuilder);
        }
        else
        {
            $this->db->shouldReceive('get_new_dml_query_builder_object')
                     ->atLeast()
                     ->once()
                     ->andReturn($this->realBuilder);
        }

        $this->db->shouldReceive('query')
                 ->once()
                 ->andReturn($this->result);

        $this->result->shouldReceive('warnings')
                     ->once()
                     ->andReturn(NULL);

        $this->result->shouldReceive('has_failed')
                     ->once()
                     ->andReturn(FALSE);

        $this->result->shouldReceive('has_deadlock')
                     ->zeroOrMoreTimes()
                     ->andReturn(FALSE);

        $this->result->shouldReceive('has_lock_timeout')
                     ->zeroOrMoreTimes()
                     ->andReturn(FALSE);
    }

    /**
     * Expect that a query is successful, after a deadlock-caused retry.
     *
     * @return void
     */
    protected function expectQuerySuccessAfterRetry(): void
    {
        if (property_exists($this, 'realSimpleBuilder'))
        {
            $this->db->shouldReceive('get_new_dml_query_builder_object')
                     ->atLeast()
                     ->once()
                     ->andReturnUsing(fn(bool $argument = TRUE) => $argument ? $this->realSimpleBuilder : $this->realBuilder);
        }
        else
        {
            $this->db->shouldReceive('get_new_dml_query_builder_object')
                     ->atLeast()
                     ->once()
                     ->andReturn($this->realBuilder);
        }

        $this->db->shouldReceive('query')
                 ->twice()
                 ->andReturn($this->result);

        $this->result->shouldReceive('query')
                     ->once()
                     ->andReturn('QUERY');

        $this->result->shouldReceive('warnings')
                     ->once()
                     ->andReturn(NULL);

        $this->result->shouldReceive('has_failed')
                     ->once()
                     ->andReturn(FALSE);

        $this->result->shouldReceive('has_deadlock')
                     ->once()
                     ->andReturn(TRUE);

        $this->result->shouldReceive('has_deadlock')
                     ->once()
                     ->andReturn(FALSE);

        $this->result->shouldReceive('has_lock_timeout')
                     ->once()
                     ->andReturn(FALSE);
    }

}

?>
