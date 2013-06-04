<?php

class Acl_Model_ResourceMapper extends OS_Mapper
{

    /**
     * @var Acl_Model_ResourceMapper
     */
    protected static $_instance = null;
    
    /**
     * @return Acl_Model_ResourceMapper
     */
    public static function getInstance()
    {
        if ( is_null(self::$_instance) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * @return Acl_Model_DbTable_Resource
     */
    public function getDbTable ()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Acl_Model_DbTable_Resource');
        }
        return $this->_dbTable;
    }
    
    /**
     * 
     * @param unknown $id
     * @param Acl_Model_Resource $resource
     * @throws Zend_Exception
     */
    public function find($id, Acl_Model_Resource $resource)
    {
        $translate = Zend_Registry::get('Zend_Translate');
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            throw new Zend_Exception($translate->translate("Row not found"));
        }
        $row = $result->current();
        $resource->setId($row->id)
            ->setActioncontroller($row->actioncontroller)
            ->setController($row->controller)
            ->setModule($row->module)
        ;
    }
    
    /**
     * 
     * @param Acl_Model_Resource $resource
     */
    public function save(Acl_Model_Resource $resource)
    {
        $data = array(
                'module' => $resource->getModule(),
                'actioncontroller' => $resource->getActioncontroller(),
                'controller' => $resource->getController() 
        );
        if ( null === ($id = $resource->getId()) ) {
            unset ($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id=?' => $resource->getId()));
        }
    }
    
    /**
     * 
     * @return multitype:Acl_Model_Resource
     */
    public function getAll()
    {
        $resources = array();
        $resultSet = $this->getDbTable()->getAll();
        foreach ($resultSet as $result) {
            $resource = new Acl_Model_Resource();
            $resource->setActioncontroller($result->actioncontroller)
                ->setController($result->controller)
                ->setId($result->id)
                ->setModule($result->module)
            ;
            $resources[] = $resource;
        }
        return $resources;
    }
    
    /**
     * 
     * @param Acl_Model_Resource $resource
     * @return multitype:Acl_Model_Resource
     */
    public function getByModule( Acl_Model_Resource $resource )
    {
        $resources = array();
        $resultSet = $this->getDbTable()->getByModule($resource);
        foreach ($resultSet as $result) {
            $resource = new Acl_Model_Resource();
            $resource->setActioncontroller($result->actioncontroller)
                ->setController($result->controller)
                ->setId($result->id)
                ->setModule($result->module)
            ;
            $resources[] = $resource;
        }
        return $resources;
    }

    /**
     * 
     * @param Acl_Model_Resource $resource
     */
    public function getIdByDetail( Acl_Model_Resource $resource )
    {
        $row = $this->getDbTable()->getIdByDetail($resource);
        if ( !$row ) { 
            $resource = null;
        } else {
            $resource->setId($row->id)
                ->setActioncontroller($row->actioncontroller)
                ->setController($row->controller)
                ->setModule($row->module);
        }
    }

    public function remove(Acl_Model_Resource $resource)
    {
        $mdlPermission = Acl_Model_PermissionMapper::getInstance();
        if ( $mdlPermission->countByResource($resource) > 0 ) {
            throw new Exception('The resource is currently used');
        }
        $this->getDbTable()->remove($resource);
    }
}

