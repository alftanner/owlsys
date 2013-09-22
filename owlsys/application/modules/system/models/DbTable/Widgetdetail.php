<?php

class System_Model_DbTable_Widgetdetail extends Zend_Db_Table_Abstract
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
     * @param System_Model_Widget $widget
     * @return boolean
     */
    public function isRenderForAll( System_Model_Widget $widget )
    {
        $select = $this->select()
            ->where( 'widget_id=?', $widget->getId() )
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
//             echo $select->__toString();
//             die();
            $cache->save($rows, $cacheId);
        }
        return $rows;
    }

    /**
     * 
     * @param System_Model_Widget $widget
     */
    public function getByWidget(System_Model_Widget $widget)
    {
        $select = $this->select()
            ->from( $this->_name, 'IFNULL(menuitem_id,0) AS menuitem_id' )
            ->where('widget_id=?', $widget->getId(), Zend_Db::INT_TYPE)
        ;
        return $this->fetchAll($select);
    }
    
}

