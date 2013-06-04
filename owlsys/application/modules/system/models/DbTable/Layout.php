<?php

class System_Model_DbTable_Layout extends Zend_Db_Table_Abstract
{

    protected $_name = 'layout';
    
    protected $_dependentTables = array ( 'Acl_Model_DbTable_Role' );
    
    function __construct() {
        $this->_name = Zend_Registry::get('tablePrefix').$this->_name;
        parent::__construct();
    }

    public function getAll()
    {
        $select = $this->select();
        return $this->fetchAll();
    }
}

