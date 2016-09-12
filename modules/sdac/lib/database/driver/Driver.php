<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : Driver.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Lms Database Driver Abstract Layer
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 87 $)
 * CREATED     : Dec 7, 2012
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-11-05 17:30:25 +0800 (週四, 05 十一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) Driver.php              1.0 Dec 7, 2012
   by Michelle Hong


   Copyright by ASTRI, Ltd., (ECE Group)
   All rights reserved.

   This software is the confidential and proprietary information
   of ASTRI, Ltd. ("Confidential Information").  You shall not
   disclose such Confidential Information and shall use it only
   in accordance with the terms of the license agreement you
   entered into with ASTRI.
   --------------------------------------------------------------- */

namespace Clms\Tools\PhpDao\Driver;

use Clms\Tools\PhpDao\Exception\DbException;

use Clms\Tools\PhpDao\Column\Column;
use Clms\Tools\PhpDao\Column\ColumnInt;
use Clms\Tools\PhpDao\Query\Query;

/* ===============================================================
   Begin of Driver.php
   =============================================================== */


/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */


/* ---------------------------------------------------------------
   Global Variables
   --------------------------------------------------------------- */


/* ---------------------------------------------------------------
   Constant definition
   --------------------------------------------------------------- */



/* ---------------------------------------------------------------
   Function definition
   --------------------------------------------------------------- */
/**
 *
 * Lms Database Driver Abstract Layer
 *
 * How to get a database
 * <code>
 *     $db = Driver::getInstance($options);
 * </code>
 *
 * The options include driver name, host name, user name, password, database
 * name, table prefix. A sample code is as follows:
 * <code>
 * $options['driver']= 'mysql';
 * $options['dbhost'] = 'localhost';
 * $options['dbuser'] = 'cloud';
 * $options['database'] = 'cloud';
 * $options['dbpassword'] ='cloud';
 * $options['dbprefix'] = 'lms_';
 * </code>
 *
 * Please note that in oracle database (version 11g), the dbuser should be
 * uppercase in our testing.
 * @package Php-Dao
 * @subpackage driver
 * @since  Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
abstract class Driver
{
    /**
     * Lms Database default prefix (for replacement)
     * @var unknown
     * @version 1.0
     * @since Version 1.0
     */
    const LMS_DEFAULT_PREFIX= '#__';

    /**
     * @var string Lms Database db name
     * @version 1.0
     * @since Version 1.0
     */
    protected $_database;
    /**
     * @var string Table prefix of the system
     * @version 1.0
     * @since Version 1.0
     */
    protected $_tablePrefix;

    /**
     *
     * @var array Database configuration array, passed by constructor
     * @version 1.0
     * @since Version 1.0
     */
    protected $_options;
    /**
     *
     * @var string Driver name, used to determine query name
     * @version 1.0
     * @since Version 1.0
     */
    public $name='';
    /**
     * The quotation mark used by database in order to use reserving words
     * @var string
     * @version 1.0
     * @since Version 1.0
     */
    protected $_nameQuote='"';

    /**
     *
     * @var PDO Connection to the db, a PDO instance
     * @version 1.0
     * @since Version 1.0
     */
    protected $_connection;
    /**
     *
     * @var Query Lms Database query, must be associated with one
     *              correct Driver
     * @version 1.0
     * @since Version 1.0
     */
    protected $_query;

    /**
     *
     * @var \PDOStatement The prepared statement
     * @version 1.0
     * @since Version 1.0
     */
    protected $_prepared= null;

    /**
     *
     * @var boolean Whether the statement has been executed. This field is used
     *              to retry the execution if first time fails
     * @version 1.0
     * @since Version 1.0
     */
    protected $_executed = false;

    /**
     *
     * @var array An array of Driver normally for a database connection
     *
     * @version 1.0
     * @since Version 1.0
     */
    protected static $_instances = array();

    /**
     *
     * Function to generate the PDO connection string
     * @since  Version 1.0
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
    */
    abstract protected function getConnectionString();
    /**
     *
     * Check if the driver is supported
     * @version 1.0
     * @since Version 1.0
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
    */
    abstract public function isSupported();


    /**
     *
     * Function description goes here
     *
     * @version 1.0
     * @since  Version 1.0
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    abstract protected function insertid();

    /**
     *
     * Get the create table query string
     * @version 1.0
     * @since Version 1.0
     * @param Dao $table table definition
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
    */
    abstract public function getCreateTableQuery($table);


    /**
     *
     * Abstract function to get the drop table query
     *
     * @version 1.0
     * @since  Version 1.0
     * @param \Clms\Tools\PhpDao\Dao $table Dao object
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    abstract protected function getDropTableQuery($table);

    /**
     *
     * Abstract function to generate a query for drop index in table
     * @version 1.0
     * @param string $table table name
     * @param string $index index name
     * @since Version 1.0
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
    */
    abstract protected function getDropIndexQuery($table, $index);

    /**
     *
     * Get the sql for drop unique key in a table
     * @version 1.0
     * @since Version 1.0
     * @param string $table table name
     * @param string  $key keyname
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
    */
    abstract protected function getDropUniqueQuery($table, $key);

    /**
     *
     * Get the sql for drop primary key
     * @version 1.0
     * @since Version 1.0
     * @param string $table table name
     * @param string $key key name
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
    */
    abstract protected function getDropPrimaryQuery($table,$key);

    /**
     *
     * Simple query to test if the database is on.
     * @return string query string
     * @since  Version 1.0
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    abstract protected function getTestConnectionQuery();

    /**
     *
     * Load data list from database
     *
     * This is an abstract function. Shall be override by each of the database
     * platform.
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $tableName Table name
     * @param array $columnSpec Column specification
     * @param array $keys Search criteria as key/value pair
     * @param string $class Class name for return object
     * @param int $offset Search offset
     * @param int $limit Search limit
     * @param array $orderBy Search order by. For example "timecreated"=> "ASC"
     * @param array $relations Search operator, such as bigger than , etc.
     * @param array $groupBy Search group by
     * @param array $aggregates Aggregates search
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    abstract public function loadObjectList($tableName, $columnSpec, $keys, $class,
            $offset, $limit, $orderBy, $relations, $groupBy, $aggregates);




    /**
     * 
     * Make a column as string
     * 
     * Used for add column or create table
     *
     * @since  Version 1.0
     * @param Column $column
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    abstract protected function column2String($column);
    /**
     *
     * Database driver constructor
     *
     * There is no real connection test during the constructor
     * Test connection is only checked when there is an operation to Database
     * @version 1.0
     * @since Version 1.0
     * @param array $options array of database connection information
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
    */
    private function __construct($options)
    {
        $options['dbhost'] = (isset($options['dbhost'])) ? $options['dbhost']
                : 'localhost';
        $options['dbuser'] = (isset($options['dbuser'])) ? $options['dbuser']
                : 'root';
        $options['dbpassword'] = (isset($options['dbpassword'])) ? $options['dbpassword']
                : '';
        $options['database'] = (isset($options['database'])) ? $options['database']
                : '';

        // Initialize object variables.
        $this->_database = (isset($options['database'])) ? $options['database']
                : '';

        $this->_tablePrefix = (isset($options['dbprefix'])) ? $options['dbprefix']
                : 'lms_';

        // Set class options.
        $this->_options = $options;
    }
    /**
     *
     * Get a Driver instance based on the given options.
     * Only given database will be select to use
     *
     *
     * @todo change to use Clms\Tools\PhpDao\Util\Singleton later
     *
     * @param array $options Parameters to be passed to the database driver.
     * @throws Clms\Tools\PhpDao\Exception\DbException
     * @return Driver
     * @since  Version 1.0
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function getInstance($options = array())
    {
        // Get the options signature for the database connector.
        $signature = md5('clms_relation_db');

        $driver= isset($options['driver'])?$options['driver']:null;

        // If we already have a database connector instance for these options
        // then just use that.
        if (empty(self::$_instances[$signature]) || (!empty($driver) && $driver!== self::$_instances[$signature]->name) ) {
            //$instance= new DriverMysql($options);
            // Derive the class name from the driver.
            $class = __NAMESPACE__ . '\\'. 'Driver' . ucfirst(strtolower($options['driver']));

            // Create our new Driver connector based on the options given.
            if (class_exists($class)) {
                $instance = new $class($options);
            } else {
                throw new DbException(DbException::DBERROR_CANNOT_GET_DRIVER);
            }
            // Set the new connector to the global instances based on signature.
            self::$_instances[$signature] = $instance;
        }

        return self::$_instances[$signature];
    }

    /**
     *
     * Connect to database based on options
     *
     * Please note that for Oracle Database, the user name is case sensitive
     * should map to the schema name under the user. It is safe to use an
     * uppercase user name in Oracle 11g.
     *
     * @throws Clms\Tools\PhpDao\Exception\DbException
     * @since  Version 1.0
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function connect()
    {
        if ($this->_connection) {
            return;
        }


        if (!$this->isSupported()) {
            throw new DbException(
                DbException::DBERROR_PDO_UNSUPPOT
            );
        }

        try {
            $pdo = new \PDO(
                $this->getConnectionString(), $this->_options['dbuser'],
                $this->_options['dbpassword'],
                array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';")
            );
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER);
            $pdo->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_TO_STRING);
            //$pdo->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, true);

            $this->_connection= $pdo;


        } catch (\PDOException $e) {
            error_log($e->getTraceAsString());
            throw new DbException(DbException::DBERROR_CONNECTION, $e);
        }

        return;
    }

    /**
     *
     * Determines if the connection to the server is on.
     * @return boolean True if the database server is on
     * @throws Clms\Tools\PhpDao\Exception\DbException
     * @since  Version 1.0
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    private function connected()
    {
        // Flag to prevent recursion into this function.
        static $checkingConnected = false;

        if ($checkingConnected) {
            // Reset this flag and throw an exception.
            $checkingConnected = true;
            throw new DbException(DbException::DBERROR_TEST_CONNECTION_MULTIPLE);
        }

        // Backup the query state.
        $sql = $this->_query;
        $prepared = $this->_prepared;

        try
        {
            // Set the checking connection flag.
            $checkingConnected = true;

            // Run a simple query to check the connection.
            $this->setQuery($this->getTestConnectionQuery());
            $status = (bool) $this->loadResult();

        }
        // If we catch an exception here, we must not be connected.
        catch (Exception $e)
        {
            $status = false;
        }

        // Restore the query state.
        $this->_query = $sql;
        $this->_prepared = $prepared;
        $checkingConnected = false;

        return $status;
    }

    /**
     *
     * Begin a transaction
     *
     * @version 1.0
     * @since  Version 1.0
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function beginTransaction()
    {
        $this->connect();
        $this->_connection->beginTransaction();
    }

    /**
     *
     * Rollback
     *
     * @version 1.0
     * @since  Version 1.0
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function rollback()
    {
        $this->_connection->rollback();
    }

    /**
     *
     * Commit changes to database
     *
     * @version 1.0
     * @since  Version 1.0
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function commit()
    {
        $this->_connection->commit();
    }


    /**
     *
     * Wrap an SQL statement identifier name such as column, table or database
     * names in quotes to prevent injection risks and reserved word conflicts.
     *
     * Usage:
     *   Input: $name= 'cloud.object'
     *          $as = 'lmsobject'
     *   Output: 'cloud'.'object' as 'lmsobject'
     * @version 1.0
     * @since Version 1.0
     * @param mixed $name The identifier name to wrap in quotes, or an array of
     *                    identifier names to wrap in quotes.
     *                    Each type supports dot-notation name.
     * @param string $as  The AS query part associated to $name.
     *                    It can be string or array, in latter case it has to be
     *                    same length of $name; if is null there will not be any
     *                    AS part for string or array element.
     * @return mixed  The quote wrapped name, same type of $name.
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function quoteName($name, $as = null)
    {
        if (is_string($name)) {
            $quotedName = $this->quoteNameStr(explode('.', $name));

            $quotedAs = '';
            if (!is_null($as)) {
                settype($as, 'array');
                $quotedAs .= ' AS ' . $this->quoteNameStr($as);
            }

            return $quotedName . $quotedAs;
        } else {
            $fin = array();
            if (is_null($as)) {
                foreach ($name as $str) {
                    $fin[] = $this->quoteName($str);
                }
            } elseif (is_array($name) && (count($name) == count($as))) {
                $count = count($name);
                for ($i = 0; $i < $count; $i++) {
                    $fin[] = $this->quoteName($name[$i], $as[$i]);
                }
            }

            return $fin;
        }
    }


    /**
     *
     * Quote a array an d return a string with dot-imploded string
     *
     * Usage:
     *    Input: <code> $strArr=array('cloud', 'object');</code>
     *    Output:<code> 'cloud'.'object' </code>
     * Also if the input array element is a * then no quotation mark
     * For example 'cloud'.*
     * @version 1.0
     * @since Version 1.0
     * @param array $strArr Array of string need to be quoted
     * @return string a string with suitable quotation mark
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    private function quoteNameStr($strArr)
    {
        $parts = array();
        $q = $this->_nameQuote;

        foreach ($strArr as $part) {
            if (is_null($part)) {
                continue;
            }

            if ($part==='*') {
                $parts[]= $part;
                continue;
            }

            if (strlen($q) == 1) {
                $parts[] = $q . $part . $q;
            } else {
                $parts[] = $q{0} . $part . $q{1};
            }
        }

        return implode('.', $parts);
    }

    /**
     *
     * Quote the column array one by one in order to prevent injection risks
     * and reserved word conflicts.
     * @version 1.0
     * @since Version 1.0
     * @param mixed $columns a single/array field to quote
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    private function quoteColumns($columns)
    {
        if (is_string($columns)) {
            $result= $this->quoteName($columns);
        } else {
            $result= array();
            foreach ($columns as $value) {
                $result[]= $this->quoteName($value);
            }
            $result= implode(',', $result);
        }
        return  $result;
    }

    /**
     *
     * Get the query builder based on the driver type
     *
     * Use the corresponding Query type with the driver
     *
     * @since  Version 1.0
     * @param string $new  True if the new query is created or use the old
     * Clms\Tools\PhpDao\Exception\DbException
     * @return unknown|Query
     * @throws Clms\Tools\PhpDao\Exception\DbException
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getQuery($new = false)
    {
        if ($new) {
            // Derive the class name from the driver.
            $class = 'Clms\Tools\PhpDao\Query\Query' . ucfirst($this->name);

            // Make sure we have a query class for this driver.
            if (!class_exists($class)) {
                // If it doesn't exist throw an exception.
                throw new DbException(DbException::DBERROR_CANNOT_GET_QUERY_CLASS);
            }
            return new $class($this);
        } else {
            return $this->_query;
        }
    }



    /**
     *
     * Set the SQL string for later execution
     * @param mixed $query The SQL statement to set, should be Query or string
     *                     (not recommended)
     * @param mixed $driverOptions The driver options send to PDO::Prepare.
     *                             This parameters could set specific attribute
     *                             for the query, not frequently used
     * @return Driver
     * @since  Version 1.0
     * @see PDO::prepare() for $driverOptions
     * @author      Michelle Hong
     * @testing
     * @warnings  The Query is set by developers as string, developers has to
     *            make sure that the SQL is worked in all database types
     * @updates
     */
    public function setQuery($query,  $driverOptions = array())
    {
        $this->connect();

        $this->freeResult();

        if (is_string($query)) {
            // Allows taking advantage of bound variables in a direct query:
            $query = $this->getQuery(true)->setQuery($query);
        }

        $sql = $this->replacePrefix((string) $query);

        $this->_prepared = $this->_connection->prepare($sql, $driverOptions);

        $this->_query = $query;

        return $this;
    }


    /**
     *
     * Insert a row into database
     * @version 1.0
     * @since Version 1.0
     * @param   string  $table    The name of the database table to insert into.
     * @param   object  &$object  A reference to an object whose public
     *                            properties match the table fields.
     * @param   string  $key      The name of the primary key. If provided the
     *                            object property is updated.
     * @param   array   $columnSpecs   Array of Column
     * @return  boolean    True on success
     * @throws Clms\Tools\PhpDao\Exception\DbException
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function insertObject($table, &$object, $key, $columnSpecs)
    {
        $fields = array();
        $values = array();

        // Create the base insert statement.
        $query = $this->getQuery(true);
        // Iterate over the object variables to build the query fields
        //and values.
        foreach (get_object_vars($object) as $k => $v) {
            if ($k===$key
                && $columnSpecs[$key] instanceof ColumnInt
                && $columnSpecs[$key]->isAutoIncremental()
                && $this instanceof DriverSequenceable) {
                $next= $this->getSequencer($this->replacePrefix($table));

                $v= $next;
                $object->$k=$next;
            }
            // Only process non-null scalars.
            if (is_array($v) or is_object($v) or $v === null) {
                continue;
            }

            // Ignore any internal fields.
            if ($k[0] == '_') {
                continue;
            }
            // Prepare and sanitize the fields and values for
            //the database query.
            $fields[] = $this->quoteName($k);
            $placeHolders [] = ':'. $k;
            $type= $columnSpecs[$k]->getBindParamType();

            //Use $object->$k instead of $v for pass by reference issue
            $query->bind(':'.$k, $object->$k, $type, strlen($object->$k));
        }

        $query->insert($this->quoteName($table))
              ->columns($fields)
              ->values(implode(',', $placeHolders));

        // Set the query and execute the insert.
        $this->setQuery($query);
        if ($this->execute()) {
            // Update the primary key if it exists based on the PDO driver
            if (! ($this instanceof  DriverSequenceable)) {
                $id = $this->insertid();

                if ($key && $id && is_string($key)) {
                    $object->$key = $id;
                }
            }
            return true;
        }


    }

    /**
     *
     * Update a row in database
     * @since  Version 1.0
     * @param string $table table name
     * @param object $object the object need to be updated
     * @param mixed $tblPrimaryKey primary key of the table
     * @param array $fieldSpecs table field definition
     * @param boolean $updateNulls if the null value will be update in database
     * @param array $extraWhere add extra key value pair if extra
     *                          update criteria is given other than primary key
     * @return boolean if the update is successfully done
     * @throws Clms\Tools\PhpDao\Exception\DbException
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function updateObject($table, &$object, $tblPrimaryKey, $fieldSpecs,
            $updateNulls = false, $extraWhere = array())
    {
        $fields = array();

        $where = array();

        // Create the base insert statement.
        $query = $this->getQuery(true);

        foreach ($extraWhere as $key => $value) {
            if (isset($fieldSpecs[$key])) {
                $where[] = $this->quoteName($key) . '=:lms_extra_' . $key;
                $type = $fieldSpecs[$key]->getBindParamType();
                $query ->bind(
                    ':lms_extra_' . $key,
                    $extraWhere[$key],
                    $type,
                    strlen($value)
                );
            }
        }

        $validFields = array_keys($fieldSpecs);

        if (is_string($tblPrimaryKey)) {
            $tblPrimaryKey = array($tblPrimaryKey);
        }

        if (is_object($tblPrimaryKey)) {
            $tblPrimaryKey = (array) $tblPrimaryKey;
        }

        // Iterate over the object variables to build the query fields/value
        // pairs.
        foreach (get_object_vars($object) as $k => $v) {
            // Only process scalars that are not internal fields.
            if (is_array($v) || is_object($v) || $k[0] == '_'
                    || !in_array($k, $validFields)) {
                continue;
            }

            // Set the primary key to the WHERE clause instead
            // of a field to update.
            if (in_array($k, $tblPrimaryKey)) {
                $where[] = $this->quoteName($k) . '=:' . $k;
                $type = $fieldSpecs[$k]->getBindParamType();
                $query->bind(':' . $k, $object->$k, $type, strlen($object->$k));
                continue;
            }

            // Prepare and sanitize the fields and values for the database
            //query.
            if ($v === null) {
                // If the value is null and we want to update nulls then set it.
                if ($updateNulls) {
                    $val = 'NULL';
                    // Add the field to be updated.
                    $fields[] = $this->quoteName($k) . '=NULL';

                } else {
                    // If the value is null and we do not want to update nulls
                    //then ignore this field.
                    continue;
                }
            } else {
                // The field is not null so we prep it for update.

                // Add the field to be updated.
                $fields[] = $this->quoteName($k) . '=' . ':' . $k;

                $type = $fieldSpecs[$k]->getBindParamType();

                $query->bind(':' . $k, $object->$k, $type, strlen($v));
            }
        }

        // We don't have any fields to update.
        if (empty($fields)) {
            return true;
        }

        $query->update($this->quoteName($table))->set($fields)->where($where);

        // Set the query and execute the insert.
        $this->setQuery($query);

        if ($this->execute()) {
            return true;
        }
    }


    /**
     *
     * Create a table based on Dao definition
     * @param Dao $table Dao object.
     * @return boolean If the table is successfully created
     * @throws Clms\Tools\PhpDao\Exception\DbException
     * @since  Version 1.0
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function createTable($table)
    {
        $sql= $this->getCreateTableQuery($table);

        $this->setQuery($sql);
        if ($this->execute()) {
            $indexes= $table->getColumnIndex();
            if (!empty($indexes)) {
                foreach ($indexes as $index) {
                    $this->addIndex(
                        $table->getTableName(),
                        $index->getIndexName(),
                        $index->getColumns(),
                        $index->isUnique()
                    );
                }
            }
            $uniques= $table->getTableUnique();
            if (!empty($uniques)) {
                foreach ($uniques as $unique) {
                    $this->addUnique(
                        $table->getTableName(),
                        $unique->getKeyName(),
                        $unique->getColumns()
                    );
                }
            }
        }
        return true;
    }


    /**
     *
     * Drop a table based on DAO
     *
     * @version 1.0
     * @since  Version 1.0
     * @param \Clms\Tools\Dao $table table DAO definition
     * @return boolean
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function dropTable ($table)
    {
        $sql= $this->getDropTableQuery($table);

        $this->setQuery($sql);
        $cursor = $this->execute();

        $this->freeResult($cursor);
        return  true;
    }
    /**
     *
     * Add a new column into an exisiting table
     * @version 1.0
     * @since Version 1.0
     * @param string $table table name
     * @param string $field column name
     * @param Column $spec column spec
     * @return boolean If the column is successfully added
     * @throws Clms\Tools\PhpDao\Exception\DbException
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function addColumn($table, $field, $spec)
    {
        
        $sql=  'ALTER TABLE ' . $this->quoteName($table). ' ADD '
                . $this->quoteName($field). ' '. $this->column2String($spec);

        $this->setQuery($sql);
        $cursor = $this->execute();

        $this->freeResult($cursor);
        return  true;
    }

    /**
     *
     * Drop a column from a table
     * @version 1.0
     * @since Version 1.0
     * @param string $table table name
     * @param string $field column name
     * @return bool
     * @throws Clms\Tools\PhpDao\Exception\DbException
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function dropColumn($table, $field)
    {
        $sql=  'ALTER TABLE ' . $this->quoteName($table). ' DROP COLUMN '
                . $this->quoteName($field);

        $this->setQuery($sql);
        $cursor = $this->execute();

        $this->freeResult($cursor);
        return  true;
    }

    /**
     *
     * Add index to a exisiting table
     * @version 1.0
     * @since Version 1.0
     * @param string $table table name
     * @param string $index index name
     * @param string|array $columns a single field or an array of field name
     * @param string $unique if the index should be unique
     * @return bool
     * @throws Clms\Tools\PhpDao\Exception\DbException
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function addIndex($table, $index, $columns, $unique= false)
    {

        $sql=  $this->getAddIndexQuery($table, $index, $columns, $unique);

        $this->setQuery($sql);
        $cursor = $this->execute();
        $this->freeResult($cursor);
        return  true;
    }


    /**
     *
     * Drop an index from table
     * @version 1.0
     * @since Version 1.0
     * @param string $table table name
     * @param string $index index name
     * @return bool
     * @throws Clms\Tools\PhpDao\Exception\DbException
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function dropIndex($table, $index)
    {
        $sql= $this->getDropIndexQuery($table, $index);
        $this->setQuery($sql);
        $cursor = $this->execute();

        $this->freeResult($cursor);
        return  true;

    }
    /**
     *
     * Add an unique constraint to a table
     * @version 1.0
     * @since Version 1.0
     * @param string $table table name
     * @param string $name key name
     * @param mixed $columns columns in the unique constraint
     * @throws Clms\Tools\PhpDao\Exception\DbException
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function addUnique($table, $name, $columns)
    {
        $sql= $this->getAddConstraintQuery($table, 'unique', $name, $columns);
        $this->setQuery($sql);
        $cursor = $this->execute();

        $this->freeResult($cursor);
        return  true;
    }

    /**
     *
     * Drop an unique constraint to a table
     * @version 1.0
     * @since Version 1.0
     * @param string $table table name
     * @param string $name unique key name
     * @return bool
     * @throws Clms\Tools\PhpDao\Exception\DbException
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function dropUnique($table, $name)
    {
        $sql= $this->getDropUniqueQuery($table, $name);
        $this->setQuery($sql);
        $cursor = $this->execute();

        $this->freeResult($cursor);
        return  true;
    }

    /**
     *
     * Add a primary key in a table
     * @version 1.0
     * @since Version 1.0
     * @param string $table table name
     * @param string $name key name
     * @param mixed $columns columns in the unique constraint
     * @return bool
     * @throws Clms\Tools\PhpDao\Exception\DbException
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function addPrimary($table, $name, $columns)
    {
        $sql= $this->getAddConstraintQuery($table, 'primary', $name, $columns);
        $this->setQuery($sql);
        $cursor = $this->execute();
        $this->freeResult($cursor);
        return  true;
    }

    /**
     *
     * Drop an primary key constraint to a table
     * @version 1.0
     * @since Version 1.0
     * @param string $table table name
     * @param string $name unique key name
     * @return boolean If the drop primary key successfully
     * @throws Clms\Tools\PhpDao\Exception\DbException
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function dropPrimary($table, $name)
    {

        $sql= $this->getDropPrimaryQuery($table, $name);
        $this->setQuery($sql);
        $cursor = $this->execute();

        $this->freeResult($cursor);
        return  true;
    }
    /**
     *
     * Get a sql to add a constranit
     * @version 1.0
     * @param string $table table name
     * @param string $type constaint type: 'unique' or 'primary'
     * @param string $name containt name
     * @param mixed $columns columns involved
     * @since Version 1.0
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getAddConstraintQuery($table, $type, $name, $columns)
    {
        if ($type==='unique') {
            $type= 'UNIQUE';
        } else {
            $type= 'PRIMARY KEY';
        }
        return 'ALTER TABLE '. $this->quoteName($table).' ADD CONSTRAINT '
                . $this->quoteName($name).' '. $type.' ('
                . $this->quoteColumns($columns). ')';

    }

    /**
     *
     * Get the add index query for table
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $table table name
     * @param string $index index name
     * @param string|array $columns a single field or an array of field name
     * @param bool $unique if the index should be unique
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getAddIndexQuery($table, $index, $columns, $unique= false)
    {
        return  'CREATE'. ($unique? ' UNIQUE': '')
        . ' INDEX '. $this->quoteName($index)
        . ' ON ' . $this->quoteName($table)
        . ' (' . $this->quoteColumns($columns) . ')';

    }

    /**
     *
     * Exceute a query based on the query value
     *
     * @throws Clms\Tools\PhpDao\Exception\DbException
     * @since  Version 1.0
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function execute()
    {
        $this->connect();

        $firstError=null;

        if (!is_object($this->_connection)) {
            throw new DbException(DbException::DBERROR_BASE);
        }

        // Take a local copy so that we don't modify the original query
        //and cause issues later
        //$sql = $this->replacePrefix((string) $this->_query);

        //var_dump($this->replacePrefix((string) $this->_query));

        // Execute the query.
        $this->_executed = false;
        if ($this->_prepared instanceof \PDOStatement) {

            try {
                //check if we need to bind params
                $bounded = &$this->_query
                                 ->getBounded();
                foreach ($bounded as $key => $obj) {
                    $this->_prepared
                            ->bindParam(
                                $key,
                                $obj->value,
                                $obj->dataType,
                                $obj->length,
                                $obj->driverOptions
                            );
                }
                $this->_executed = $this->_prepared->execute();
            }
            catch (\RuntimeException $e) {
                //doing nothing for the first time
                $firstError = $e;
            }
        }

        // If an error occurred handle it.
        if (!$this->_executed) {
            try{
                $isConnected= $this->connected();
            }
            catch (\RuntimeException $e){
                //doing nothing for testing connection
            }
            if (!$isConnected) {
                $this->_connection = null;
                $this->connect();

                // Since we were able to reconnect, run the query again.
                return $this->execute();
            } else {

                //if the connection is fine. then report as the first error
                $code=DbException::DBERROR_QUERY_FAIL;
                switch($this->_query->getType()){
                    case 'insert':
                        $code= DbException::DBERROR_CREATE;
                        break;
                    case 'delete':
                        $code = DbException::DBERROR_DELETE;
                        break;
                    case 'update':
                        $code = DbException::DBERROR_UPDATE;
                        break;
                    case 'select':
                        $code = DbException::DBERROR_READ;

                }
                error_log(print_r($firstError->getMessage(),2));
                throw new DbException($code, $firstError);
            }
        }

        return $this->_prepared;
    }
    /**
     *
     * Replace LMS_DEFAULT_PREFIX with the string held is the
     * table dbprefix in the config variable.
     *
     * @version 1.0
     * @since Version 1.0
     * @param string $sql The SQL statement to prepare.
     * @param string $prefix The common table prefix.
     * @return string the replaced SQL statement
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function replacePrefix($sql, $prefix = Driver::LMS_DEFAULT_PREFIX)
    {
        $sql = trim($sql);

        $literal = str_replace($prefix, $this->_tablePrefix, $sql);

        /*
        $escaped = false;
        $startPos = 0;
        $quoteChar = '';
        $literal = '';

        $sql = trim($sql);
        $n = strlen($sql);

        while ($startPos < $n) {
            $ip = strpos($sql, $prefix, $startPos);
            if ($ip === false) {
                break;
            }

            $j = strpos($sql, "'", $startPos);
            $k = strpos($sql, '"', $startPos);
            if (($k !== false) && (($k < $j) || ($j === false))) {
                $quoteChar = '"';
                $j = $k;
            } else {
                $quoteChar = "'";
            }

            if ($j === false) {
                $j = $n;
            }

            $literal .= str_replace(
                $prefix,
                $this->_tablePrefix,
                substr($sql, $startPos, $j - $startPos)
            );
            $startPos = $j;

            $j = $startPos + 1;

            if ($j >= $n) {
                break;
            }

            // Quote comes first, find end of quote
            while (true) {
                $k = strpos($sql, $quoteChar, $j);
                $escaped = false;
                if ($k === false) {
                    break;
                }
                $l = $k - 1;
                while ($l >= 0 && $sql{$l} == '\\') {
                    $l--;
                    $escaped = !$escaped;
                }
                if ($escaped) {
                    $j = $k + 1;
                    continue;
                }
                break;
            }
            if ($k === false) {
                // Error in the query - no end quote; ignore it
                break;
            }
            $literal .= substr($sql, $startPos, $k - $startPos + 1);
            $startPos = $k + 1;
        }
        if ($startPos < $n) {
            $literal .= substr($sql, $startPos, $n - $startPos);
        }*/

        return $literal;
    }

    /**
     *
     * Method to get the first field of the first row of the result
     * set from the database query.
     * Used by Testing connection only
     * For general query, please using loadAssoc or loadObject
     * @return NULL|mixed the first column of the first result or null if fails
     * @since  Version 1.0
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    private function loadResult()
    {
        $this->connect();

        $ret = null;

        // Execute the query and get the result set cursor.
        $cursor = $this->execute();
        // Get the first row from the result set as an array.
        if ($row = $this->fetchArray($cursor)) {
            $ret = $row[0];
        }
        // Free up system resources and return.
        $this->freeResult($cursor);

        return $ret;
    }



    /**
     *
     * Fetch a row from the result set cusor as an array
     * PDO::FETCH_NUM: returns an array indexed by column number
     * as returned in your result set, starting at column 0
     * @param mixed $cursor the prepared statement or null
     * @return mixed
     * @since  Version 1.0
     * @see PDOStatement::fetch
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function fetchArray($cursor = null)
    {
        if (!empty($cursor) && $cursor instanceof \PDOStatement) {
            return $cursor->fetch(\PDO::FETCH_NUM);
        }
        if ($this->_prepared instanceof \PDOStatement) {
            return $this->_prepared->fetch(\PDO::FETCH_NUM);
        }
    }

    /**
     *
     * Fetch a row from the result set cusor as an object
     *
     * @version 1.0
     * @since  Version 1.0
     * @param mixed $cursor the prepared statement or null
     * @param string $class Name of the created class.
     * @return object
     * @see PDOStatement::fetchObject
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function fetchObject($cursor = null, $class = 'stdClass')
    {
        $object= false;

        if (!empty($cursor) && $cursor instanceof \PDOStatement) {
            $object= $cursor->fetch(\PDO::FETCH_ASSOC);

        } else if ($this->_prepared instanceof \PDOStatement) {
            $object= $this->_prepared->fetch(\PDO::FETCH_ASSOC);
        }


        if ($object) {

            $result = new $class;
            //$result= $object;

            if (method_exists($result, "getColumnSpec")) {
                foreach ($result-> getColumnSpec() as $key => $value) {
                    //$result->$key=$object[$key];
                    $result->$key= $this->matchObjectType($value->type, $object[$key]);
                }
            } else {
                $result= (object) $object;
            }
            unset($object);
            return $result;
        } else {
            return false;
        }
    }

    /**
     *
     * Convert the database result based on the column type
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $type PHP data type
     * @param mixed $value Data value
     * @return mixed Converted data value
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function matchObjectType($type, $value)
    {
        $result= '';

        if($value === ""){
            return null;
        }
        switch ($type){
            case Column::TYPE_BOOL:
                $result= (bool) $value;
                break;
            case Column::TYPE_INT:
            case Column::TYPE_TIMESTAMP:
                $result= (int) $value;
                break;
            case Column::TYPE_TEXT:
                if (is_resource($value)) {
                    $result= stream_get_contents($value);
                    fclose($value);
                } else {
                    $result= $value;
                }
                break;
            case Column::TYPE_DECIMAL:
                $result = (float) $value;
                break;
            default: $result= $value;
        }
        return $result;
    }


    /**
     *
     * Free up the connection to the server and empty the statement
     *
     * other SQL statements may be issued, but leaves the statement in a state
     * that enables it to be executed again.
     * @version 1.0
     * @since  Version 1.0
     * @param mixed $cursor the prepared statement or null
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function freeResult($cursor = null)
    {
        $this->_executed = false;

        if ($cursor instanceof \PDOStatement) {
            $cursor->closeCursor();
            $cursor = null;
        }
        if ($this->_prepared instanceof \PDOStatement) {
            $this->_prepared->closeCursor();
            $this->_prepared = null;
        }
    }


    /**
     *
     * Load one result using PDO::FETCH_OBJ
     *
     * Returns an object with property names that match to
     * the column names returned in your result set
     *
     * <code>
     * Sample
     * (
     *    [NAME] => apple
     *    [COLOUR] => red
     * )
     * </code>
     * @version 1.0
     * @since  Version 1.0
     * @param string $class
     * @return NULL|Ambigous <NULL, mixed>
     * @see \PDOStatement::fetch
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function loadObject($class = '\stdClass')
    {
        $ret = null;

        // Execute the query and get the result set cursor.
        $cursor = $this->execute();

        // Get the first row from the result set as an object of type $class.
        if ($object = $this->fetchObject($cursor, $class)) {
            $ret = $object;
        }
        // Free up system resources and return.
        $this->freeResult($cursor);

        return $ret;
    }

    /**
     * 
     * Count the number of result
     * 
     * @version 1.0
     * @since  Version 1.0
     * @param string $tableName table name
     * @param array $columnSpec table array specification
     * @param array $keys search keys
     * @return integer total number of result
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function countResult($tableName, $columnSpec, $keys)
    {
        $query = $this->getQuery(true);
        $query->select('count(*)', true)->from($tableName);
        $validFields= array_keys($columnSpec);

        foreach ($keys as $key => $value) {
            if (!in_array($key, $validFields)) {
                //we ingnore the value not in definition
                continue;
            }
            $query->where($this->quoteName($key) . ' =:' . $key);
            $type= $columnSpec[$key]->getBindParamType();
            $query->bind(':'.$key, $keys[$key], $type, strlen($value));
        }

        $this->setQuery($query);

        // Execute the query and get the result set cursor.
        $cursor = $this->execute();

        //Get all of the rows from the result set as objects of type $class.
        $result= $this->fetchArray($cursor);

        // Free up system resources and return.
        $this->freeResult($cursor);

        if($result){
            $result= $result[0];
        } else {
            $result=0;
        }

        return $result;


    }

}


/* ===============================================================
   End of Driver.php
   =============================================================== */