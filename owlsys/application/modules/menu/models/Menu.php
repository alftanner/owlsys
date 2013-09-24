<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package menu
 * @subpackage models
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class menu_Model_Menu extends Zend_Db_Table_Abstract
{
  protected $_name = 'menu';
  
  protected $_dependentTables = array ( 'menu_Model_DbTable_Item' );
  
  function __construct() {
    $this->_name = Zend_Registry::get('tablePrefix').$this->_name;
    parent::__construct();
  }
  
  public function getMenus()
  {
    $select = $this->select();
    $select->order('id');
    return $this->fetchAll($select);
  }
  
  /**
   * Return a list of menus filtered by status
   * @param int $status
   * @return Ambigous <multitype:, Zend_Db_Table_Rowset_Abstract, mixed, false, boolean, string>
   */
  public function getByStatus( $status )
  {
    $rows = array();
    /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
    $cache = Zend_Registry::get('cache');
    $cacheId = 'menu_getByStatus_'.$status;
    if ( $cache->test($cacheId) ) {
      $rows = $cache->load($cacheId);
    } else {
      $select = $this->select();
      $select->order('id');
      $select->where('isPublished=?', $status, Zend_Db::INT_TYPE);
      $rows = $this->fetchAll($select);
      $cache->save($rows, $cacheId);
    }
    return $rows;
  }
  
  /**
   *
   * @param menu_Model_Menu $menu
   */
  public function remove($menu)
  {
    $select = $this->select()
     ->where('id=?',$menu->id, Zend_Db::INT_TYPE)
    ;
    $row = $this->fetchRow($select);
    $row->delete();
  }


}