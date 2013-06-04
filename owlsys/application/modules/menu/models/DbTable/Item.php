<?php

class menu_Model_DbTable_Item extends Zend_Db_Table_Abstract
{

    protected $_name = 'menu_item';
    
    protected $_dependentTables = array( 'menu_Model_DbTable_Menu', 'System_Model_DbTable_Widgetdetail' );
    
    protected $_referenceMap = array(
            'refMenu' => array(
                    'columns' 			=> array('menu_id'),
                    'refTableClass' 	=> 'menu_Model_DbTable_Menu',
                    'refColumns'		=> array('id'),
            ),
            'refParent' => array(
                    'columns' 			=> array('parent_id'),
                    'refTableClass' 	=> 'menu_Model_DbTable_Item',
                    'refColumns'		=> array('id'),
            ),
            'refResource' => array(
                    'columns' 			=> array('resource_id'),
                    'refTableClass' 	=> 'Acl_Model_DbTable_Resource',
                    'refColumns'		=> array('id'),
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
     * @param menu_Model_Menu $menu
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getListByMenu( menu_Model_Menu $menu )
    {
        $prefix = Zend_Registry::get('tablePrefix');
        $items = array();
        /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
//         $cache = Zend_Registry::get('cache');
//         $cacheId = 'menuItem_getListBymenu_'.$menu->getId();
//         if ( $cache->test($cacheId) ) {
//             $items = $cache->load($cacheId);
//         } else {
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from( array('it'=> $this->_name), array('id', 'ordering', 'icon', 'wtype', 'title', 'route', 'isPublished', 'description') ) # item
                ->joinLeft( array('itp' => $prefix.'menu_item'), 'it.parent_id = itp.id', array('title AS parent_title', 'id AS parent_id') ) # item parent
                ->joinInner( array('rs' => $prefix.'acl_resource'), 'rs.id = it.resource_id', array('module', 'controller', 'actioncontroller') ) # resource
                ->where("it.menu_id = ?", $menu->getId(), Zend_Db::INT_TYPE)
                ->order('it.parent_id')
                ->order('it.ordering ASC')
            ;
            $items = $this->fetchAll($select);
//             $cache->save($items, $cacheId);
//         }
        return $items;
    }
    
    /**
     * returns the last position of a contact list according to the category they belong in ascending order
     * @param menu_Model_Item $menuItem
     * @return number
     */
    public function getLastPosition( menu_Model_Item $menuItem )
    {
        $select = $select = $this->select()
            ->from($this->_name, 'ordering');
        if ( $menuItem->getParent()->getId() > 0 ) {
            $select
                ->where( 'menu_id = ?', $menuItem->getMenu()->getId(), Zend_Db::INT_TYPE )
                ->where( 'parent_id = ?', $menuItem->getParent()->getId(), Zend_Db::INT_TYPE )
                ->order( 'ordering DESC' );
        } else {
    		$select
        		->where( 'menu_id = ?', $menuItem->getMenu()->getId(), Zend_Db::INT_TYPE )
    			->order( 'ordering DESC' );
        }
        $row = $this->fetchRow( $select );
        if ( $row )
            return $row->ordering;
        return 0;
    }
    
    /**
     * Moves the record position one above
     * @param menu_Model_Item $menuItem
     * @return boolean
     */
    function moveUp( menu_Model_Item $menuItem )
    {
        $select = $this->select()
            ->order('ordering DESC')
            ->where("ordering < ?", $menuItem->getOrdering(), Zend_Db::INT_TYPE)
            ->where("menu_id = ?", $menuItem->getMenu()->getId(), Zend_Db::INT_TYPE);
		$previousItem = $this->fetchRow($select);
		if ( $previousItem ) {
			$previousPosition = $previousItem->ordering;
			$previousItem->ordering = $menuItem->getOrdering();
			$previousItem->save();
			$menuItem->setOrdering($previousPosition);
        }
    }
    
    /**
     * Moves the record position one down
     * @param menu_Model_Item $menuItem
     * @return boolean
     */
    function moveDown( menu_Model_Item $menuItem )
    {
        $select = $this->select()
            ->order('ordering ASC')
			->where("ordering > ?", $menuItem->getOrdering(), Zend_Db::INT_TYPE)
            ->where("menu_id = ?", $menuItem->getMenu()->getId(), Zend_Db::INT_TYPE);
        $nextItem = $this->fetchRow($select);
        if ( $nextItem ) {
            $nextPosition = $nextItem->ordering;
            $nextItem->ordering = $menuItem->getOrdering();
            $nextItem->save();
            $menuItem->setOrdering($nextPosition);
        }
    }
    
    /**
     * @param menu_Model_Item $menuItem
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getChildren(menu_Model_Item $menuItem) 
    {
        /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
//         $cache = Zend_Registry::get('cache');
//         $cacheId = 'menu_getChildren_'.$menuItem->getId();
//         if ( $cache->test($cacheId) ) {
//             $rows = $cache->load($cacheId);
//         } else {
            $prefix = Zend_Registry::get('tablePrefix');
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from( array('it'=>$this->_name) )
                ->joinInner( array('rs' => $prefix.'acl_resource'), 'rs.id = it.resource_id', array('module', 'controller', 'actioncontroller') ) # resource
                ->where('it.parent_id=?', $menuItem->getId())
                ->where('it.isVisible=1')
            ;
            $rows = $this->fetchAll($select);
//             $cache->save($rows, $cacheId);
//         }
        return $rows;
    }
    
    /**
     * Retorna los menu items de un menu especifico. (el parent_id=0 limita a que se busque en menuitems que no son hijos)
     * @param menu_Model_Menu $menu
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getAllByMenu(menu_Model_Menu $menu)
    {
        /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
        $cache = Zend_Registry::get('cache');
        $cacheId = 'menu_getAllByMenu_'.$menu->getId();
        if ( $cache->test($cacheId) ) {
            $rows = $cache->load($cacheId);
        } else {
            $prefix = Zend_Registry::get('tablePrefix');
            $select = $this->select()
                ->setIntegrityCheck(false)
                ->from( array('it'=>$this->_name) )
                ->joinInner( array('rs' => $prefix.'acl_resource'), 'rs.id = it.resource_id', array('module', 'controller', 'actioncontroller') ) # resource
                ->where('IFNULL(it.parent_id,0)=0')
                ->where('it.menu_id=?', $menu->getId(), Zend_Db::INT_TYPE)
            ;
            //Zend_Debug::dump($select->__toString());
            $rows = $this->fetchAll($select);
            $cache->save($rows, $cacheId);
        }
        return $rows;
    }

    /**
     * 
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getRegisteredRoutes()
    {
        $prefix = Zend_Registry::get('tablePrefix');
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from( array('it'=>$this->_name), array('route') )
            ->joinInner( array('rs' => $prefix.'acl_resource'), 'rs.id = it.resource_id', array('module', 'controller', 'actioncontroller') ) # resource
            ->where('it.isPublished=1')
        ;
        //Zend_Debug::dump($select->__toString());
        $rows = $this->fetchAll($select);
        return $rows;
    }

    /**
     * 
     * @param menu_Model_Item $menuItem
     */
    public function remove(menu_Model_Item $menuItem)
    {
        $row = $this->find($menuItem->getId())->current();
        $row->delete();
    }
}

