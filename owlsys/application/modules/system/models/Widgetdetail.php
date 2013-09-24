<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package system
 * @subpackage models
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class System_Model_Widgetdetail extends Zend_Db_Table_Abstract 
{
  protected $_name = 'widget_detail';
  
  protected $_referenceMap = array (
      'refWidget' => array(
          'columns'			=> array ( 'widget_id' ),
          'refTableClass'	    => 'System_Model_DbTable_Widget',
          'refColumns'		=> array ( 'id' ),
      ),
      'refMenuItem' => array(
          'columns'			=> array ( 'menuitem_id' ),
          'refTableClass'	    => 'menu_Model_DbTable_Item',
          'refColumns'		=> array ( 'id' ),
      )
  );
  
  function __construct() {
    $this->_name = Zend_Registry::get('tablePrefix').$this->_name;
    parent::__construct();
  }
  
  /**
   * Check if a widget is renderable in a menu item
   * @param Zend_Db_Table_Row_Abstract $widget
   * @return boolean
   */
  public function isRenderForAll( $widget )
  {
    $select = $this->select()
      ->where( 'widget_id=?', $widget->id )
      ->where( 'IFNULL(menuitem_id,0)=0' )
      ->limit(1)
    ;
    #echo $select->__toString();
    $rowCount = $this->fetchAll ( $select );
    #echo $rowCount->count();
    if ( $rowCount->count() == 1 ) return true;
    return false;
  }
  
    /**
    * Returns widgets filter by hook and menu item id
    * @param int $itemId
    * @param string $hook
    * @return Zend_Db_Table_Rowset_Abstract|NULL
    */
    public function getWidgetsByHooksAndItemId($itemId, $hooks)
    {
      $rows = array();
      $prefix = Zend_Registry::get('tablePrefix');
      /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
      $cache = Zend_Registry::get('cache');
     
      $cacheId = 'system_getWidgetsByHooksAndItemId_'.$itemId;
      $cacheId = str_replace('-', '_', $cacheId);
       
      if ( $cache->test($cacheId) ) {
        $rows = $cache->load($cacheId);
      } else {
      $select = $this->select();
      $select->setIntegrityCheck(false);
      $select->from( array('wgt' => $prefix.'widget'), array('id AS widget_id', 'position', 'title', 'ordering', 'params', 'resource_id', 'wid', 'showtitle') );
      $select->joinInner( array('wd'=> $this->_name), 'wgt.id = wd.widget_id', null );
      $select->joinInner( array('rs' => $prefix.'acl_resource'), 'rs.id = wgt.resource_id', array('module', 'controller', 'actioncontroller') );
      $select->where( 'IFNULL(wd.menuitem_id,0)=?',$itemId );
      $select->where( 'wgt.position IN(?)', $hooks );
      $select->where('wgt.isPublished=1');
      $select->where( 'wgt.isPublished=1' );
      $select->order( 'wgt.position ASC' );
      $select->order( 'wgt.ordering ASC' );
      $rows = $this->fetchAll($select);
      $cache->save($rows, $cacheId, array('getWidgetsByHooksAndItemId'));
    }
    return $rows;
  }
  
  /**
  * Get details by widget
  * @param Zend_Db_Table_Row_Abstract $widget
  */
  public function getByWidget($widget)
  {
    $select = $this->select()
      ->from( $this->_name, 'IFNULL(menuitem_id,0) AS menuitem_id' )
      ->where('widget_id=?', $widget->id, Zend_Db::INT_TYPE)
    ;
//     Zend_Debug::dump($select->__toString());die();
    return $this->fetchAll($select);
  }   

  public function deleteByWidget($widget)
  {
    $where = $this->getAdapter()->quoteInto('widget_id = ?', $widget->id);
    $this->delete($where);
  }
  
}