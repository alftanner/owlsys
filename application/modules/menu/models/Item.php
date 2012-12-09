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
class Menu_Model_Item extends Zend_Db_Table_Abstract
{

    /**
     * 
     * @var string
     */
	protected $_name = 'menu_item';
	/**
	 * 
	 * @var array
	 */
	protected $_dependentTables = array( 'menu_Model_Item', 'System_Model_Widgetdetail' );
	/**
	 * 
	 * @var array
	 */
	protected $_referenceMap = array(
		'Menu' => array(
			'columns' 			=> array('menu_id'),
			'refTableClass' 	=> 'Menu_Model_Menu',
			'refColumns'		=> array('id'),
			'onDelete'			=> self::RESTRICT,
			'onUpdate'			=> self::RESTRICT
		),
		'MenuParent' => array(
			'columns' 			=> array('parent_id'),
			'refTableClass' 	=> 'Menu_Model_Item',
			'refColumns'		=> array('id'),
			'onDelete'			=> self::RESTRICT,
			'onUpdate'			=> self::RESTRICT
		),
		'Resource' => array(
			'columns' 			=> array('resource_id'),
			'refTableClass' 	=> 'Acl_Model_Resource',
			'refColumns'		=> array('id'),
			'onDelete'			=> self::CASCADE,
			'onUpdate'			=> self::RESTRICT
		),
	);
	
	/**
	 * renames the table by adding the prefix defined in the global configuration parameters
	 */
	function __construct() {
		$this->_name = Zend_Registry::get('tablePrefix').$this->_name;
		parent::__construct();
	}

	/**
	 * Returns a recordset filter by menu order by parent_id and ordering
	 * @param Zend_Db_Table_Row_Abstract $menu
	 * @return Zend_Paginator_Adapter_DbTableSelect
	 */
	public function getPaginatorAdapterListByMenu( Zend_Db_Table_Row_Abstract $menu )
	{
		$select = $this->select()
			->setIntegrityCheck(false)
			->from( array('it'=> $this->_name), array('id', 'ordering', 'icon', 'wtype', 'title', 'id_alias', 'published', 'description') ) # item
			->joinLeft( array('itp' => Zend_Registry::get('tablePrefix').'menu_item'), 'it.parent_id = itp.id', 'title AS titleParent' ) # item parent
			->joinInner( array('rs' => Zend_Registry::get('tablePrefix').'acl_resource'), 'rs.id = it.resource_id', array('module', 'controller', 'actioncontroller') ) # resource
			->where("it.menu_id = ?", $menu->id, Zend_Db::INT_TYPE)
			->order('it.parent_id')
			->order('it.ordering ASC')
		;
		#echo $select->__toString();
		#$items = $this->fetchAll($select);
		#if ( $items->count() > 0 )
		#	return $items;
		#else return null;
		return new Zend_Paginator_Adapter_DbTableSelect($select);
	}
	
	/**
	 * Returns a recordset filter by menu order by ordering
	 * @param Zend_Db_Table_Row_Abstract $menu
	 * @return Zend_Db_Table_Rowset_Abstract|NULL
	 */
	function getListItemsByMenu( Zend_Db_Table_Row_Abstract $menu)
	{
		$select = $this->select()
			->from( array('it' => $this->_name), array('id', 'title') )
			->where("it.menu_id = ?", $menu->id, Zend_Db::INT_TYPE)
			->order('it.ordering');
		$items = $this->fetchAll($select);
		if ( $items->count() > 0 )
			return $items;
		else return null;
	}

	/**
	 * Sets the position to record and save 
	 * @param Zend_Db_Table_Row_Abstract $menuItem
	 * @return Ambigous <mixed, multitype:>
	 */
	function save( Zend_Db_Table_Row_Abstract $menuItem )
	{
		$menuItem->ordering = $this->_getLastPosition($menuItem)+1;
		return $menuItem->save();
	}
	
	/**
	 * returns the last position of a contact list according to the category they belong in ascending order
	 * @param Zend_Db_Table_Row_Abstract $menuItem
	 * @return int
	 */
	private function _getLastPosition( Zend_Db_Table_Row_Abstract $menuItem )
	{
		#$mdlMenu = new menu_Model_Menu();
		#$menu = $mdlMenu->find( $menuItem->menu_id )->current();
		if ( $menuItem->parent_id > 0 ) 
		{
			$select = $this->select()
				->where( 'menu_id = ?', $menuItem->menu_id, Zend_Db::INT_TYPE )
				->where( 'parent_id = ?', $menuItem->parent_id, Zend_Db::INT_TYPE )
				->order( 'ordering DESC' );
		} else {
			$select = $this->select()
				->where( 'menu_id = ?', $menuItem->menu_id, Zend_Db::INT_TYPE )
				->order( 'ordering DESC' );
		}
		$row = $this->fetchRow( $select );
		if ( $row )
			return $row->ordering;
		return 0;
	}
	
	/**
	 * Moves the record position one above
	 * @param Zend_Db_Table_Row_Abstract $menuItem
	 * @return boolean|Ambigous <mixed, multitype:>
	 */
	function moveUp( Zend_Db_Table_Row_Abstract $menuItem )
	{
		$ordering = $menuItem->ordering;
		if ( $ordering < 1 ) return false;
		else 
		{
			$select = $this->select()
				->order('ordering DESC')
				->where("ordering < ?", $ordering, Zend_Db::INT_TYPE)
				->where("menu_id = ?", $menuItem->menu_id, Zend_Db::INT_TYPE);
			$previousItem = $this->fetchRow($select);
			if ( $previousItem )
			{
				$previousPosition = $previousItem->ordering;
				$previousItem->ordering = $ordering;
				$previousItem->save();
				$menuItem->ordering = $previousPosition;
				return $menuItem->save();
			}
		}
	}
	
	/**
	 * Moves the record position one down
	 * @param Zend_Db_Table_Row_Abstract $menuItem
	 * @return boolean|Ambigous <mixed, multitype:>
	 */
	function moveDown( Zend_Db_Table_Row_Abstract $menuItem )
	{
		$ordering = $menuItem->ordering;
		if ( $ordering == $this->_getLastPosition($menuItem) ) return false;
		else 
		{
			$select = $this->select()
				->order('ordering ASC')
				->where("ordering > ?", $ordering, Zend_Db::INT_TYPE)
				->where("menu_id = ?", $menuItem->menu_id, Zend_Db::INT_TYPE);
			$nextItem = $this->fetchRow($select);
			if ( $nextItem )
			{
				$nextPosition = $nextItem->ordering;
				$nextItem->ordering = $ordering;
				$nextItem->save();
				$menuItem->ordering = $nextPosition;
				return $menuItem->save();
			}
		}
	}
	
	/**
	 * @param int $menuId
	 * @param int $parent
	 * @return Zend_Db_Table_Rowset_Abstract|multitype:
	 * @deprecated 
	 */
	public function getNavItemsByMenu( $menuId, $parent = 0 )
	{
		$select = $this->select()
			->setIntegrityCheck(false)
			->from( array('it'=> $this->_name), array('id', 'ordering', 'icon', 'wtype', 'title', 'id_alias', 'published', 'description', 'menu_id') ) # item
			->joinInner( array('rs' => Zend_Registry::get('tablePrefix').'acl_resource'), 'rs.id = it.resource_id', array('module', 'controller', 'actioncontroller') ) # resource
			->where("it.menu_id = ?", $menuId, Zend_Db::INT_TYPE)
			->where("it.published = ?",1, Zend_Db::INT_TYPE)
			->where("it.parent_id = ?", $parent, Zend_Db::INT_TYPE)
			->order('it.ordering');
		#echo ($menuId == 8) ? $select->__toString() : '';
		$items = $this->fetchAll($select);
		if ( $items->count() > 0 )
			return $items;
		else return array();
	}
	
	/**
	 * Return items filter by widget
	 * @param int $level
	 * @param Zend_Db_Table_Row_Abstract $menu
	 * @param Zend_Db_Table_Row_Abstract $menuItem
	 * @param array $arrData
	 */
	public function getMenuItemsForWidget( $level = 0, Zend_Db_Table_Row_Abstract $menu, Zend_Db_Table_Row_Abstract $menuItem = null, &$arrData)
	{
	    if ( $level == 0 ) {
	        $mdlMenu = new menu_Model_Menu();
	        $selectMenu = $mdlMenu->select()->where('parent_id=?', 0, Zend_Db::INT_TYPE);
	        $items = $menu->findDependentRowset('menu_Model_Item', 'Menu', $selectMenu);
	        if ( $items->count() > 0 ) 
	        {
	            $level++;
	            foreach ( $items as $item )
	            {
	                $arrData[ $item->id ] = $item->title;
	                $selectItem = $this->select()->where('parent_id=?', $item->id, Zend_Db::INT_TYPE);
	                $subItems = $item->findDependentRowset('menu_Model_Item', 'MenuParent');
	                if ( $subItems->count() > 0 )
	                {
	                    $this->getMenuItemsForWidget($level, $menu, $item, $arrData);
	                }
	            }
	        }
	    }
	    else {
	        $selectItem = $this->select()->where('parent_id=?', $menuItem->id, Zend_Db::INT_TYPE);
	        $subItems = $menuItem->findDependentRowset('menu_Model_Item', 'MenuParent');
	        if ( $subItems->count() > 0 )
	        {
	            $level++;
	            foreach ( $subItems as $smi ) 
	            {
	                $prefix = str_pad("", $level-1, "-");
	                $arrData[ $smi->id ] = $prefix.' '.$smi->title;
	        		$this->getMenuItemsForWidget($level, $menu, $smi, $arrData);
	            }
	        }
	    }
	}

	/**
	 * Returns a simple list items used for routing support
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getListForRouting()
	{
		$select = $this->select()
			->setIntegrityCheck(false)
			->from( array('mit'=> $this->_name), array('id', 'id_alias', 'params') ) # item
			->joinInner( array('rs' => Zend_Registry::get('tablePrefix').'acl_resource'), 'rs.id = mit.resource_id', array('module', 'controller', 'actioncontroller') ) # resource
			->where("mit.published=?", 1)
			#->where("mit.external=?", 1)
			#->where("LENGTH(mit.id_alias)>?", 0)
			->order('mit.ordering');
		#echo $select->__toString();
		$items = $this->fetchAll($select);
		return $items;
	}

	/**
	 * Return a recorset of items used for navigation
	 */
	function getItemsForNavigationByMenu( Zend_Db_Table_Row_Abstract $menu )
	{
		$select = $this->select()
			->setIntegrityCheck(false)
			->from( 
				array('mit'=> $this->_name), 
				array('*') 
			) # item
			->joinInner( 
				array('rs' => Zend_Registry::get('tablePrefix').'acl_resource'), 'rs.id = mit.resource_id', 
				array('module', 'controller', 'actioncontroller') 
			) # resource
			->where('mit.menu_id=?', $menu->id, Zend_Db::INT_TYPE)
			->where('mit.published=?',1)
			->order('mit.depth ASC')
			->order('mit.parent_id ASC')
			->order('mit.id ASC')
		;
		#echo $select->__toString();
		return $this->fetchAll( $select );
	}
}

