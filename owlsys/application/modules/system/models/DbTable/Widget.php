<?php

class System_Model_DbTable_Widget extends Zend_Db_Table_Abstract
{

    protected $_name = 'widget';
    protected $_dependentTables = array ( 'System_Model_DbTable_Widgetdetail' );
    
    protected $_referenceMap = array (
            'refWidgetResource' => array(
                    'columns'			=> array ( 'resource_id' ),
                    'refTableClass'	=> 'Acl_Model_DbTable_Resource',
                    'refColumns'		=> array ( 'id' ),
            )
    );
    
    function __construct() {
        $this->_name = Zend_Registry::get('tablePrefix').$this->_name;
        parent::__construct();
    }
    
    /**
     * returns the last position of a contact list according to the category they belong in ascending order
     * @param System_Model_Widget $widget
     * @return number
     */
    public function getLastPosition( System_Model_Widget $widget)
    {
        $select = $this->select()
            ->where('position=?', $widget->getPosition())
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
        return $rows;
    }
    
    /**
     * Moves the record position one above
     * @param System_Model_Widget $widget
     */
    public function moveUp( System_Model_Widget $widget )
    {
        $select = $this->select()
            ->order('ordering DESC')
            ->where("ordering < ?", $widget->getOrdering(), Zend_Db::INT_TYPE)
            ->where("position = ?", $widget->getPosition());
        $previousItem = $this->fetchRow($select);
        if ( $previousItem )
        {
            $previousPosition = $previousItem->ordering;
            $previousItem->ordering = $widget->getOrdering();
            $previousItem->save();
            $widget->setOrdering($previousPosition);
        }
    }
    
    /**
     * Moves the record position one down
     * @param System_Model_Widget $widget
     */
    public function moveDown( System_Model_Widget $widget )
    {
        $select = $this->select()
            ->order('ordering ASC')
            ->where("ordering > ?", $widget->getOrdering(), Zend_Db::INT_TYPE)
            ->where("position = ?", $widget->getPosition());
        $nextItem = $this->fetchRow($select);
        if ( $nextItem )
        {
            $nextPosition = $nextItem->ordering;
            $nextItem->ordering = $widget->getOrdering();
            $nextItem->save();
            $widget->setOrdering($nextPosition);
        }
    }

    public function remove(System_Model_Widget $widget)
    {
        $row = $this->find($widget->getId())->current();
        $menuItemsWidget = $row->findDependentRowset('System_Model_DbTable_Widgetdetail', 'refWidget');
        foreach ( $menuItemsWidget as $miw ) $miw->delete();
        $row->delete();
    }

    
}

