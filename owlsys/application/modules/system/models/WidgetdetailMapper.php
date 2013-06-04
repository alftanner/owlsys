<?php

class System_Model_WidgetdetailMapper extends OS_Mapper
{

    /**
     * @var System_Model_WidgetdetailMapper
     */
    protected static $_instance = null;
    
    /**
     * @return System_Model_WidgetdetailMapper
     */
    public static function getInstance()
    {
        if ( is_null(self::$_instance) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * @return System_Model_DbTable_Widgetdetail
     */
    public function getDbTable ()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('System_Model_DbTable_Widgetdetail');
        }
        return $this->_dbTable;
    }

    /**
     * 
     * @param unknown $itemId
     * @param unknown $hooks
     * @return multitype:System_Model_Widget
     */
    public function getWidgetsByHooksAndItemId($itemId, $hooks)
    {
        $widgets = array();
        $resultSet = $this->getDbTable()->getWidgetsByHooksAndItemId($itemId, $hooks);
        foreach ( $resultSet as $row ) {
            $widget = new System_Model_Widget();
            $widget->setId($row->widget_id)
                ->setPosition($row->position)
                ->setShowtitle($row->showtitle)
                ->setTitle($row->title)
                ->setWid($row->wid)
                ->setParams($row->params)
                ->setOrdering($row->ordering)
            ;
            $resource = new Acl_Model_Resource();
            $resource->setModule($row->module)
                ->setController($row->controller)
                ->setActioncontroller($row->actioncontroller)
            ;
            $widget->setResource($resource);
            $widgets[] = $widget;
        }
        return $widgets;
    }

    /**
     * 
     * @param System_Model_Widgetdetail $widgetDetail
     */
    public function save(System_Model_Widgetdetail $widgetDetail)
    {
        $data = array(
                'widget_id'     => $widgetDetail->getWidget()->getId(),
                'menuitem_id'   => ($widgetDetail->getMenuItem()) ? $widgetDetail->getMenuItem()->getId() : null
        );
        $this->getDbTable()->insert($data);
    }

    /**
     * 
     * @param System_Model_Widget $widget
     * @return boolean|multitype:NULL
     */
    public function getByWidget(System_Model_Widget $widget)
    {
        $details = array();
        $resultSet = $this->getDbTable()->getByWidget($widget);
        if ( $resultSet->count() == 1 && $resultSet->getRow(0)->menuitem_id == 0 ) {
            return false;
        }
        foreach ( $resultSet as $row ) {
            $details[] = $row->menuitem_id;
        }
        return $details;
    }
    
    public function delete(System_Model_Widget $widget)
    {
        $where = $this->getDbTable()->getAdapter()->quoteInto('widget_id = ?', (int)$widget->getId());
        $this->getDbTable()->delete($where);
    }
    
}

