<?php

class Acl_Model_RoleMapper extends OS_Mapper
{

    /**
     * @var Acl_Model_RoleMapper
     */
    protected static $_instance = null;
    
    /**
     * @return Acl_Model_RoleMapper
     */
    public static function getInstance()
    {
        if ( is_null(self::$_instance) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * @return Acl_Model_DbTable_Role
     */
    public function getDbTable ()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Acl_Model_DbTable_Role');
        }
        return $this->_dbTable;
    }

    /**
     * 
     * @return multitype:Acl_Model_Role
     */
    public function getList()
    {
        $roles = array();
        $resultSet = $this->getDbTable()->getList();
        foreach ( $resultSet as $row )
        {
            $role = new Acl_Model_Role();
            $role->setId($row->id)
                ->setName($row->name);
            $layout = new System_Model_Layout();
            $layout->setId($row->layout_id)
                ->setName($row->layout_name)
            ;
            $role->setLayout($layout);
            $roles[] = $role;
        }
        return $roles;
    }
    
    /**
     * 
     * @param number $id
     * @param Acl_Model_Role $role
     * @throws Zend_Exception
     */
    public function find($id, Acl_Model_Role $role)
    {
        $translate = Zend_Registry::get('Zend_Translate');
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            throw new Zend_Exception($translate->translate("Row not found"));
        }
        $row = $result;
        $role->setId($row->id)
            ->setName($row->name);
        $layout = new System_Model_Layout();
        $layout->setId( $row->layout_id );
        $role->setLayout($layout);
    }

    /**
     * 
     * @param Acl_Model_Role $role
     */
    public function save(Acl_Model_Role $role)
    {
        $data = array(
                'name'      => $role->getName(),
                'layout_id' => $role->getLayout()->getId()
        );
        if ( null === ($id = $role->getId()) ) {
            unset ($data['id']);
            $id = $this->getDbTable()->insert($data);
        } else {
            $this->getDbTable()->update($data, array('id=?' => $role->getId()));
        }
    }
    
    public function remove(Acl_Model_Role $role)
    {
        if ( $role->getId() <= 3 ) {
            throw new Exception('Default roles could not be removed');
        }
        $this->getDbTable()->remove($role);
    }

}

