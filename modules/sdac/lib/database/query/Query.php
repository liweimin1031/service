<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : Query.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Lms Database Query Builder Base Class
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 83 $)
 * CREATED     : Dec 7, 2012
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-01-13 16:30:26 +0800 (週二, 13 一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) Query.php              1.0 Dec 7, 2012
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
   Begin of Query.php
   =============================================================== */
namespace Clms\Tools\PhpDao\Query;

/* ---------------------------------------------------------------
   Included Library
   --------------------------------------------------------------- */
//require_once(dirname(__FILE__). LMSDS.'QueryParameter.php');

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
 * Lms Database Query Builder
 *
 * Please note when build a query, you cannot mix insert, update, delete
 * and select method calls.
 *
 * The class is used to build a sql query and abstract the different
 * implementation under different database. It is recommended that developers
 * call this class to build queries instead of written a SQL statement string
 * directly.
 * @todo add some examples here
 * @package Php-Dao
 * @subpackage query
 * @version 1.0
 * @since Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
abstract class Query
{
     /**
      * @var Driver An instance of Lms Database Driver
      * @version 1.0
      * @since  1.0
      */
     protected $_db= null;

     /**
      * @var string SQl String
      * @version 1.0
      * @since  1.0
      */
     protected $_sql= null;

     /**
      * @var mixed The param need to be bind in the query
      * @version 1.0
      * @since 1.0
      */
     protected $_bounded = array();
     /**
      *
      * @var mixed The columns bounded using in bindColumns
      * @version 1.0
      * @since Version 1.0
      */
     protected $_boundColumns= array();
     /**
      * @var string Query type
      * @version 1.0
      * @since  1.0
      */
     protected $_type= '';
     /**
      * @var    QueryParameter  The select element of the query
      * @version 1.0
      * @since  1.0
      */
     protected $_selectPara = null;

     /**
      * @var    QueryParameter  The delete element of the query
      * @version 1.0
      * @since  1.0
      */
     protected $_deletePara = null;

     /**
      * @var    QueryParameter  The update element of the query
      * @version 1.0
      * @since  1.0
      */
     protected $_updatePara = null;

     /**
      * @var    QueryParameter The insert element of the query
      * @version 1.0
      * @since  1.0
      */
     protected $_insertPara = null;

     /**
      * @var    QueryParameter The from element of the query
      * @version 1.0
      * @since  1.0
      */
     protected $_fromPara = null;

     /**
      * @var    QueryParameter The set element of the query
      * @version 1.0
      * @since  1.0
      */
     protected $_setPara = null;

     /**
      * @var    QueryParameter  The where element of the query
      * @version 1.0
      * @since  1.0
      */
     protected $_wherePara = null;

     /**
      * @var    QueryParameter  The group by element of the query
      * @version 1.0
      * @since  1.0
      */
     protected $_groupPara = null;

     /**
      * @var    QueryParameter  The having element of the query
      * @version 1.0
      * @since  1.0
      */
     protected $_havingPara = null;

     /**
      * @var    QueryParameter  The column list for an INSERT statement
      * @version 1.0
      * @since  1.0
      */
     protected $_columnsPara = null;

     /**
      * @var    QueryParameter  The values list for an INSERT statement
      * @version 1.0
      * @since  1.0
      */
     protected $_valuesPara = null;

     /**
      * @var    QueryParameter  The order element of the query
      * @version 1.0
      * @since  1.0
      */
     protected $_orderPara = null;


     /**
      * @var integer Query result limit
      * @version 1.0
      * @since 1.0
      */
     protected $_limit;

     /**
      * @var integer Query result offset
      * @version 1.0
      * @since 1.0
      */
     protected $_offset;

     /**
      *
      * Process the limit offset parameter
      *
      * Due to different database, the process is different. For details,
      * please check the implementation if different Query.
      * @param string $sql a sql string without limit offset
      * @param int $limit the limit of result
      * @param int $offset the offset of the result
      * @return string a sql statement string with limit and offset
      * @version 1.0
      * @since  Version 1.0
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     abstract public function processLimit($sql, $limit, $offset = 0);

     /**
      *
      * Lms Database Query constructors
      *
      * @since  Version 1.0
      * @param Driver $db a database driver instance
      * @see
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function __construct($db)
     {
         $this->_db = $db;
     }



     /**
      * Convert the query into a string
      *
      * This is the most important function in this class. The function
      * internally calls the QueryParameter to convert all the elements
      * into string
      *
      * @return string a sql string
      * @since  Version 1.0
      * @version 1.0
      * @see QueryParameter->_toString()
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function __toString()
     {
         $query = '';

         if ($this->_sql) {
             return $this->_sql;
         }
         switch ($this->_type)
         {
             case 'select':
                 $query .= (string) $this->_selectPara;
                 $query .= (string) $this->_fromPara;

                 if ($this->_wherePara) {
                     $query .= (string) $this->_wherePara;
                 }

                 if ($this->_groupPara) {
                     $query .= (string) $this->_groupPara;
                 }

                 if ($this->_havingPara) {
                     $query .= (string) $this->_havingPara;
                 }

                 if ($this->_orderPara) {
                     $query .= (string) $this->_orderPara;
                 }

                 break;


             case 'delete':
                 $query .= (string) $this->_deletePara;
                 $query .= (string) $this->_fromPara;


                 if ($this->_wherePara) {
                     $query .= (string) $this->_wherePara;
                 }


                 break;

             case 'update':
                 $query .= (string) $this->_updatePara;



                 $query .= (string) $this->_setPara;

                 if ($this->_wherePara) {
                     $query .= (string) $this->_wherePara;
                 }

                 break;

             case 'insert':
                 $query .= (string) $this->_insertPara;


                 // Set method
                 if ($this->_setPara) {
                     $query .= (string) $this->_setPara;
                 } elseif ($this->_valuesPara) {   // Columns-Values method
                     if ($this->_columnsPara) {
                         $query .= (string) $this->_columnsPara;
                     }

                     $elements = $this->_valuesPara->getElements();
                     if (!($elements[0] instanceof $this)) {
                         $query .= ' VALUES ';
                     }

                     $query .= (string) $this->_valuesPara;
                 }

                 break;

         }

         $query = $this->processLimit($query, $this->_limit, $this->_offset);
         return $query;
     }

     /**
      *
      * Get the query operation type, such as insert, update, delete.
      * @return string type operation type
      * @since  Version 1.0
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function getType()
     {
         return $this->_type;
     }


     /**
      *
      * Clear all the parameters in the current query
      *
      * Usage:
      * <code>$query->reset('select');</code>
      * <code>$query->reset();</code>
      * @version 1.0
      * @since  Version 1.0
      * @param string $clause Optionally, the name of the clause to clear,
      *                       or nothing to clear the whole query.
      * @return Query An empty Query
      * @see
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function reset($clause = null)
     {
         $this->_sql = null;

         switch ($clause)
         {
             case 'select':
                 $this->_selectPara = null;
                 $this->_type = null;
                 break;

             case 'delete':
                 $this->_deletePara = null;
                 $this->_type = null;
                 break;

             case 'update':
                 $this->_updatePara = null;
                 $this->_type = null;
                 break;

             case 'insert':
                 $this->_insertPara = null;
                 $this->_type = null;
                 break;

             case 'from':
                 $this->_fromPara = null;
                 break;

             case 'set':
                 $this->_setPara = null;
                 break;

             case 'where':
                 $this->_wherePara = null;
                 break;

             case 'group':
                 $this->_groupPara = null;
                 break;

             case 'having':
                 $this->_havingPara = null;
                 break;

             case 'order':
                 $this->_orderPara = null;
                 break;

             case 'columns':
                 $this->_columnsPara = null;
                 break;

             case 'values':
                 $this->_valuesPara = null;
                 break;

             case 'limit':
                 $this->_offset = 0;
                 $this->_limit = 0;
                 break;

             default:
                 $this->_bounded = array();
                 $this->_type = null;
                 $this->_selectPara = null;
                 $this->_deletePara = null;
                 $this->_updatePara = null;
                 $this->_insertPara = null;
                 $this->_fromPara = null;
                 $this->_setPara = null;
                 $this->_wherePara = null;
                 $this->_groupPara = null;
                 $this->_havingPara = null;
                 $this->_orderPara = null;
                 $this->_columnsPara = null;
                 $this->_valuesPara = null;
                 $this->_offset = 0;
                 $this->_limit = 0;
                 break;
         }

         return $this;
     }

     /**
      * Set the "INSERT" parameters
      *
      * @param string $table table name
      * @return Query an Query instance for chaining setting
      * @since  Version 1.0
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function insert($table)
     {
         $this->_type = 'insert';
         $this->_insertPara = new QueryParameter('INSERT INTO', $table);

         return $this;
     }

     /**
      *
      * Set the "UPDATE" parameters
      *
      * @version 1.0
      * @since  Version 1.0
      * @param string $table the database name to update
      * @return Query an Query instance for chaining setting
      * @see
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function update($table)
     {
         $this->_type = 'update';
         $this->_updatePara = new QueryParameter('UPDATE', $table);

         return $this;
     }

     /**
      *
      * Set the "SET" parameters
      *
      * Usage
      * @todo add the usage example
      * @version 1.0
      * @since  Version 1.0
      * @param mixed $conditions the conditions to set
      * @param string $glue the connectors between set paramters
      * @return Query an Query instance for chaining setting
      * @see
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function set($conditions, $glue = ',')
     {
         if (is_null($this->_setPara)) {
             $glue = strtoupper($glue);
             $this->_setPara = new QueryParameter('SET', $conditions, "\n\t$glue ");
         } else {
             $this->_setPara->append($conditions);
         }

         return $this;
     }

     /**
      *
      * Delete statement start
      *
      * @param string $table table name
      * @return Query an Query instance for chaining setting
      * @since  Version 1.0
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function delete($table = null)
     {
         $this->_type = 'delete';
         $this->_deletePara = new QueryParameter('DELETE', null);

         if (!empty($table)) {
             $this->from($table);
         }
         return $this;
     }

     /**
      *
      * Create a column list.
      *
      * Usage:
      * @todo add the usage sample here
      *
      * @param mixed  $columns  A column name, or array of column names.
      * @return Query an Query instance for chaining setting
      * @since  Version 1.0
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function columns($columns)
     {
         if (is_null($this->_columnsPara)) {
             $this->_columnsPara = new QueryParameter('()', $columns);
         } else {
             $this->_columnsPara->append($columns);
         }

         return $this;
     }

     /**
      * Set the "VALUES" parameters
      *
      * Adds a tuple or array of tuples, used as values for an
      * INSERT INTO statement.
      *
      * Usage:
      * <code>
      * $query->values('1,2,3')->values('4,5,6');
      * </code>
      * @param string|array $values a single tuple or array of tuples
      * @return Query an Query instance for chaining setting
      * @since  Version 1.0
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function values($values)
     {
         if (is_null($this->_valuesPara)) {
             $this->_valuesPara = new QueryParameter('()', $values, '),(');
         } else {
             $this->_valuesPara->append($values);
         }

         return $this;
     }

     /**
      *
      * Set the "SELECT" parameters
      *
      * The query is set as type select.
      *
      * Usage:
      * <code>
      * $query->select('course.*')->select('class.id');
      * </code>
      * @since  Version 1.0
      * @param mixed $columns A string or an array of field names.
      * @param boolean $isAggregated If the select is an aggregated query
      * @return Query an Query instance for chaining setting
      * @see
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function select($columns, $isAggregated=false)
     {
         $this->_type = 'select';

         if (!$isAggregated) {
             $columns= $this->quoteName($columns);
         }


         if (is_null($this->_selectPara)) {
             $this->_selectPara = new QueryParameter('SELECT', $columns);
         } else {
             $this->_selectPara->append($columns);
         }

         return $this;
     }

     /**
      *
      * Set the "WHERE" parameters
      *
      * The could be used in various query types, such as select, set, etc.
      *
      * Usage:
      * <code>
      * $query->where('course.id=1');
      * $query->where(array('grade=5', 'subject=4'));
      * </code>
      * @since  Version 1.0
      * @param mixed $conditions A string or array of conditions
      * @param string $glue
      * @return Query an Query instance for chaining setting
      * @see
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function where ($conditions, $glue = 'AND')
     {
         if (is_null($this->_wherePara)) {
             $glue = strtoupper($glue);
             $this->_wherePara = new QueryParameter('WHERE', $conditions, " $glue ");
         } else {
             $this->_wherePara->append($conditions);
         }
         return $this;
     }


     /*
     public function getNormalCondition($key, $value , $condition= '=')
     {
         return $this->quoteName($key) . $condition
             . (is_string($value)? $this->quoteName($value): $value);
     }

     public function getInCondition ($expression, $inArray)
     {
         $condition= $this->quoteName($expression) . ' IN (';
         foreach ($inArray as $value) {
             $condition .= (is_string($value)? $this->quoteName($value): $value)
                        .',' ;
         }

         $condition= rtrim($condition, ',');
         $condition .=') ';

         return $condition;
     }*/

     /**
      *
      * Set the "FROM" parameters
      *
      * If an array of tables is given, please use join operation
      *
      * Usage:
      * <code> $query->select('*')->from('#__a');</code>
      * @version 1.0
      * @since Version 1.0
      * @param mixed $tables  A string or array of table names.
      *                       This can be a QueryParameter object (or
      *                       a child of it) when used as a subquery in
      *                       FROM clause along with a value for $subQueryAlias.
      * @param string $subQueryAlias Alias used when $tables is a Query.
      * @return Query an Query instance for chaining setting
      * @see
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function from($tables, $subQueryAlias = null)
     {
         if ($tables instanceof $this) {
             if (is_null($subQueryAlias)) {
                 //no subquery alias give an error msg
             }

             $tables = '( ' . (string) $tables . ' ) AS '
                       . $this->quoteName($subQueryAlias);
         } else {
             $tables= $this->quoteName($tables, $subQueryAlias);
         }

         if (is_null($this->_fromPara)) {
             $this->_fromPara = new QueryParameter('FROM', $tables);
         } else {
             $this->_fromPara->append($tables);
         }

         return $this;
     }

     /**
      *
      * Set the "ORDER BY" parameters in the query
      *
      * @param mixed $columns order by columns
      * @return Query an Query instance for chaining setting
      * @since  Version 1.0
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function order($columns)
     {
         if (is_null($this->_orderPara)) {
             $this->_orderPara = new QueryParameter('ORDER BY', $columns);
         } else {
             $this->_orderPara->append($columns);
         }

         return $this;
     }

     /**
      *
      * Set the "GROUP BY" parameters in the query
      *
      * @version 1.0
      * @since  Version 1.0
      * @param array $columns
      * @return Query
      * @see
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function group($columns)
     {
         if (is_null($this->_groupPara)) {
             $this->_groupPara = new QueryParameter('GROUP BY', $columns);
         } else {
             $this->_groupPara->append($columns);
         }

         return $this;
     }


     /**
      *
      * Put a element into bind array.
      *
      * The bind elements are used for set and search parameters
      * Function to implement interface DbQeuryPreparable::bind()
      *
      * Usage:
      * <code>
      *    $query->bind('key', 'this is the value');
      * </code>
      *
      * (non-PHPdoc)
      * @see DbQeuryPreparable::bind()
      * @since Version 1.0
      * @param string|integer $key  The key that will be used in your
      *                              SQL query to reference the value.
      *                              Usually of the form ':key', but can
      *                              also be an integer.
      * @param mixed $value  The value that will be bound. The value is
      *                      passed by reference to support output
      * @param integer $dataType Constant corresponding to a SQL data type.
      * @param integer $length The length of the variable. Usually required
      *                        for OUTPUT parameters.
      * @param array $driverOptions PDO driver options
      * @return Query an Query instance for chaining setting
      * @see
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function bind($key = null, &$value = null,
                          $dataType = \PDO::PARAM_STR,
                          $length = 0, $driverOptions = array())
     {
         // Ignore the empty key
         // This will throw exception when query
         if (empty($key)) {
             $this->_bounded = array();
             return $this;
         }

         // Ignore the empty value field
         /*if (is_null($value)) {
             if (isset($this->_bounded[$key])) {
                 unset($this->_bounded[$key]);
             }

             return $this;
         }*/

         $obj = new \stdClass;

         $obj->value = &$value;
         $obj->dataType = $dataType;
         $obj->length = $length;
         $obj->driverOptions = $driverOptions;

         //Add the value to bounded array
         $this->_bounded[$key] = $obj;

         return $this;
     }

     /**
      * Get the bounded element in this query
      *
      * Function to implement the interface DbQeuryPreparable::getBounded()
      *
      * (non-PHPdoc)
      * @see DbQeuryPreparable::getBounded()
      * @since Version 1.0
      * @param mixed  $key  The bounded variable key to retrieve.
      * @return mixed a array of bounded variable or object if key is provided
      * @see
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function &getBounded($key = null)
     {
         if (empty($key)) {
             return $this->_bounded;
         } else {
             if (isset($this->_bounded[$key])) {
                 return $this->_bounded[$key];
             }
         }
     }

     /**
      *
      * Set the SQL statement directly to the query
      *
      * Use by Driver to set a query. Please do not set the query through this
      * function.
      *
      *
      * @param string SQL string
      * @return Query an Query instance for chaining setting
      * @since  Version 1.0
      * @see
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function setQuery($sql)
     {
         $this->_sql = $sql;

         return $this;
     }

     /**
      *
      * Set the limit or offset of a query
      *
      * @version 1.0
      * @since  Version 1.0
      * @param int $offset the offset of the query
      * @param int $limit the limit of the query
      * @return Query an Query instance for chaining setting
      * @see
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function limit($offset = 0, $limit = 0)
     {
         $this->_limit = (int) $limit;
         $this->_offset = (int) $offset;

         return $this;
     }

     /**
      *
      * Get the quoted name of a column or table name to
      * avoid database injection
      *
      * @version 1.0
      * @since Version 1.0
      * @param string $name a name need to be quoted, such as column name
      * @return string a quoted name of table or columns
      * @see Driver->quoteName
      * @author      Michelle Hong
      * @testing
      * @warnings
      * @updates
      */
     public function quoteName($name)
     {
         return $this->_db->quoteName($name);
     }


}


/* ===============================================================
   End of Query.php
   =============================================================== */