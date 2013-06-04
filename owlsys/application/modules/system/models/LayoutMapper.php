<?php

class System_Model_LayoutMapper extends OS_Mapper
{
    
    /**
     * @var System_Model_LayoutMapper
     */
    protected static $_instance = null;
    
    /**
     * @return System_Model_LayoutMapper
     */
    public static function getInstance()
    {
        if ( is_null(self::$_instance) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * @return System_Model_DbTable_Layout
     */
    public function getDbTable ()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('System_Model_DbTable_Layout');
        }
        return $this->_dbTable;
    }

    public function find($id, System_Model_Layout $layout)
    {
        /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
        $cache = Zend_Registry::get('cache');
        $cacheId = 'layout_find_'.$id;
        if ( $cache->test($cacheId) ) {
            $row = $cache->load($cacheId);
        } else {
            $result = $this->getDbTable()->find($id);
            if (0 == count($result)) {
                return;
            }
            $row = $result->current();
        }
        $layout->setDescription($row->description)
            ->setId($row->id)
            ->setName($row->name)
            ->setIsPublished($row->isPublished)
        ;
    } 

    /**
     * 
     * @return multitype:System_Model_Layout
     */
    public function getAll()
    {
        $layouts = array();
        $resultSet = $this->getDbTable()->getAll();
        foreach ( $resultSet as $result ) {
            $layout = new System_Model_Layout();
            $layout->setId($result->id)
                ->setDescription($result->description)
                ->setIsPublished($result->isPublished)
                ->setName($result->name)
            ;
            $layouts[] = $layout;
        }
        return $layouts;
    }
}

