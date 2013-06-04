<?php

class Acl_Model_AccountMapper extends OS_Mapper
{

    /**
     * @var Acl_Model_AccountMapper
     */
    protected static $_instance = null;
    
    /**
     * @return Acl_Model_AccountMapper
     */
    public static function getInstance()
    {
        if ( is_null(self::$_instance) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @return Acl_Model_DbTable_Account
     */
    public function getDbTable ()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Acl_Model_DbTable_Account');
        }
        return $this->_dbTable;
    }
    
    /**
     * 
     * @param number $id
     * @param Acl_Model_Account $account
     */
    public function find($id, Acl_Model_Account $account)
    {
        $translate = Zend_Registry::get('Zend_Translate');
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            throw new Zend_Exception($translate->translate("Row not found"));
        }
        $row = $result->current();
        $account->setId($row->id)
            ->setEmail($row->email)
            ->setEmailAlternative($row->email_alternative)
            ->setFullname($row->fullname)
            ->setIsBlocked($row->isBlocked)
            ->setLastVisitDate($row->lastvisitdate)
            ->setPassword($row->password)
            ->setRegisterDate($row->registerdate)
            ->setRecoverpwdtoken($row->recoverpwdtoken);
        $role = new Acl_Model_Role();
        $role->setId($row->role_id);
        $account->setRole($role);
    }
    
    /**
     * 
     * @param Acl_Model_Account $account
     * @return boolean
     */
    public function login(Acl_Model_Account $account)
    {
        $accountToValidate = $this->getDbTable()->getByEmail($account->getEmail());
        if ( !$accountToValidate ) return false;
        return $this->getDbTable()->login($account, $accountToValidate);
    }

    public function getList()
    {
        $resultSet = $this->getDbTable()->getList();
        $accounts = array();
        foreach ( $resultSet as $row ) {
            $account = new Acl_Model_Account();
            $account->setId($row->id);
            $account->setEmail($row->email);
            $account->setRegisterDate($row->registerdate);
            $account->setLastVisitDate($row->lastvisitdate);
            $account->setIsBlocked($row->isBlocked);
            $role = new Acl_Model_Role();
            $role->setName($row->role);
            $role->setId($row->roleId);
            $account->setRole($role);
            $accounts[] = $account;
        }
        return $accounts;
    }

    /**
     * 
     * @param Acl_Model_Account $account
     */
    public function save(Acl_Model_Account $account)
    {
        $data = array(
                'email' => $account->getEmail(),
                'password' => $account->getPassword(),
                'isBlocked' => $account->getIsBlocked(),
                'role_id' => $account->getRole()->getId(),
                'fullname' => $account->getFullname(),
                'email_alternative' => $account->getEmailAlternative(),
                'recoverpwdtoken' => $account->getRecoverpwdtoken(),
                'lastvisitdate' => $account->getLastVisitDate()
        );
        if ( null === ($id = $account->getId()) ) {
            unset ($data['id']);
            $id = $this->getDbTable()->insert($data);
            $account->setId($id);
        } else {
            $this->getDbTable()->update($data, array('id=?' => $account->getId()));
        }
    }
    
    public function getByEmail($email)
    {
        $account = null;
        $row = $this->getDbTable()->getByEmail($email);
        if ( $row ) {
            $account = new Acl_Model_Account();
            $account->setId($row->id)
                ->setEmail($row->email)
                ->setEmailAlternative($row->email_alternative)
                ->setFullname($row->fullname)
                ->setIsBlocked($row->isBlocked)
                ->setLastVisitDate($row->lastvisitdate)
                ->setPassword($row->password)
                ->setRegisterDate($row->registerdate)
                ->setRecoverpwdtoken($row->recoverpwdtoken);
            $role = new Acl_Model_Role();
            $role->setId($row->role_id);
            $account->setRole($role);
        }
        return $account;
    }

}

