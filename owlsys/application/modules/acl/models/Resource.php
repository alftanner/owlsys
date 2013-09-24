<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package acl
 * @subpackage models
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Acl_Model_Resource extends Zend_Db_Table_Abstract
{
  protected $_name = 'acl_resource';
  
  protected $_dependentTables = array ( 'Acl_Model_DbTable_Permission', 'menu_Model_DbTable_Item', 'System_Model_DbTable_Widget' );
  
  function __construct() {
    $this->_name = Zend_Registry::get('tablePrefix').$this->_name;
    parent::__construct();
  }
  
  /**
   * Returns all resources registered order by module / controller asc
   * @return Zend_Db_Table_Rowset_Abstract instance of | array
   */
  public function getAll( )
  {
    $select = $this->select();
    $rows = $this->fetchAll( $select, array('module'=>'asc', 'controller'=>'asc') );
    return $rows;
  }
  
  /**
   * @param Acl_Model_Resource $resource
   * @return Zend_Db_Table_Rowset_Abstract
   */
  public function getByModule( $resource )
  {
    /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
    $cache = Zend_Registry::get('cache');
    $cacheId = 'acl_getByModule_'.$resource->module;
    $rows = array();
    if ( $cache->test($cacheId) ) {
      $rows = $cache->load($cacheId);
    } else {
      $select = $this->select()->where('module=?', $resource->module);
      $rows = $this->fetchAll( $select );
      $cache->save($rows, $cacheId);
    }
    return $rows;
  }
  
  /**
   * Return all registered module
   * @return Zend_Db_Table_Rowset_Abstract
   */
  public function getModules()
  {
    $rows = array();
    /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
    $cache = Zend_Registry::get('cache');
    $cacheId = 'acl_getModules';
    if ( $cache->test($cacheId) ) {
      $rows = $cache->load($cacheId);
    } else {
      $select = $this->select( )
        ->distinct()
        ->from( array('sr'=> $this->_name), 'module' );
      $rows = $this->fetchAll($select);
      $cache->save($rows, $cacheId);
    }
    return $rows;
  }
  
  /**
   * Return a resource by module, controller and action
   * @param Acl_Model_Resource $resource
   * @return Zend_Db_Table_Row_Abstract|null The row results per the Zend_Db_Adapter fetch mode, or null if no row found.
   */
  public function getIdByDetail( $resource )
  {
    $select = $this->select()
      ->where( 'module=?', $resource->module)
      ->where( 'controller=?', $resource->controller)
      ->where( 'actioncontroller=?', $resource->actioncontroller)
      ->limit(1);
    $row = $this->fetchRow($select);
    return $row;
  }
  
  public function remove( $resource )
  {
    $select = $this->select()
      ->where('id=?',$resource->id, Zend_Db::INT_TYPE)
      ->limit(1)
    ;
    $row = $this->fetchRow($select);
    $row->delete();
  }
}