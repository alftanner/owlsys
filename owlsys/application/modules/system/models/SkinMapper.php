<?php

class System_Model_SkinMapper extends OS_Mapper
{

    /**
     * @var System_Model_SkinMapper
     */
    protected static $_instance = null;
    
    /**
     * @return System_Model_SkinMapper
     */
    public static function getInstance()
    {
        if ( is_null(self::$_instance) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * @return System_Model_DbTable_Skin
     */
    public function getDbTable ()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('System_Model_DbTable_Skin');
        }
        return $this->_dbTable;
    }
    
    public function getSkinSelected(System_Model_Skin $skin)
    {
        $row = $this->getDbTable()->getSkinSelected();
        $skin->setId($row->id)
            ->setAuthor($row->author)
            ->setDescription($row->description)
            ->setIsSelected($row->isSelected)
            ->setLicense($row->license)
            ->setName($row->name)
        ;
    }

    /**
     * 
     * @return multitype:System_Model_Skin
     */
    public function getList()
    {
        $skins = array();
        $resultSet = $this->getDbTable()->getList();
        foreach ( $resultSet as $row ) {
            $skin = new System_Model_Skin();
            $skin->setId($row->id)
                ->setAuthor($row->author)
                ->setDescription($row->description)
                ->setIsSelected($row->isSelected)
                ->setLicense($row->license)
                ->setName($row->name)
            ;
            $skins[] = $skin;
        }
        return $skins;
    }

    public function find($id, System_Model_Skin $skin)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            throw new Exception('Skin not found');
        }
        $row = $result->current();
        $skin->setId($id)
            ->setAuthor($row->author)
            ->setDescription($row->description)
            ->setIsSelected($row->isSelected)
            ->setLicense($row->license)
            ->setName($row->name)
        ;
    }

    public function save(System_Model_Skin $skin)
    {
        $data = array(
                'isSelected' => $skin->getIsSelected()
        );
        $this->getDbTable()->update($data, array('id=?' => $skin->getId()));
        /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
        $cache = Zend_Registry::get('cache');
        $cacheId = 'system_getSkinSelected';
        if ( $cache->test($cacheId) ) {
            $cache->remove($cacheId);
        }
    }
}

