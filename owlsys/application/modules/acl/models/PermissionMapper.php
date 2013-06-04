<?php

class Acl_Model_PermissionMapper extends OS_Mapper
{

    /**
     * @var Acl_Model_PermissionMapper
     */
    protected static $_instance = null;
    
    /**
     * @return Acl_Model_PermissionMapper
     */
    public static function getInstance()
    {
        if ( is_null(self::$_instance) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * @return Acl_Model_DbTable_Permission
     */
    public function getDbTable ()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Acl_Model_DbTable_Permission');
        }
        return $this->_dbTable;
    }
    
    /**
     * 
     * @param Acl_Model_Resource[] $resources
     * @param Acl_Model_Role $role
     * @return multitype:Acl_Model_Permission
     */
    public function getAllowedByRole(Acl_Model_Role $role)
    {
        $allowedResources = array();
        $resultSet = $this->getDbTable()->getAllowedByRole($role);
        foreach ($resultSet as $result) {
            $allowedResources[] = $result->resource_id;
        }
        return $allowedResources;
    }

    /**
     * 
     * @param unknown $id
     * @param Acl_Model_Permission $permission
     * @throws Zend_Exception
     */
    public function find($id, Acl_Model_Permission $permission)
    {
        $translate = Zend_Registry::get('Zend_Translate');
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            throw new Zend_Exception($translate->translate("Row not found"));
        }
        $row = $result->current();
        $permission->setId($row->id)
            ->setIsAllowed($row->isAllowed)
        ;
        $resource = new Acl_Model_Resource();
        $resource->setId($row->resource_id);
        $permission->setResource($resource);
        $role = new Acl_Model_Role();
        $role->setId($role->role_id);
        $permission->setRole($role);
    }
    
    /**
     * 
     * @param Acl_Model_Permission $permission
     */
    public function save(Acl_Model_Permission $permission)
    {
        $data = array(
                'role_id' => $permission->getRole()->getId(),
                'isAllowed' => $permission->getIsAllowed(),
                'resource_id' => $permission->getResource()->getId()
        );
        if ( null === ($id = $permission->getId()) ) {
            unset ($data['id']);
            $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id=?' => $permission->getId()));
        }
    }

    /**
     * 
     * @param Acl_Model_Role $role
     * @return multitype:Acl_Model_Permission
     */
    public function getResourcesByRole(Acl_Model_Role $role) 
    {
        $privileges = array();
        $resultSet = $this->getDbTable()->getResourcesByRole($role);
        foreach ($resultSet as $result) {
            $permission = new Acl_Model_Permission();
            $permission->setIsAllowed($result->isAllowed)
                ->setId($result->id);
            $resource = new Acl_Model_Resource();
            $resource->setActioncontroller($result->actioncontroller)
                ->setController($result->controller)
                ->setModule($result->module)
                ->setId($result->resource_id)
            ;
            $permission->setResource($resource);
            $privileges[] = $permission;
        }
        return $privileges;
    }
    
    /**
     * 
     * @param Acl_Model_Role $role
     */
    public function delete(Acl_Model_Role $role)
    {
        $where = $this->getDbTable()->getAdapter()->quoteInto('role_id = ?', (int)$role->getId());
        $this->getDbTable()->delete($where);
    }
    
    /**
     * 
     * @param Acl_Model_Resource $resource
     * @return number
     */
    public function countByResource(Acl_Model_Resource $resource)
    {
        return $this->getDbTable()->countByResource($resource);
    }
    
}

