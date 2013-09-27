<?php

class System_Model_Skin extends Zend_Db_Table_Abstract
{
  protected $_name = 'skin';
  
  function __construct() {
    $this->_name = Zend_Registry::get('tablePrefix').$this->_name;
    parent::__construct();
  }
  
  public function getSkinSelected()
  {
    $row = null;
    /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
    $cache = Zend_Registry::get('cache');
    $cacheId = 'system_getSkinSelected';
    if ( $cache->test($cacheId) ) {
      $row = $cache->load($cacheId);
    } else {
      $select = $this->select()->where('isselected=1')->limit(1);
      $row = $this->fetchRow($select);
      $cache->save($row, $cacheId, array('skin'));
    }
    return $row;
  }
  
  public function getList()
  {
    $rows = array();
    /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
    $cache = Zend_Registry::get('cache');
    $cacheId = 'system_getList';
    if ( $cache->test($cacheId) ) {
      $rows = $cache->load($cacheId);
    } else {
      $select = $this->select();
      $rows = $this->fetchAll($select);
      $cache->save($rows, $cacheId, array('skin'));
    }
    return $rows;
  }	
}

