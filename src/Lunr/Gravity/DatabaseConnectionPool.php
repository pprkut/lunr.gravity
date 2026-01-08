<?php

/**
 * Database connection pool class.
 *
 * SPDX-FileCopyrightText: Copyright 2012 M2mobi B.V., Amsterdam, The Netherlands
 * SPDX-FileCopyrightText: Copyright 2022 Move Agency Group B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Gravity;

use ArrayAccess;
use MySQLi;
use Psr\Log\LoggerInterface;

/**
 * This class implements a simple database connection pool.
 *
 * @phpstan-type DatabaseConfig ArrayAccess<string, scalar>|array<string, scalar>
 */
class DatabaseConnectionPool
{

    /**
     * Database configuration
     * @var DatabaseConfig
     */
    protected ArrayAccess|array $config;

    /**
     * Shared instance of a Logger class
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Readonly connection pool
     * @var array
     */
    protected $roPool;

    /**
     * Read-Write connection pool
     * @var array
     */
    protected $rwPool;

    /**
     * Constructor.
     *
     * @param DatabaseConfig  $config Shared instance of the configuration class
     * @param LoggerInterface $logger Shared instance of a logger class
     */
    public function __construct(ArrayAccess|array $config, $logger)
    {
        $this->config = $config;
        $this->logger = $logger;

        $this->roPool = [];
        $this->rwPool = [];
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        unset($this->logger);
        unset($this->roPool);
        unset($this->rwPool);
    }

    /**
     * Get a new readonly connection from the pool.
     *
     * @return DatabaseConnection $db A database connection
     */
    public function get_new_ro_connection()
    {
        return $this->get_connection(TRUE, TRUE);
    }

    /**
     * Get a new read-write connection from the pool.
     *
     * @return DatabaseConnection $db A database connection
     */
    public function get_new_rw_connection()
    {
        return $this->get_connection(TRUE, FALSE);
    }

    /**
     * Get an existing readonly connection from the pool.
     *
     * @return DatabaseConnection $db A database connection
     */
    public function get_ro_connection()
    {
        return $this->get_connection(FALSE, TRUE);
    }

    /**
     * Get an existing read-write connection from the pool.
     *
     * @return DatabaseConnection $db A database connection
     */
    public function get_rw_connection()
    {
        return $this->get_connection(FALSE, FALSE);
    }

    /**
     * Get a database connection.
     *
     * @param bool $new Whether to get a new connection or not
     * @param bool $ro  Whether to get a readonly connection or not
     *
     * @return DatabaseConnection|null $db A database connection
     */
    protected function get_connection($new, $ro): ?DatabaseConnection
    {
        $store = $ro ? 'roPool' : 'rwPool';

        switch ($this->config['driver'])
        {
            case 'mysql':
                // Specifying the full namespace here is necessary because of a restriction
                // in PHP with regards to resolving dynamic class names.
                $type  = 'Lunr\Gravity\MySQL\MySQLConnection';
                $extra = new MySQLi();
                break;
            case 'mariadb':
                $type  = 'Lunr\Gravity\MariaDB\MariaDBConnection';
                $extra = new MySQLi();
                break;
            default:
                return NULL;
        }

        if (($new === TRUE) || (count($this->$store) == 0))
        {
            $connection = new $type($this->config, $this->logger, $extra);

            if ($ro === TRUE)
            {
                $connection->set_readonly(TRUE);
            }

            $this->{$store}[] =& $connection;
        }
        else
        {
            $connection =& $this->{$store}[0];
        }

        return $connection;
    }

}

?>
