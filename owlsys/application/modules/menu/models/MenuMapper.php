<?php

class menu_Model_MenuMapper extends OS_Mapper
{
    
    /**
     * @var menu_Model_MenuMapper
     */
    protected static $_instance = null;
    
    /**
     * @return menu_Model_MenuMapper
     */
    public static function getInstance()
    {
        if ( is_null(self::$_instance) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * @return menu_Model_DbTable_Menu
     */
    public function getDbTable ()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('menu_Model_DbTable_Menu');
        }
        return $this->_dbTable;
    }
    
    public function getList()
    {
        $menus = array();
        $resultSet = $this->getDbTable()->getMenus();
        foreach ($resultSet as $result) {
            $menu = new menu_Model_Menu();
            $menu->setId($result->id)
                ->setName($result->name)
                ->setIsPublished($result->isPublished);
            $menus[] = $menu;
        }
        return $menus;
    }
    
    /**
     * 
     * @param number $status
     * @return multitype:menu_Model_Menu
     */
    public function getByStatus($status)
    {
        $menus = array();
        $resultSet = $this->getDbTable()->getByStatus($status);
        foreach ($resultSet as $result) {
            $menu = new menu_Model_Menu();
            $menu->setId($result->id)
                ->setName($result->name)
                ->setIsPublished($result->isPublished);
            $menus[] = $menu;
        }
        return $menus;
    }
    
    /**
     * 
     * @param number $id
     * @param menu_Model_Menu $menu
     */
    public function find($id, menu_Model_Menu $menu)
    {
        $translate = Zend_Registry::get('Zend_Translate');
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            throw new Zend_Exception($translate->translate("Row not found"));
        }
        $row = $result->current();
        $menu->setId($row->id)
            ->setIsPublished($row->isPublished)
            ->setName($row->name)
        ;
    }
    
    /**
     * 
     * @param menu_Model_Menu $menu
     */
    public function save(menu_Model_Menu $menu)
    {
        $data = array(
                'name' => $menu->getName(),
                'isPublished' => $menu->getIsPublished()
        );
        if ( null === ($id = $menu->getId()) ) {
            unset ($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id=?' => $menu->getId()));
        }
    }
    
    public function remove(menu_Model_Menu $menu)
    {
        $mdlMenuItem = menu_Model_ItemMapper::getInstance();
        $mdlMenuItem->getByMenu($menu);
        if ( count($menu->getChildren()) > 0 ) {
            throw new Exception('The menu has children menu items');
        }
        $this->getDbTable()->remove($menu);
    }

}

