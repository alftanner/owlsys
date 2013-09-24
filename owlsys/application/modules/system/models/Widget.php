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
class System_Model_Widget extends Zend_Db_Table_Abstract
{

  protected $_name = 'widget';
  protected $_dependentTables = array ( 'System_Model_Widgetdetail' );
  
  protected $_referenceMap = array (
      'refWidgetResource' => array(
          'columns'			=> array ( 'resource_id' ),
          'refTableClass'	=> 'Acl_Model_Resource',
          'refColumns'		=> array ( 'id' ),
      )
  );
  
  function __construct() {
    $this->_name = Zend_Registry::get('tablePrefix').$this->_name;
    parent::__construct();
  }
  
  /**
   * returns the last position of a contact list according to the category they belong in ascending order
   * @param Zend_Db_Table_Row_Abstractt $widget
   * @return number
   */
  public function getLastPosition( $widget )
  {
    $select = $this->select()
      ->where('position=?', $widget->position)
      ->order("ordering DESC")
      ->limit(1);
    $row = $this->fetchRow($select);
    if ( !$row ) return 0;
    return $row->ordering;
  }
  
  /**
   * Returns a recordseet of widgets
   * @return unknown
   */
  public function getList()
  {
    $rows = array();
    /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
    $cache = Zend_Registry::get('cache');
    $cacheId = 'system_widget_getList';
    if ( $cache->test($cacheId) ) {
      $rows = $cache->load($cacheId);
    } else {
      $select = $this->select()
        ->setIntegrityCheck(false)
        ->from( array('wgt' => $this->_name), array('id', 'position', 'title', 'isPublished', 'ordering') )
        ->joinInner( array('rs' => Zend_Registry::get('tablePrefix').'acl_resource'),
          'rs.id = wgt.resource_id',
          array('module', 'controller', 'actioncontroller', 'id AS resource_id') )
        ->order('wgt.position')
        ->order('wgt.ordering')
      ;
      $rows = $this->fetchAll($select);
      $cache->save($rows, $cacheId, array('widgets'));
    }
    return $rows;
  }
  
  /**
   * Moves the record position one above
   * @param Zend_Db_Table_Row_Abstract $widget
   */
  public function moveUp( $widget )
  {
    $select = $this->select()
      ->order('ordering DESC')
      ->where("ordering < ?", $widget->ordering, Zend_Db::INT_TYPE)
      ->where("position = ?", $widget->position);
    $previousItem = $this->fetchRow($select);
    if ( $previousItem )
    {
      $previousPosition = $previousItem->ordering;
      $previousItem->ordering = $widget->ordering;
      $previousItem->save();
      $widget->ordering = $previousPosition;
    }
  }
  
  /**
   * Moves the record position one down
   * @param Zend_Db_Table_Row_Abstract $widget
   */
  public function moveDown($widget )
  {
    $select = $this->select()
      ->order('ordering ASC')
      ->where("ordering > ?", $widget->ordering, Zend_Db::INT_TYPE)
      ->where("position = ?", $widget->position);
    $nextItem = $this->fetchRow($select);
    if ( $nextItem )
    {
      $nextPosition = $nextItem->ordering;
      $nextItem->ordering = $widget->ordering;
      $nextItem->save();
      $widget->ordering = $nextPosition;
    }
  }
  
  /**
   * 
   * @param Zend_Db_Table_Row_Abstract $widget
   */
  public function remove( $widget )
  {
    $row = $this->find($widget->id)->current();
    $menuItemsWidget = $row->findDependentRowset('System_Model_DbTable_Widgetdetail', 'refWidget');
    foreach ( $menuItemsWidget as $miw ) $miw->delete();
    $row->delete();
  }

  /**
   * 
   * @param Zend_Db_Table_Row_Abstract $widget
   */
  public function save($widget)
  {
    if ( $widget->id < 1 ) {
      $widget->ordering = $this->getLastPosition($widget)+1;
    }
    $widget->save();
  }
  
}

