<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   :DriverOracle.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Default Description
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 83 $)
 * CREATED     : 2012-12-8
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-01-13 16:30:26 +0800 (週二, 13 一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
 @(#) DriverOracle.php              1.0 2012-12-8
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
   Begin of DriverOracle.php
   =============================================================== */

namespace Clms\Tools\PhpDao\Driver;

use Clms\Tools\PhpDao\Exception\DbException;

use Clms\Tools\PhpDao\Driver\Sequence\Sequencer;
use Clms\Tools\PhpDao\Driver\Sequence\SequencerDao;
use Clms\Tools\PhpDao\Dao;
use Clms\Tools\PhpDao\Query\QueryParameter;
use Clms\Tools\PhpDao\Column\Column;
use Clms\Tools\PhpDao\Column\ColumnInt;
use Clms\Tools\PhpDao\Column\ColumnTimeStamp;
use Clms\Tools\PhpDao\Column\ColumnBool;
use Clms\Tools\PhpDao\Column\ColumnChar;
use Clms\Tools\PhpDao\Column\ColumnDecimal;
use Clms\Tools\PhpDao\Column\ColumnText;

/* ---------------------------------------------------------------
 Included Library
--------------------------------------------------------------- */

//require_once(dirname(__FILE__). LMSDS.'Driver.php');
//require_once(dirname(__FILE__). LMSDS.'DriverSequenceable.php');
//require_once(dirname(dirname(__FILE__)). LMSDS.'Sequencer.php');
//require_once(dirname(dirname(__FILE__)). LMSDS.'query'. LMSDS.'QueryOracle.php');



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
 *
 * Lms Database driver for oracle database
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
 *  ------------------------------------------------------
 *  | No of insert    | Oracle Sequence | Sequence Table |
 *  ------------------------------------------------------
 *  |   1000          |     10          |      10        |
 *  ------------------------------------------------------
 *
 *  Since the performance is similar, we decide to use our own implementation
 *  for auto_incremental table on oracle due to easy data migration and other
 *  data base adaption. Here is the sample code for get value from oracle's
 *  sequence next value
 *  <code>
 *  $this->setQuery('SELECT CLOUD.LMS_SAMPLE_SEQUENCER.NEXTVAL AS nextInsertID FROM DUAL');
    $this->execute();
    $result= $this->fetchArray();
    $next = $result[0];
 *  </code>
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
class DriverOracle extends Driver implements DriverSequenceable
{
    /**
     * The name of the driver
     * @var string
     */
    public $name= 'oracle';

    /**
     *
     * @var string The string for quote the tables column
     * @version 1.0
     * @since Version 1.0
     */
    protected $_nameQuote = '"';


    /**
     *
     * Generate a sql statement for get the database connection string
     *
     * Function to implement abstract function Driver::getConnectionString()
     * @since   Version 1.0
     *(non-PHPdoc)
     * @see Driver::getConnectionString()
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function getConnectionString()
    {
        $tns= "//".$this->_options['dbhost'].":1521/"
              . $this->_database. ';charset=utf8';
        return  'oci:dbname=' . $tns;
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
        return 'SELECT 1 FROM DUAL';
    }
    /**
     *
     * If the database is supported
     * @return boolean
     * @since  Version 1.0
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function isSupported()
    {
        return class_exists('\PDO') && in_array('oci', \PDO::getAvailableDrivers());
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
        //$this->connect();

        // Error suppress this to prevent PDO warning us that the driver doesn't
        // support this operation.
        return null;
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

        return 'BEGIN'
               . "    EXECUTE IMMEDIATE 'DROP TABLE ".$this->quoteName($table->getTableName())."';"
               . "EXCEPTION"
               . "    WHEN OTHERS THEN"
               . "        IF SQLCODE != -942 THEN"
               . "            RAISE;"
               . "        END IF;"
               . "END;";
    }


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
            return;
        }

        $fields = $table->getColumnSpec();

        $query = 'CREATE TABLE ' . $this->quoteName($table->getTableName())
        . ' ( ';

        $pk = $table->getPrimarykey();

        foreach ($fields as $key => $value) {
            if (!empty($pk) && $pk=== $key) {
                $pkstring= ' PRIMARY KEY ';
            } else {
                $pkstring= '';
            }
            $query .= PHP_EOL . $this->quoteName($key) . ' '
                    . $this->column2String($value) . $pkstring .',';
        }


        $query = rtrim($query, ',');

        $query .= PHP_EOL
        . ')';
        return $query;

        /*$sql= ' CREATE SEQUENCE lmsobject_seq START WITH 1
         INCREMENT BY 1
        CACHE 10';

        $sql= 'CREATE OR REPLACE TRIGGER trigger_lmsobject
        BEFORE INSERT ON lmsobject
        FOR EACH ROW
        BEGIN
        :new.id := lmsobject_seq.nextval;
        END;';*/
        //return $sql;

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
    protected function Column2String($column)
    {
        if ($column instanceof ColumnBool) {
            return ' NUMBER(1) '. $this->createDefaultValueString($column);
        } elseif ($column instanceof ColumnChar) {
            return ' VARCHAR2 ('. $column->getLength().')'
                    . $this->createDefaultValueString($column)
                    . $column->getNullString();
        } elseif ($column instanceof ColumnDecimal) {
            return ' DECIMAL( '. $column->getDecimalM() . ', '
                   . $column->getDecimalD().') '
                   . $this->createDefaultValueString($column);
        } elseif ($column instanceof  ColumnText) {
            return ' CLOB ';
        } else {
            //For ColumnInt and ColumnTimeStamp
            return ' NUMBER('. $column->getLength(). ')'
                   . $this->createDefaultValueString($column)
                   . $column->getNullString();
        }
    }

    /**
     *
     * Create a default value field based on different platform
     *
     * @version 1.0
     * @since  Version 1.0
     * @param \Clms\Tools\PhpDao\Column\Column $column
     * @return string
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    private function createDefaultValueString($column)
    {
        $value=$column->getDefaultValue();
        return empty($value)? '': " DEFAULT '"
                . $value. "'";
    }



    /**
     *
     * Generate a sql statement for drop primary key
     * (non-PHPdoc)
     * @version 1.0
     * @since Version 1.0
     * @param string $table table name
     * @param string $key primary key name
     * @return string the sql statement for drop primary key
     * @see Driver::getDropPrimaryQuery()
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function getDropPrimaryQuery($table, $key)
    {
        return $this->getDropContaintQuery($table, $key);
    }

    /**
     *
     * Generate a sql statement for drop unique index
     * (non-PHPdoc)
     * @version 1.0
     * @since Version 1.0
     * @param string $table table name
     * @param string $key unique index name
     * @return string sql statement for drop unique index
     * @see Driver::getDropUniqueQuery()
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function getDropUniqueQuery($table, $key)
    {
        return $this->getDropContaintQuery($table, $key);
    }

    /**
     *
     * Generate a sql statement for drop a index
     * (non-PHPdoc)
     * @version 1.0
     * @since Version 1.0
     * @param string $table table name
     * @param string $index index name
     * @return string a sql statement for drop index
     * @see Driver::getDropIndexQuery()
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function getDropIndexQuery($table, $index)
    {
        return 'DROP INDEX '+ $index;
    }

    /**
     *
     * Generate a sql statement for drop a constraint from a table
     * @version 1.0
     * @since Version 1.0
     * @param string $table table name
     * @param string $key constraint name
     * @return string sql statement for drop constraint
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    private function getDropContaintQuery($table, $key)
    {
        return 'ALTER TABLE '. $this->quoteName($table)
               .' DROP CONSTRAINT '. $this->quoteName($columns);

    }
    /**
     *
     * Get the sequence number
     *
     * Implement interface DriverSequenceable::getSequencer
     * @version 1.0
     * @since Version 1.0
     * @param string $table
     * @see DriverSequenceable::getSequencer
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
     * Create the sequencer table
     *
     * This function is used when setup the database only
     * (non-PHPdoc)
     * @version 1.0
     * @since Version 1.0
     * @see DriverSequenceable::createSequencerTable()
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
     * Due to the difficulty of reading the stream(clob field), the
     * implementation for oracle loadList is to reset offset and limit as 1
     * each time.
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
     * @param array $relations Search operator, such as bigger than , etc.
     * @param array $groupBy Search group by
     * @param array $aggregates Aggregates search
     * @return false|array return an PDO array of standard class array (aggregate)
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

        $columnArray= array();
        $aggregateArray=array();
        $position=1;
        if(!empty($groupBy)){
            foreach($groupBy as $group){
                $query->select($group);
                $columnArray[$group]= $group;
                $position++;
            }

            if (!empty($aggregates)) {
                foreach ($aggregates as $key => $value) {
                    $aggregateArray[$position]= $value. '('. $this->quoteName($key). ')';
                    $position++;
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
        $validFields = array_keys($columnSpec);


        foreach ($keys as $key => $value) {
            if (!in_array($key, $validFields)) {
                //we ingnore the value not in definition
                continue;
            }
            if(isset($relations[$key])){
                if($relations[$key] === 'bt'){
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
                } else {
                    if ($relations[$key] === 'like') {
                        $value = '%' . $value . '%';
                        $keys[$key] = '%' . $value . '%';
                    }
                    $query
                    ->where(
                            $this->quoteName($key) .' '. $relations[$key] .' '. $value
                    );

                }
            }
            else {
                $relation= '=:';
                $query->where($this->quoteName($key) . '=:' . $key);
                $type = $columnSpec[$key]->getBindParamType();
                $query->bind(':' . $key, $keys[$key], $type, strlen($value));
            }
        }

        if (!empty($orderBy)) {
            $queryOrderBy = array();
            foreach ($orderBy as $key => $value) {
                $queryOrderBy[] = $this->quoteName($key) . ' ' . $value;
            }
            $query->order($queryOrderBy);
        }

        $query->limit($offset, $limit);
        $this->setQuery($query);
        $result = array();
        $cursor = $this->execute();


        //if we are doing a aggregate function
        if (!empty($groupBy)) {
            foreach($columnArray as $column) {
                 $cursor->bindColumn($column, $$column, $columnSpec[$column]->getBindColumnType());

            }
            foreach ($aggregateArray as $key=> $column) {
                     $cursor->bindColumn($key, $$column, \PDO::PARAM_INT);
            }
        //for normal query, bind all column
        } else {
            foreach($columnSpec as $column =>$columnDetail){
                $cursor->bindColumn($column, $$column, $columnDetail->getBindColumnType());
            }

        }

        while ($row = $cursor->fetch(\PDO::FETCH_BOUND)) {

            if(!empty($groupBy)){
                $data= new \stdClass();
                foreach($columnArray as $column) {
                    if ($columnSpec[$column]->type==='text') {
                        $data->$column=$this->matchObjectType('text', $$column);
                    } else {
                        $data->$column= $$column;
                    }
                }
                foreach ($aggregateArray as $column) {
                    $data->$column= $$column;
                }

            }
            else {
                $data=new $class;
                foreach($columnSpec as $column =>$columnDetail){
                    if($columnDetail->type==='text'){
                        $data->$column=$this->matchObjectType('text', $$column);
                    } else {
                        $data->$column= $$column;
                    }
                }
            }
            $result[]=$data;
        }

        // Free up system resources and return.
        $this->freeResult($cursor);

        return $result;

    }

}

/* ---------------------------------------------------------------
   Interface definition
   --------------------------------------------------------------- */

/* ===============================================================
   End of DriverOracle.php
   =============================================================== */