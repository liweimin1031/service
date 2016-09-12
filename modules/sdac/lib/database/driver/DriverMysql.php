<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   :DriverMysql.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : CLS Database driver for Mysql
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 83 $)
 * CREATED     : 2012-12-8
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-01-13 16:30:26 +0800 (週二, 13 一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
 @(#) DriverMysql.php              1.0 2012-12-8
by Michelle Hong


Copyright by ASTRI, Ltd., (ECE Group)
All rights reserved.

This software is the confidential and proprietary information
of ASTRI, Ltd. ("Confidential Information").  You shall not
disclose such Confidential Information and shall use it only
in accordance with the terms of the license agreement you
entered into with ASTRI.
--------------------------------------------------------------- */


/* ===============================================================
 Begin of DriverMysql.php
=============================================================== */

namespace Clms\Tools\PhpDao\Driver;

use Clms\Tools\PhpDao\Dao;
use Clms\Tools\PhpDao\Column\Column;
use Clms\Tools\PhpDao\Column\ColumnInt;
use Clms\Tools\PhpDao\Column\ColumnTimeStamp;
use Clms\Tools\PhpDao\Column\ColumnBool;
use Clms\Tools\PhpDao\Column\ColumnChar;
use Clms\Tools\PhpDao\Column\ColumnDecimal;
use Clms\Tools\PhpDao\Column\ColumnText;
use Clms\Tools\PhpDao\Exception\DbException;

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

/* ---------------------------------------------------------------
 Class definition
--------------------------------------------------------------- */

/**
 * Lms Database driver for MySql Database
 *
 * We tested the mysql database for insert only.
 *
 * Testing machine: Michelle's development machine.
 *
 * <code>
 *  $sampelObject = new Sample();
 *  //set up the object here
 *  .....
 * $time1 = time();
 * for ($i = 0; $i < 10000; $i++) {
 *     $sampelObject->id= null;
 *     $sampelObject->save();
 * }
 * echo (time() - $time1);
 * </code>
 *
 *
 * <pre>
 *  ------------------------------------------------------------
 *  | No of insert    | MySQL Auto-incremental | Sequence Table |
 *  -------------------------------------------------------------
 *  |   1000          |     34                 |      68        |
 *  -------------------------------------------------------------
 *  |   10000         |    340                 |      689       |
 *  -------------------------------------------------------------
 *
 * </pre>
 *
 * For the above record, we decide to use the MySQL native auto-incremental.
 *
 * @package Php-Dao
 * @subpackage driver
 * @since  Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
*/
class DriverMysql extends Driver
{
    /**
     * the name of the driver
     * @var-read string the name of the driver
     * @version 1.0
     * @since Version 1.0
     */
    public $name = 'mysql';

    /**
     * String used to quote in mysql database
     * @var string
     * @version 1.0
     * @since Version 1.0
     */
    protected $_nameQuote = '`';

    /**
     *
     * Generate the connection string for PDO
     *
     * Function to implement abstract function Driver::getConnectionString()
     * @since   Version 1.0
     * (non-PHPdoc)
     * @see Driver::getConnectionString()
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function getConnectionString()
    {
        return  'mysql:dbname=' . $this->_database
                . ';host=' . $this->_options['dbhost'];
    }

    /**
     *
     * Get the drop table query
     *
     * @version 1.0
     * @since  Version 1.0
     * @param Clms\Tools\PhpDao\Dao $table Dao object
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function getDropTableQuery($table){

       return 'DROP TABLE IF EXISTS '
               .$this->quoteName($table->getTableName());
    }

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
    protected function getTestConnectionQuery()
    {
        return 'SELECT 1';
    }

    /**
     *
     * Function to get the last insert ID
     * implement abstract function Driver::insertid()
     * @return int last insert id
     * @since   Version 1.0
     *(non-PHPdoc)
     * @see Driver::insertid()
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected  function insertid()
    {
        $this->connect();

        // Error suppress this to prevent PDO warning us that the driver doesn't
        // support this operation.
        return @$this->_connection->lastInsertId();
    }

    /**
     *
     * Function to check if the mysql PDO is supported
     * @return boolean
     * @since  Version 1.0
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function isSupported()
    {
        return class_exists('\PDO') &&
               in_array('mysql', \PDO::getAvailableDrivers());
    }


    /**
     *
     * Convert the result property based on the column type on the Database
     *
     * Please note this function does not need the column definition.
     * Function to implement s the interface DriverMappable::mapType
     *
     * @since  Version 1.0
     * @param PDOStatement $statement The PDO statement just called
     * @param object $object
     * @see Drivermapper
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    /*
    public function propertyTypeMap($statement, &$object)
    {
        $columnCount= $statement->columnCount();
        foreach (range(0, $columnCount - 1) as $columnIndex) {
            $meta = $statement->getColumnMeta($columnIndex);
            $key= $meta['name'];
            $tempValue= $object->$key;

            if (!isset($meta['native_type'])) {
                $object->$key= (bool) $tempValue;
                continue;
            }

            $object->$key= $this-> convertBasedOnType($meta['native_type'], $tempValue);
        }

    }*/

    /**
     *
     * Convert the value based on database definition
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $type native database type
     * @param mixed $tempValue Data returned from database
     * @return mixed Returned value based on real type
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    /*
    private function convertBasedOnType($type, $tempValue)
    {
        $result = '';
        switch ($type) {

            case 'TINY':
            case 'SHORT':
            case 'LONG':
            case 'LONGLONG':
            case 'INT24':
                $result = (int) $tempValue;
                break;
            case 'DECIMAL':
            case 'DOUBLE':
            case 'FLOAT':
            case 'NEWDECIMAL':
                $result = floatval($tempValue);
                break;
            default:
                $result = $tempValue;
                break;
        }
        return $result;
    }*/

    /**
     *
     * Generate a create table statement.
     *
     * Implement Driver::getCreateTableQuery()
     * @version 1.0
     * @param \Clms\Tools\PhpDao\Dao $table Dao object
     * @return void|string a SQL statement string
     * @since   Version 1.0
     * @see Driver::getCreateTableQuery()
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getCreateTableQuery($table)
    {
        if (!$table instanceof Dao) {
            //wait for implement the error message
            return '';
        }

        $fields = $table->getColumnSpec();

        $query = 'CREATE TABLE ' . $this->quoteName($table->getTableName())
               . ' ( ';
        foreach ($fields as $key => $value) {
            $query .= PHP_EOL . $this->quoteName($key) . ' '
                   . $this->column2String($value) . ',';
        }
        $indexes = $table->getColumnIndex();
        //add the primary key

        $pk = $table->getPrimarykey();
        if (!empty($pk)) {
            $query .= PHP_EOL . 'PRIMARY KEY (' . $this->quoteName($pk) . ' ),';
        }

        $query = rtrim($query, ',');

        $query .= PHP_EOL
            . ') ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci';
        return $query;
    }


    /**
     *
     * Convert the Column into string
     *
     * Used by create table only
     * @version 1.0
     * @since Version 1.0
     * @param \Clms\Tools\PhpDao\Column\Column $column
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function column2String($column)
    {
        if ($column instanceof ColumnBool) {
            return ' TINYINT(1) ' . $this->createDefaultValueString($column);

        } elseif ($column instanceof ColumnChar) {

            return 'VARCHAR (' . $column->getLength() . ')'
                . $column->getNullString() . $this->createDefaultValueString($column, true);
        } elseif ($column instanceof ColumnDecimal) {
            return ' DECIMAL( ' . $column->getDecimalM() . ', '
                . $column->getDecimalD() . ') '
                . $this->createDefaultValueString($column);
        } elseif ($column instanceof ColumnText) {
            return ' text ';
        } else { //For ColumnInt and ColumnTimeStamp
            return 'INT(' . $column->getLength() . ')'
                . $column->getNullString() . $this->createDefaultValueString($column)
                . ($column->isAutoIncremental() ? ' AUTO_INCREMENT' : '');
        }
    }

    /**
     *
     * Create a default value field based on different platform
     *
     * @version 1.0
     * @since  Version 1.0
     * @param \Clms\Tools\PhpDao\Column\Column $column
     * @param boolean $shouldQuote If the default value shall be quoted
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    private function createDefaultValueString($column, $shouldQuote=false)
    {
       $value=$column->getDefaultValue();

       if($shouldQuote){
           $value=  "'". $value. "'";
       }
       return empty($value)? '': ' DEFAULT '. $value;
    }


    /**
     *
     * Create a drop index query SQL
     * (non-PHPdoc)
     * @version 1.0
     * @since Version 1.0
     * @param string $table table name
     * @param string $index index name
     * @return string
     * @see Driver::getDropIndexQuery()
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function getDropIndexQuery($table, $index)
    {
        return 'ALTER TABLE ' . $this->quoteName($table). ' DROP INDEX '
                . $this->quoteName($index);
    }


    /**
     *
     * Function description goes here
     * @version 1.0
     * @since Version 1.0
     * @param unknown $table
     * @param unknown $key
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function getDropPrimaryQuery($table, $key)
    {
        return 'ALTER TABLE'. $this->quoteName($table). ' DROP PRIMARY KEY';
    }
    /**
     *
     * Function to implement Driver::getDropUniqueQuery()
     * In mysql drop unique is equals with drop index
     * (non-PHPdoc)
     * @version 1.0
     * @since Version 1.0
     * @param string $table table name
     * @param string $key key name
     * @return string
     * @see Driver::getDropUniqueQuery()
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function getDropUniqueQuery($table, $key)
    {
        return $this->getDropIndexQuery($table, $key);
    }



    /**
     *
     * Get the next value based on sequence table implementation method
     *
     * @deprecated
     * @version 1.0
     * @since  Version 1.0
     * @param string $table Table name
     * @return mixed the integer for next value or failed if system error
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getSequencer($table)
    {
        $lmsSequencer= Sequence\Sequencer::getInstance($this, $table);
        return $lmsSequencer->next();
    }


    /**
     *
     * Create a sequence table
     *
     * Only used if the auto-incremental field implemented by sequence table
     *
     * @deprecated
     * @version 1.0
     * @since  Version 1.0
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function createSequencerTable()
    {
        $lmsSequencerDao= new Sequence\SequencerDao($this);
        $this->createTable($lmsSequencerDao);
    }


    /**
     *
     * Load data list from database
     *
     *
     * @version 1.0
     * @since  Version 1.0
     * @param string $tableName Table name
     * @param array $columnSpec Column specification
     * @param array $keys Search criteria as key/value pair
     * @param string $class Class name for return object
     * @param int $offset Search offset
     * @param int $limit Search limit
     * @param array $orderBy Search order by
     * @param array $relations Search operators
     * @param array $groupBy Search group by
     * @param array $aggregates Aggregates search
     * @return boolean|multitype:object
     * @see \Clms\Tools\PhpDao\Driver\Driver::loadObjectList()
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function loadObjectList($tableName, $columnSpec, $keys, $class,
            $offset, $limit, $orderBy, $relations, $groupBy, $aggregates)
    {
        // Initialize the query.
        $query = $this->getQuery(true);

        if(!empty($groupBy)){
            $query->select($groupBy);

            if (!empty($aggregates)) {
                foreach ($aggregates as $key => $value) {
                    $query->select($value. '('. $this->quoteName($key). ')', true);
                }
            }

            $queryGroupBy= array();
            foreach($groupBy as $key=> $value){
                $queryGroupBy[]= $this->quoteName($key);
            }
            $query->group($queryGroupBy);

        } else {
            $query->select('*');
        }

        $query->from($tableName);

        $validFields= array_keys($columnSpec);

        foreach ($keys as $key => $value) {
            if (!in_array($key, $validFields)) {
                //we ingnore the value not in definition
                continue;
            }
            if(isset($relations[$key]) && $relations[$key] === 'bt'){

            		if(!isset ($value['max']) || !isset($value['min'])) {
            			throw new DbException(DbException::DBERROR_WRONG_QUERY_PARAM_TYPE);
            		}
            		if($columnSpec[$key]->type==='text' || $columnSpec[$key]->type==='char'){

            			$query->where($this->quoteName($key) . ' between ' . $this->quoteName($value['min'])
            					. ' and '. $this->quoteName($value['max']) );
            		} else {
            			$query->where($this->quoteName($key) . ' between ' . $value['min']
            					. ' and '. $value['max'] );
            		}

            }  else {
                if (isset($relations[$key])) {
                    $relation =' '. $relations[$key] . ' :';

                    if ($relations[$key]==='like') {
                        $value= '%'. $value. '%';
                        $keys[$key]= '%'. $value. '%';
                    }
                } else {
                    $relation = '=:';
                }
            	$query->where($this->quoteName($key) . $relation . $key);

                $type= $columnSpec[$key]->getBindParamType();
                $query->bind(':'.$key, $keys[$key], $type, strlen($value));
            }

        }

        if(!empty($orderBy)){
            $queryOrderBy= array();
            foreach($orderBy as $key=> $value){
                $queryOrderBy[]= $this->quoteName($key). ' '. $value;
            }

            $query->order($queryOrderBy);
        }

        $query->limit($offset, $limit);

        $this->setQuery($query);

        $results= array();

        // Execute the query and get the result set cursor.
        $cursor = $this->execute();

        if(empty($groupBy)){
            //Get all of the rows from the result set as objects of type $class.
            while ($row= $this->fetchObject($cursor, $class)) {
                $results[] = $row;
            }
        } else {
            //Get all of the rows from the result set as objects of type $class.
            while ($row= $this->fetchObject($cursor)) {
                foreach($row as $key => $value){
                    if(isset($columnSpec[$key])){
                        $row->$key=$this->matchObjectType($columnSpec[$key]->type, $value);
                    } else {
                        $row->$key= (int)$value;
                    }
                }
                $results[] = $row;
            }
        }



        // Free up system resources and return.
        $this->freeResult($cursor);

        return $results;
    }
}

/* ---------------------------------------------------------------
   Interface definition
   --------------------------------------------------------------- */

/* ===============================================================
   End of DriverMysql.php
   =============================================================== */