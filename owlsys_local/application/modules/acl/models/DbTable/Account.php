<?php

class Acl_Model_DbTable_Account extends Zend_Db_Table_Abstract
{

    protected $_name = 'account';

    protected $_dependentTables = array(
            'Contact_Model_Contact',
            'Acl_Model_Accountdetail',
            'Ficha_Model_Ficha'
    );

    protected $_referenceMap = array(
            'Role' => array(
                    'columns' => array(
                            'role_id'
                    ),
                    'refTableClass' => 'Acl_Model_Role',
                    'refColumns' => array(
                            'id'
                    ),
                    'onDelete' => self::RESTRICT,
                    'onUpdate' => self::RESTRICT
            )
    );
    
    function __construct() {
        $this->_name = Zend_Registry::get('tablePrefix').$this->_name;
        parent::__construct();
    }
    
    
    
}

