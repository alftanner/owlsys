<?php

class menu_Model_DbTable_Menu extends Zend_Db_Table_Abstract
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
    public function remove(menu_Model_Menu $menu)
    {
        $select = $this->select()
            ->where('id=?',$menu->getId(), Zend_Db::INT_TYPE)
        ;
        $row = $this->fetchRow($select);
        $row->delete();
    }
}

