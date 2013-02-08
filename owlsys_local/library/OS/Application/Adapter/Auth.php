<?php
class OS_Application_Adapter_Auth implements Zend_Auth_Adapter_Interface {
    
    protected $_username;
    protected $_password;
    protected $_email;
    protected $_row;
    
    public function __construct($email, $password){
        $this->_email = $email;
        $this->_password = $password;
    }
    
    public function authenticate(){
        $mdlAccount = new Acl_Model_Account();
        $select = $mdlAccount->select()->where('email=?', $this->_email)->where('block=0')->limit(1);
        #trigger_error($select->__toString());
        $row = $mdlAccount->fetchRow($select);
        
        if ( $row == null ) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $this->_email);
        }
        $data = $row->toArray();
        
        if ( crypt($this->_password, $data['password']) !== $data['password'] ) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $this->_email);
        } else {
            $this->_row = $row;
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, 'email');
            #return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this->_email);
        }
    }
    
    public function getResultRowObject()
    {
        #trigger_error($this->_row);
        $sesion = new Zend_Session_Namespace('account');
        $sesion->aid = $this->_row->id; # account id
        $sesion->email = $this->_row->email;
        $sesion->role_id = $this->_row->role_id;
        $sesion->fullname = $this->_row->fullname;
        return $this->_row;
    }
}