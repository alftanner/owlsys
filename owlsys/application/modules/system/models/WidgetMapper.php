<?php

class System_Model_WidgetMapper extends OS_Mapper
{

    /**
     * @var System_Model_WidgetMapper
     */
    protected static $_instance = null;
    
    /**
     * @return System_Model_WidgetMapper
     */
    public static function getInstance()
    {
        if ( is_null(self::$_instance) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * @return System_Model_DbTable_Widget
     */
    public function getDbTable ()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('System_Model_DbTable_Widget');
        }
        return $this->_dbTable;
    }
    
    /**
     * 
     * @return multitype:System_Model_Widget
     */
    public function getList()
    {
        $widgets = array();
        $resultSet = $this->getDbTable()->getList();
        foreach ( $resultSet as $row ) {
            $widget = new System_Model_Widget();
            $widget->setId($row->id)
                ->setTitle($row->title)
                ->setIsPublished($row->isPublished)
                ->setOrdering($row->ordering)
                ->setPosition($row->position)
            ;
            $resource = new Acl_Model_Resource();
            $resource->setId($row->resource_id)
                ->setActioncontroller($row->actioncontroller)
                ->setController($row->controller)
                ->setModule($row->module)
            ;
            $widget->setResource($resource);
            $widgets[] = $widget;
        }
        return $widgets;
    }
    
    /**
     * 
     * @param System_Model_Widget $widget
     * @return boolean
     */
    public function moveUp(System_Model_Widget $widget)
    {
        if ( $widget->getOrdering() < 1 ) return false;
        $this->getDbTable()->moveUp($widget);
        $this->save($widget);
    }
    
    /**
     * 
     * @param System_Model_Widget $widget
     * @return boolean
     */
    public function moveDown(System_Model_Widget $widget)
    {
        if ( $widget->getOrdering() == $this->getDbTable()->getLastPosition($widget) ) return false;
        $this->getDbTable()->moveDown($widget);
        $this->save($widget);
    }
    
    /**
     * 
     * @param System_Model_Widget $widget
     */
    public function save(System_Model_Widget $widget)
    {
        $data = array(
                'isPublished' => $widget->getIsPublished(),
                'ordering' => $widget->getOrdering(),
                'params' => $widget->getParams(),
                'position' => $widget->getPosition(),
                'resource_id' => $widget->getResource()->getId(),
                'showtitle' => $widget->getShowtitle(),
                'title' => $widget->getTitle(),
                'wid' => $widget->getWid()
        );
        if ( null === ($id = $widget->getId()) ) {
            unset ($data['id']);
            $this->_setPosition($widget);
            $data['ordering'] = $widget->getOrdering();
            $id = $this->getDbTable()->insert($data);
            $widget->setId($id);
        } else {
            $this->getDbTable()->update($data, array('id=?' => $widget->getId()));
        }
    }

    /**
     * 
     * @param System_Model_Widget $widget
     */
    private function _setPosition( System_Model_Widget $widget )
    {
        $widget->ordering = $this->getDbTable()->getLastPosition($widget)+1;
    }
    
    /**
     * 
     * @param number $id
     * @param System_Model_Widget $widget
     */
    public function find($id, System_Model_Widget $widget)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            throw new Exception('Widget not found');
        }
        $row = $result->current();
        $widget->setId($row->id)
            ->setIsPublished($row->isPublished)
            ->setOrdering($row->ordering)
            ->setParams($row->params)
            ->setPosition($row->position)
            ->setShowtitle($row->showtitle)
            ->setTitle($row->title)
            ->setWid($row->wid)
        ;
        $resource = new Acl_Model_Resource();
        $resource->setId($row->resource_id);
        $widget->setResource($resource);
    }

    public function remove(System_Model_Widget $widget)
    {
        $this->getDbTable()->remove($widget);
    }


}

