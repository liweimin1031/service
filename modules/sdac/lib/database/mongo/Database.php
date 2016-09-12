<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : Database.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Default Description
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 4780 $)
 * CREATED     : Aug 3, 2016
 * LASTUPDATES : $Author: csdhong $ on $Date: 10:45:38 AM Aug 3, 2016 $
 * UPDATES     : 
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) Database.php              1.0 Aug 3, 2016
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
   Begin of Database.php
   =============================================================== */
namespace Clms\Tools\PhpDao\Mongo;
use MongoDB\Driver\Command;
use MongoDB\Driver\Manager;
use MongoDB\Driver\ReadConcern;
use MongoDB\Driver\ReadPreference;
use MongoDB\Driver\WriteConcern;

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
class Database
{
    private static $_defaultTypeMap = [
            'array' => 'MongoDB\Model\BSONArray',
            'document' => 'MongoDB\Model\BSONDocument',
            'root' => 'MongoDB\Model\BSONDocument',
    ];
    private $_databaseName;
    private $_manager;
    private $_readConcern;
    private $_readPreference;
    private $_typeMap;
    private $_writeConcern;
    /**
     * Constructs new Database instance.
     *
     * This class provides methods for database-specific operations and serves
     * as a gateway for accessing collections.
     *
     * Supported options:
     *
     *  * readConcern (MongoDB\Driver\ReadConcern): The default read concern to
     *    use for database operations and selected collections. Defaults to the
     *    Manager's read concern.
     *
     *  * readPreference (MongoDB\Driver\ReadPreference): The default read
     *    preference to use for database operations and selected collections.
     *    Defaults to the Manager's read preference.
     *
     *  * typeMap (array): Default type map for cursors and BSON documents.
     *
     *  * writeConcern (MongoDB\Driver\WriteConcern): The default write concern
     *    to use for database operations and selected collections. Defaults to
     *    the Manager's write concern.
     *
     * @param Manager $manager      Manager instance from the driver
     * @param string  $databaseName Database name
     * @param array   $options      Database options
     * @throws InvalidArgumentException
     */
    public function __construct(Manager $manager, $databaseName, array $options = [])
    {
        if (strlen($databaseName) < 1) {
            throw new InvalidArgumentException('$databaseName is invalid: ' . $databaseName);
        }
        if (isset($options['readConcern']) && ! $options['readConcern'] instanceof ReadConcern) {
            throw InvalidArgumentException::invalidType('"readConcern" option', $options['readConcern'], 'MongoDB\Driver\ReadConcern');
        }
        if (isset($options['readPreference']) && ! $options['readPreference'] instanceof ReadPreference) {
            throw InvalidArgumentException::invalidType('"readPreference" option', $options['readPreference'], 'MongoDB\Driver\ReadPreference');
        }
        if (isset($options['typeMap']) && ! is_array($options['typeMap'])) {
            throw InvalidArgumentException::invalidType('"typeMap" option', $options['typeMap'], 'array');
        }
        if (isset($options['writeConcern']) && ! $options['writeConcern'] instanceof WriteConcern) {
            throw InvalidArgumentException::invalidType('"writeConcern" option', $options['writeConcern'], 'MongoDB\Driver\WriteConcern');
        }
        $this->_manager = $manager;
        $this->_databaseName = (string) $databaseName;
        $this->_readConcern = isset($options['readConcern']) ? $options['readConcern'] : $this->_manager->getReadConcern();
        $this->_readPreference = isset($options['readPreference']) ? $options['readPreference'] : $this->_manager->getReadPreference();
        $this->_typeMap = isset($options['typeMap']) ? $options['typeMap'] : self::$_defaultTypeMap;
        $this->_writeConcern = isset($options['writeConcern']) ? $options['writeConcern'] : $this->_manager->getWriteConcern();
    }
    /**
     * Return the database name.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_databaseName;
    }
    /**
     * Returns the database name
     *
     * @return string
     */
    public function getDatabaseName()
    {
        return $this->_databaseName;
    }
}


/* ===============================================================
   End of Database.php
   =============================================================== */
?>