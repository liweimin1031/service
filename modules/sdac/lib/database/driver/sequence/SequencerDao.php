<?php
/* --------------------------------------------------------------- */
/**
 * FILE NAME   : SequencerDao.php
 * AUTHOR      : Michelle Hong
 * SYNOPSIS    :
 * DESCRIPTION : Default Description
 * SEE ALSO    :
 * VERSION     : 1.0 ($Revision: 83 $)
 * CREATED     : Jan 14, 2013
 * LASTUPDATES : $Author: michellehong $ on $Date: 2015-01-13 16:30:26 +0800 (週二, 13 一月 2015) $
 * UPDATES     :
 * NOTES       :
 */
/* ---------------------------------------------------------------
   @(#) SequencerDao.php              1.0 Jan 14, 2013
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
   Begin of SequencerDao.php
   =============================================================== */

namespace Clms\Tools\PhpDao\Driver\Sequence;

use Clms\Tools\PhpDao\Column\Column;

use Clms\Tools\PhpDao\Column\ColumnFactory;

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
 * Lms Database sequencer database access object
 *
 * Used internally by Sequencer during reseed process. The table name is set
 * <b>DatabaseSequencer</b>.
 * @package Php-Dao
 * @subpackage driver
 * @since  Version 1.0
 * @see
 * @author      Michelle Hong
 * @testing
 * @warnings
 * @updates
 */
class SequencerDao extends \Clms\Tools\PhpDao\Dao
{
    /**
     *
     * @var string The name of this sequencer. The value should match the table
     *             name of the auto-incremental column owner.
     * @version 1.0
     * @since Version 1.0
     */
    public $name = null;

    /**
     *
     * @var int The seed this sequencer, used for generating its ID's
     * @version 1.0
     * @since Version 1.0
     */
    public $seed  = -1;

    /**
     * @var int The last updated time of the seed
     *
     * @version 1.0
     * @since Version 1.0
     */
    public $lastupdate = 0;

    /**
     *
     * A database sequence constructor
     * @version 1.0
     * @since Version 1.0
     * @param Driver $db database instance
     * @param string $name sequence name. If null is provided, use 'stdClass',
     *                     and this is only used for create sequencer table only
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function __construct($db, $name= 'stdClass')
    {
        $this->_tblPK ='name';
        parent::__construct('DatabaseSequencer', $db);

        $this->name= $name;
    }

    /**
     *
     * Set Lms Database sequencer column specification
     *
     * (non-PHPdoc)
     * @version 1.0
     * @since Version 1.0
     * @see Dao::setColumnSpec()
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    protected function setColumnSpec()
    {
        $this->_columnSpec['name']= ColumnFactory::createColumn(Column::TYPE_CHAR);
        $this->_columnSpec['seed']= ColumnFactory::createColumn(Column::TYPE_INT);
        $this->_columnSpec['lastupdate']= ColumnFactory::createColumn(Column::TYPE_INT);
    }


    /**
     *
     * Get the database
     * @version 1.0
     * @since Version 1.0
     * @return Driver
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getDatabase()
    {
        return $this->_db;
    }

    /**
     *
     * Load data from sequencer table
     *
     * @version 1.0
     * @since Version 1.0
     * @see \Clms\Tools\PhpDao\Dao::load()
     * @param array $keys searched keys
     * @see
     * @author      Michelle Hong
     * @testing
     * @warnings
     * @updates
     */
    public function getCurrentSequence($keys= null)
    {
        return parent::load($keys, true);
    }

}

/* ===============================================================
   End of SequencerDao.php
   =============================================================== */