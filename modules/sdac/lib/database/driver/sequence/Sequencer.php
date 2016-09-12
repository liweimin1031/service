<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : Sequencer.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Lms Database Sequencer to implement the auto-incremental column
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 83 $)
 * CREATED     : Dec 19, 2012
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-01-13 16:30:26 +0800 (週二, 13 一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) Sequencer.php              1.0 Dec 19, 2012
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
   Begin of Sequencer.php
   =============================================================== */

namespace Clms\Tools\PhpDao\Driver\Sequence;

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
 * Lms Database Sequencer class definition
 * This is a Lms Database self
 * implement database sequence through a table.
 *
 * This method is cross database platform.
 * Each table with an auto-incremental field should have a row in the database
 * sequence table and hold the seed for sequence generation.
 *
 * Used by those database which does not support auto-incremental
 * @package Php-Dao
 * @subpackage driver
 * @since  Version 1.0
 * @see SequencerDao
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class Sequencer
{

    /**
     * Max Keys for a seed. The default value is 1. As a consequence, we do
     * reseed whenever there is a calculation of next value
     * @var int
     * @version 1.0
     * @since Version 1.0
     */
    const  MAX_KEYS = 1;

    /**
     * @var array All sequencers currently in memory
     * @static
     * @version 1.0
     * @since Version 1.0
     */
    private static $_sequencers  = array();

    /**
     * @var string The name of this sequencer.
     * @version 1.0
     * @since Version 1.0
     */
    private $_name = null;

    /**
     * @var int Sequence number used in this sequence
     * @version 1.0
     * @since Version 1.0
     */
    private $_sequence =0;

    /**
     * @var SequencerDao An instance of SequencerDao
     * @version 1.0
     * @since Version 1.0
     */
    private $_sequenceObj = null;

    /**
     *
     * Constructor for a sequencer for a certain table
     * @version 1.0
     * @since Version 1.0
     * @param Driver $db a database driver instance
     * @param string $tableName table name
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    private function __construct( $db,  $tableName)
    {
        $this->_name= $tableName;
        $this->_sequenceObj= new SequencerDao($db, $tableName);
    }

    /**
     *
     * Get an instance of Sequencer
     * @version 1.0
     * @since Version 1.0
     * @param Driver $db Database instance
     * @param string $tableName table name
     * @return Sequencer
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public static function getInstance($db,  $tableName)
    {
        if (empty(self::$_sequencers[$tableName])) {

            // Create our new Driver connector based
            // on the options given.
            $instance = new Sequencer($db, $tableName);

            // Set the new connector to the global instances based on signature.
            self::$_sequencers[$tableName] = $instance;
        }
        return self::$_sequencers[$tableName];
    }
    /**
     *
     * Get the next value by the sequencer
     * @version 1.0
     * @since Version 1.0
     * @return integer
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function next()
    {
        $sequenceObj= $this->_sequenceObj;
        if ( ($sequenceObj->seed === -1) ||
             (($this->_sequence + 1) >= Sequencer::MAX_KEYS)) {
            $this->reseed();
        }
        // up the sequence value for the next key
        $this->_sequence++;
        // the next key for this sequencer
        return (($sequenceObj->seed * Sequencer::MAX_KEYS) +$this->_sequence);

    }

    /**
     *
     * regenerate the seed information
     * @version 1.0
     * @since Version 1.0
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    private function reseed()
    {
        $db= $this->_sequenceObj->getDatabase();

        do {
            if (!$this->_sequenceObj->getCurrentSequence()) {
                $this->_sequenceObj->seed=0;
                $this->_sequenceObj->lastupdate= time();
                //if there is no sequence saved, save it
                //if the save is failed, retry
                $insert= $db->insertObject(
                    $this->_sequenceObj->getTableName(),
                    $this->_sequenceObj,
                    $this->_sequenceObj->getPrimaryKey(),
                    $this->_sequenceObj->getColumnSpec()
                );
                if (!$insert) {
                    $this->_sequenceObj->seed=-1;
                }

            } else {
                $this->_sequenceObj->seed++;
                //update here need to modified
                $extraWhere = array(
                        'lastupdate' => $this->_sequenceObj
                                             ->lastupdate
                );

                $this->_sequenceObj->lastupdate= time();
                $update= $db->updateObject(
                    $this->_sequenceObj->getTableName(),
                    $this->_sequenceObj,
                    $this->_sequenceObj->getPrimaryKey(),
                    $this->_sequenceObj->getColumnSpec(),
                    false,
                    $extraWhere
                );
                if (!$update) {
                    $this->_sequenceObj->seed=-1;
                }
            }
        } while ( $this->_sequenceObj->seed === -1);

        $this->_sequence=0;
    }
}

/* ===============================================================
   End of Sequencer.php
   =============================================================== */