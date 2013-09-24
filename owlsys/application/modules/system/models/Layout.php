<?php

class System_Model_Layout extends Zend_Db_Table_Abstract
{    
  protected $_name = 'layout';
  
  protected $_dependentTables = array ( 'Acl_Model_Role' );
  
  function __construct() {
    $this->_name = Zend_Registry::get('tablePrefix').$this->_name;
    parent::__construct();
  }
  
  public function getAll()
  {
    $select = $this->select();
    return $this->fetchAll();
  }

  public function find($id)
  {
    /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
    $cache = Zend_Registry::get('cache');
    $cacheId = 'layout_find_'.$id;
    $row = null;
    if ( $cache->test($cacheId) ) {
      $row = $cache->load($cacheId);
    } else {
      $row = parent::find($id);
      $cache->save($row, $cacheId);
    }
    return $row;
  }
}


