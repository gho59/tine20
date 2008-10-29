<?php
/**
 * class to hold snom phone line data
 * 
 * @package     Voipmanager Management
 * @license     http://www.gnu.org/licenses/agpl.html AGPL Version 3
 * @author      Lars Kneschke <l.kneschke@metaways.de>
 * @copyright   Copyright (c) 2008 Metaways Infosystems GmbH (http://www.metaways.de)
 * @version     $Id$
 *
 */

/**
 * class to hold snom phone line data
 * 
 * @package     Voipmanager Management
 */
class Voipmanager_Model_SnomLine extends Tinebase_Record_Abstract
{
    /**
     * key in $_validators/$_properties array for the filed which 
     * represents the identifier
     * 
     * @var string
     */    
    protected $_identifier = 'id';
    
    /**
     * application the record belongs to
     *
     * @var string
     */
    protected $_application = 'Voipmanager';
    
    /**
     * list of zend inputfilter
     * 
     * this filter get used when validating user generated content with Zend_Input_Filter
     *
     * @var array
     */
    protected $_filters = array(
        '*'                     => 'StringTrim'
    );
    
    /**
     * list of zend validator
     * 
     * this validators get used when validating user generated content with Zend_Input_Filter
     *
     * @var array
     */
    protected $_validators = array(
        'id' 			    => array(Zend_Filter_Input::ALLOW_EMPTY => true, Zend_Filter_Input::DEFAULT_VALUE => NULL),
        'snomphone_id'      => array(Zend_Filter_Input::ALLOW_EMPTY => false, 'presence' => 'required'),
        'asteriskline_id'   => array(Zend_Filter_Input::ALLOW_EMPTY => false, 'presence' => 'required'),
        'linenumber'        => array(Zend_Filter_Input::ALLOW_EMPTY => false, 'presence' => 'required'),
        'lineactive'        => array(Zend_Filter_Input::ALLOW_EMPTY => true, 'presence' => 'required'),
        'idletext'          => array(Zend_Filter_Input::ALLOW_EMPTY => true)
    );
    
    /**
     * converts a int, string or Voipmanager_Model_SnomLine to an phone id
     *
     * @param int|string|Voipmanager_Model_SnomLine $_snomLineId the snomline id to convert
     * @return int
     * @throws  Voipmanager_Exception_InvalidArgument
     */
    static public function convertSnomLineIdToInt($_snomLineId)
    {
        if ($_snomLineId instanceof Voipmanager_Model_SnomLine) {
            if (empty($_snomLineId->id)) {
                throw new Voipmanager_Exception_InvalidArgument('no line id set');
            }
            $id = (string) $_snomLineId->id;
        } else {
            $id = (string) $_snomLineId;
        }
        
        if (empty($id)) {
            throw new Voipmanager_Exception_InvalidArgument('lineid id can not be \'\'');
        }
        
        return $id;
    }

}