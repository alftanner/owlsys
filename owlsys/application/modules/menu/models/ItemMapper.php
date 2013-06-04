<?php

class menu_Model_ItemMapper extends OS_Mapper
{

    /**
     * @var menu_Model_ItemMapper
     */
    protected static $_instance = null;
    
    /**
     * @return menu_Model_ItemMapper
     */
    public static function getInstance()
    {
        if ( is_null(self::$_instance) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * @return menu_Model_DbTable_Item
     */
    public function getDbTable ()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('menu_Model_DbTable_Item');
        }
        return $this->_dbTable;
    }
    
    /**
     * 
     * @param number $id
     * @param menu_Model_Item $menuItem
     */
    public function find($id, menu_Model_Item $menuItem)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $menuItem->setId($row->id)
            ->setCssClass($row->css_class)
            ->setDepth($row->depth)
            ->setDescription($row->description)
            ->setExternal($row->external)
            ->setIcon($row->icon)
            ->setIsPublished($row->isPublished)
            ->setIsVisible($row->isVisible)
            ->setMid($row->mid)
            ->setOrdering($row->ordering)
            ->setParams($row->params)
            ->setRoute($row->route)
            ->setTitle($row->title)
            ->setWtype($row->wtype)
        ;
        $parent = new menu_Model_Item();
        $parent->setId($row->parent_id);
        $menuItem->setParent($parent);
        $resource = new Acl_Model_Resource();
        $resource->setId($row->resource_id);
        $menuItem->setResource($resource);
        $menu = new menu_Model_Menu();
        $menu->setId($row->menu_id);
        $menuItem->setMenu($menu);
    }

    /**
     * 
     * @param menu_Model_Menu $menu
     * @return multitype:menu_Model_Item
     */
    public function getListByMenu( menu_Model_Menu $menu )
    {
        $items = array();
        $resultSet = $this->getDbTable()->getListByMenu($menu);
        foreach ($resultSet as $result) {
            $item = new menu_Model_Item();
            $item->setId($result->id)
                ->setOrdering($result->ordering)
                ->setIcon($result->icon)
                ->setWtype($result->wtype)
                ->setTitle($result->title)
                ->setRoute($result->route)
                ->setIsPublished($result->isPublished)
                ->setDescription($result->description)
            ;
            $itemParent = new menu_Model_Item();
            $itemParent->setId($result->parent_id)
                ->setTitle($result->parent_title)
            ;
            $item->setParent($itemParent);
            $resource = new Acl_Model_Resource();
            $resource->setModule($result->module)
                ->setController($result->controller)
                ->setActioncontroller($result->actioncontroller)
            ;
            $item->setResource($resource);
            $item->setMenu($menu);
            $items[] = $item;
        }
        return $items;
    }
    
    /**
     * 
     * @param menu_Model_Item $menuItem
     */
    public function save(menu_Model_Item $menuItem)
    {
        $data = array(
                'route' => $menuItem->getRoute(),
                'menu_id' => $menuItem->getMenu()->getId(),
                'resource_id' => $menuItem->getResource()->getId(),
                'parent_id' => $menuItem->getParent()->getId(),
                'icon' => $menuItem->getIcon(),
                'wtype' => $menuItem->getWtype(),
                'params' => $menuItem->getParams(),
                'isPublished' => $menuItem->getIsPublished(),
                'title' => $menuItem->getTitle(),
                'description' => $menuItem->getDescription(),
                'external' => $menuItem->getExternal(),
                'mid' => $menuItem->getMid(),
                'isVisible' => $menuItem->getIsVisible(),
                'css_class' => $menuItem->getCssClass(),
                'depth' => $menuItem->getDepth(),
                'ordering' => $menuItem->getOrdering()
        );
        if ( null === ($id = $menuItem->getId()) ) {
            unset ($data['id']);
            $this->_setPosition($menuItem);
            $data['ordering'] = $menuItem->getOrdering();
            $id = $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id=?' => $menuItem->getId()));
        }
        /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
        $cache = Zend_Registry::get('cache');
        if ( $cache->test('menuItem_getListBymenu_'.$menuItem->getMenu()->getId()) ) {
            $cache->remove('menuItem_getListBymenu_'.$menuItem->getMenu()->getId());
        }
        if ( $cache->test('menu_getAllByMenu_'.$menuItem->getMenu()->getId()) ) {
            $cache->remove('menu_getAllByMenu_'.$menuItem->getMenu()->getId());
        }
        if ( $cache->test('menu_getChildren_'.$menuItem->getMenu()->getId()) ) {
            $cache->remove('menu_getChildren_'.$menuItem->getMenu()->getId());
        }
    }
    
    /**
     * 
     * @param menu_Model_Item $menuItem
     */
    private function _setPosition( menu_Model_Item $menuItem )
    {
        $menuItem->ordering = $this->getDbTable()->getLastPosition($menuItem)+1;
    }
    
    /**
     * 
     * @param menu_Model_Item $menuItem
     * @return boolean
     */
    public function moveUp( menu_Model_Item $menuItem )
    {
        if ( $menuItem->getOrdering() < 1 ) return false;
        $this->getDbTable()->moveUp($menuItem);
        $this->save($menuItem);
        
        /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
        $cache = Zend_Registry::get('cache');
        if ( $cache->test('menuItem_getListBymenu_'.$menuItem->getMenu()->getId()) ) {
            $cache->remove('menuItem_getListBymenu_'.$menuItem->getMenu()->getId());
        }
        if ( $cache->test('menu_getAllByMenu_'.$menuItem->getMenu()->getId()) ) {
            $cache->remove('menu_getAllByMenu_'.$menuItem->getMenu()->getId());
        }
        if ( $cache->test('menu_getChildren_'.$menuItem->getMenu()->getId()) ) {
            $cache->remove('menu_getChildren_'.$menuItem->getMenu()->getId());
        }
    }
    
    /**
     * 
     * @param menu_Model_Item $menuItem
     * @return boolean
     */
    public function moveDown(menu_Model_Item $menuItem)
    {
        if ( $menuItem->getOrdering() == $this->getDbTable()->getLastPosition($menuItem) ) return false;
        $this->getDbTable()->moveDown($menuItem);
        $this->save($menuItem);
        
        /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
        $cache = Zend_Registry::get('cache');
        if ( $cache->test('menuItem_getListBymenu_'.$menuItem->getMenu()->getId()) ) {
            $cache->remove('menuItem_getListBymenu_'.$menuItem->getMenu()->getId());
        }
        if ( $cache->test('menu_getAllByMenu_'.$menuItem->getMenu()->getId()) ) {
            $cache->remove('menu_getAllByMenu_'.$menuItem->getMenu()->getId());
        }
        if ( $cache->test('menu_getChildren_'.$menuItem->getMenu()->getId()) ) {
            $cache->remove('menu_getChildren_'.$menuItem->getMenu()->getId());
        }
    }
    
    /**
     * Fetch menu items under $menuItem and them to it.
     * @param menu_Model_Item $menuItem
     */
    public function getChildren(menu_Model_Item $menuItem)
    {
        $resultSet = $this->getDbTable()->getChildren($menuItem);
        foreach ( $resultSet as $row ) {
            $child = new menu_Model_Item();
            $child->setId($row->id)
                ->setOrdering($row->ordering)
                ->setIcon($row->icon)
                ->setWtype($row->wtype)
                ->setTitle($row->title)
                ->setRoute($row->route)
                ->setIsPublished($row->isPublished)
                ->setDescription($row->description)
                ->setIsVisible($row->isVisible)
                ->setParent($menuItem)
            ;
            $resource = new Acl_Model_Resource();
            $resource->setId($row->resource_id)
                ->setActioncontroller($row->actioncontroller)
                ->setController($row->controller)
                ->setModule($row->module)
            ;
            $child->setResource($resource);
            $child->setMenu($menuItem->getMenu());
            $menuItem->addChild($child);
        }
    }
    
    /**
     * Fetch menu items by menu and add them to it. 
     * @param menu_Model_Menu $menu
     */
    public function getByMenu(menu_Model_Menu $menu)
    {
        $resultSet = $this->getDbTable()->getAllByMenu($menu);
        foreach ( $resultSet as $row ) {
            $menuItem = new menu_Model_Item();
            $menuItem->setId($row->id)
                ->setOrdering($row->ordering)
                ->setIcon($row->icon)
                ->setWtype($row->wtype)
                ->setTitle($row->title)
                ->setRoute($row->route)
                ->setIsPublished($row->isPublished)
                ->setDescription($row->description)
                ->setMenu($menu)
            ;
            $resource = new Acl_Model_Resource();
            $resource->setId($row->resource_id)
                ->setActioncontroller($row->actioncontroller)
                ->setModule($row->module)
                ->setController($row->controller)
            ;
            $menuItem->setResource($resource);
            $menu->addChild($menuItem);
            // menu item parent not added because the search is at first level only (for menu but menu item parents) 
        }
    }

    /**
     * 
     * @param menu_Model_Item $menuItem
     * @param number $level
     * @param string $data
     */
    public function getMenuItemsRecursively(menu_Model_Item $menuItem) 
    {
        $this->getChildren($menuItem);
        $children = $menuItem->getChildren();
        if ( count($children) > 0 ) {
            foreach ( $children as $child ) {
                $this->getMenuItemsRecursively($child);
            }
        }
    }
    
    /**
     * 
     * @return multitype:menu_Model_Item
     */
    public function getRegisteredRoutes()
    {
        $menuItems = array();
        $resultSet = $this->getDbTable()->getRegisteredRoutes();
        foreach ( $resultSet as $row ) {
            $menuItem = new menu_Model_Item();
            $menuItem->setRoute($row->route);
            $resource = new Acl_Model_Resource();
            $resource->setActioncontroller($row->actioncontroller)
                ->setController($row->controller)
                ->setModule($row->module)
            ;
            $menuItem->setResource($resource);
            $menuItems[] = $menuItem;
        }
        return $menuItems;
    }
    
    /**
     * 
     * @param menu_Model_Item $menuItem
     */
    public function remove(menu_Model_Item $menuItem) 
    {
        /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
        $cache = Zend_Registry::get('cache');
        if ( $cache->test('menuItem_getListBymenu_'.$menuItem->getMenu()->getId()) ) {
            $cache->remove('menuItem_getListBymenu_'.$menuItem->getMenu()->getId());
        }
        if ( $cache->test('menu_getAllByMenu_'.$menuItem->getMenu()->getId()) ) {
            $cache->remove('menu_getAllByMenu_'.$menuItem->getMenu()->getId());
        }
        if ( $cache->test('menu_getChildren_'.$menuItem->getMenu()->getId()) ) {
            $cache->remove('menu_getChildren_'.$menuItem->getMenu()->getId());
        }
        $this->getDbTable()->remove($menuItem);
    }
}

