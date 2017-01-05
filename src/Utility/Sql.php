<?php namespace ChaoticWave\BlueVelvet\Utility;

use Illuminate\Database\Connection;
use Illuminate\Database\MySqlConnection;

/**
 * Down-and-dirty PDO SQL class
 */
class Sql
{
    //*************************************************************************
    //* Members
    //*************************************************************************

    /**
     * @var Connection
     */
    protected static $connection;
    /**
     * @var \PDOStatement
     */
    protected static $statement;

    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * Creates and returns an optionally parameter-bound \PDOStatement object
     *
     * @param string $sql
     * @param \PDO   $connection
     * @param int    $fetchMode Set to false to not touch fetch mode
     *
     * @return \PDOStatement
     */
    public static function createStatement($sql, &$connection = null, $fetchMode = \PDO::FETCH_ASSOC)
    {
        $_db = static::checkConnection($connection);

        /** @var $_statement \PDOStatement */
        $_statement = $_db->prepare($sql);

        if (false !== $fetchMode) {
            $_statement->setFetchMode($fetchMode);
        }

        return $_statement;
    }

    /**
     * Creates and returns an optionally parameter-bound \PDOStatement object suitable for iteration
     *
     * @param string $sql
     * @param array  $parameters
     * @param \PDO   $connection
     * @param int    $fetchMode Set to false to not touch fetch mode
     *
     * @return DataReader
     */
    public static function query($sql, $parameters = null, &$connection = null, $fetchMode = \PDO::FETCH_ASSOC)
    {
        return DataReader::create($sql, $parameters, $connection, $fetchMode);
    }

    /**
     * Executes a SQL statement
     *
     * @param string $sql
     * @param array  $parameters
     * @param \PDO   $connection
     * @param int    $fetchMode
     *
     * @return bool TRUE on success
     */
    public static function execute($sql, $parameters = null, $connection = null, $fetchMode = \PDO::FETCH_ASSOC)
    {
        static::$statement = static::createStatement($sql, $connection, $fetchMode);

        if (empty($parameters)) {
            return static::$statement->execute();
        }

        return static::$statement->execute($parameters);
    }

    /**
     * Executes a SQL query
     *
     * @param string $sql
     * @param array  $parameters
     * @param \PDO   $connection
     * @param int    $fetchMode
     *
     * @return null|array
     */
    public static function find($sql, $parameters = null, $connection = null, $fetchMode = \PDO::FETCH_ASSOC)
    {
        if (false === ($_reader = static::query($sql, $parameters, $connection, $fetchMode))) {
            return null;
        }

        return $_reader->fetch();
    }

    /**
     * Executes the given sql statement and returns all results
     *
     * @param string $sql
     * @param array  $parameters
     * @param \PDO   $connection
     * @param int    $fetchMode
     *
     * @return array|bool
     */
    public static function findAll($sql, $parameters = null, $connection = null, $fetchMode = \PDO::FETCH_ASSOC)
    {
        if (false === ($_reader = static::query($sql, $parameters, $connection, $fetchMode))) {
            return null;
        }

        return $_reader->fetchAll();
    }

    /**
     * Returns the first column of the first row or null
     *
     * @param string $sql
     * @param int    $columnNumber
     * @param array  $parameters
     * @param \PDO   $connection
     * @param int    $fetchMode
     *
     * @return mixed
     */
    public static function scalar($sql, $columnNumber = 0, $parameters = null, $connection = null, $fetchMode = \PDO::FETCH_ASSOC)
    {
        if (false === ($_reader = static::query($sql, $parameters, $connection, $fetchMode))) {
            return null;
        }

        return $_reader->fetchColumn($columnNumber);
    }

    /**
     * @static
     *
     * @param \PDO $connection
     *
     * @throws \LogicException
     * @return \PDO
     */
    protected static function checkConnection(&$connection = null)
    {
        if (!is_object($connection)) {
            $connection = \DB::connection($connection);
            $_db = $connection->getPdo();
        } elseif (static::$connection) {
            $_db = static::$connection->getPdo();
        }

        //	Connect etc...
        if (empty($_db) || !$_db->getAttribute(\PDO::ATTR_CONNECTION_STATUS)) {
            throw new \LogicException('Cannot proceed until a database connection has been established. Try setting the "connection" property.');
        }

        static::setConnection($_db);

        return $_db;
    }

    /**
     * @param \PDO|null $connection
     */
    public static function setConnection($connection = null)
    {
        //  GC first
        static::$connection and static::$connection = null;
        static::$connection = $connection;
    }

    /**
     * @return \PDO
     */
    public static function getConnection()
    {
        return static::$connection;
    }

    /**
     * @param \PDOStatement $statement
     */
    public static function setStatement($statement)
    {
        static::$statement = $statement;
    }

    /**
     * @return \PDOStatement
     */
    public static function getStatement()
    {
        return static::$statement;
    }
}
