<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc., 51 Franklin
 * St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package acl
 * @subpackage controller
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 *
 */

class Acl_AccountController extends Zend_Controller_Action
{

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     *
     */
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    /**
     * create action for account controller
     *
     */
    public function createAction()
    {
		try {
			$translate = Zend_Registry::get('Zend_Translate');
			$frmAccount = new Acl_Form_Account();
			$frmAccount->setAction( $this->_request->getBaseUrl() . "/account-new" );
			$this->view->frmAccount = $frmAccount;
			
			$frmAccount->removeElement('id');
			$frmAccount->getElement('email')->addValidator(
			        new Zend_Validate_Db_NoRecordExists(array(
			                'table' => 'os_acl_account',
			                'field'=>'email',
			        ))
			);
			$frmAccount->getElement('emailAlternative')->addValidator(
			        new Zend_Validate_Db_NoRecordExists(array(
			                'table' => 'os_acl_account',
			                'field'=>'email_alternative',
			        ))
			);
			
			if ( $this->getRequest()->isPost() )
			{
				if ( $frmAccount->isValid( $_POST ) )
				{
				    $mdlAccount = Acl_Model_AccountMapper::getInstance();
					$account = new Acl_Model_Account();
					$role = new Acl_Model_Role();
					$account->setOptions( $frmAccount->getValues() );
					$account->setIsBlocked(0);
					$role->setId( $frmAccount->getValue('role') );
					$account->setRole($role);
					$salt = hash('SHA512',md5(uniqid(rand(), TRUE)).time());
					$account->setPassword( crypt($account->password, '$6$5000$'.$salt.'$') );
					$account->setRegisterDate( Zend_Date::now()->toString('Y-M-d H-m-s') );
// 					Zend_Debug::dump($account->toArray());
// 					die();
					$mdlAccount->save($account);
					$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("New account added") ) );
					$this->redirect('accounts');
				} 
			} 
		} catch (Exception $e) {
			$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
			$this->redirect('accounts');
		}
    }

    /**
     * Update action for account controller
     * @throws Zend_Exception
     *
     */
    public function updateAction()
    {
    	$translate = Zend_Registry::get('Zend_Translate');
    	$id = null;
    	try {
    		$frmAccount = new Acl_Form_Account();
			$frmAccount->getElement('password')->setRequired(false);
			$frmAccount->getElement('password2')->setRequired(false);
			$id = $this->getRequest()->getParam( "id", null );
			$auth = Zend_Auth::getInstance();
			$identity = $auth->getIdentity();
			if ( $identity->role_id != 1 ) {
				$id = $identity->id;
				$frmAccount->removeElement('role_id');
				$frmAccount->removeElement('id');
			} else {
				$id = $this->getRequest()->getParam( "id", $identity->id );
			}
			
			$mdlAccount = Acl_Model_AccountMapper::getInstance();
			$account = new Acl_Model_Account();
			$mdlAccount->find($id, $account);
			
			$id = $account->getId();
			
			$frmAccount->populate( $account->toArray() );
			$frmAccount->setAction( $this->_request->getBaseUrl() . "/account-update/".$account->getId() );
			$this->view->frmAccount = $frmAccount;
			
			$frmAccount->getElement('email')->addValidator(
			        new Zend_Validate_Db_NoRecordExists(array(
			                'table' => 'os_acl_account',
			                'field'=>'email',
			                'exclude'=>array('field'=>'id','value'=>$account->getId())
			        ))
			);
			$frmAccount->getElement('emailAlternative')->addValidator(
			        new Zend_Validate_Db_NoRecordExists(array(
			                'table' => 'os_acl_account',
			                'field'=>'email_alternative',
			                'exclude'=>array('field'=>'id','value'=>$account->getId())
			        ))
			);
	
			if ( $this->getRequest()->isPost() )
			{
				if ( $frmAccount->isValid( $_POST ) )
				{
					$oldPassword = $account->getPassword();
					$account->setOptions( $frmAccount->getValues() );
					$password = $this->getRequest()->getParam('password');
					if ( strlen($password) > 0 ) {
					    $salt = hash('SHA512',md5(uniqid(rand(), TRUE)).time());
					    $account->setPassword(crypt($account->password, '$6$5000$'.$salt.'$'));
					}
					else $account->setPassword($oldPassword);
					$role = new Acl_Model_Role();
					$role->setId( $frmAccount->getValue('role') );
					$account->setRole($role);
					$mdlAccount->save($account);
					$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("Account updated") ) );
					$this->redirect('accounts'); 
				}
			} 
			
    	} catch (Exception $e) {
    		$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
    		$this->redirect('account-update/'.$id);
    	}
    }

    /**
     * list action for account controller
     *
     */
    public function listAction()
    {
        try {
        	$mdlAccount = Acl_Model_AccountMapper::getInstance();
        	$paginator = Zend_Paginator::factory($mdlAccount->getList());
        	$paginator->setItemCountPerPage(10);
        	$pageNumber = $this->getRequest()->getParam('page',1);
        	$paginator->setCurrentPageNumber($pageNumber);
        	$this->view->accounts = $paginator;
        } catch (Exception $e) {
        	echo $e->getMessage();
        }
        return;
    }

    /**
     * delete action for account controller
     * @throws Zend_Exception
     *
     */
    public function deleteAction()
    {
    	$translate = Zend_Registry::get('Zend_Translate');
        try {
        	$id = $this->getRequest()->getParam( "id" );
	        if ( $id == 1 ) {
	        	throw new Exception( $translate->translate("ACL_DEFAULT_ACCOUNT_COULD_NOT_BE_DROPPED") );
	        }
			$mdlAccount = new Acl_Model_Account();
			$account = $mdlAccount->find( $id )->current();
			if ( !$account ) {
				throw new Exception( $translate->translate("LBL_ROW_NOT_FOUND") );
			}
			$account->delete();
			$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("LBL_MENU_DELETED_SUCCESSFULLY") ) );
			$this->redirect('accounts');
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('accounts');
        }
        
    }

    /**
     * block action for account controller
     * @throws Zend_Exception
     *
     */
    public function blockAction()
    {
    	try {
    		$translate = Zend_Registry::get('Zend_Translate');
        	$mdlAccount = Acl_Model_AccountMapper::getInstance();
        	$account = new Acl_Model_Account();
        	$id = $this->getRequest()->getParam('id', 0);
        	$mdlAccount->find($id, $account);
	    	if ( $account->getIsBlocked() == 0 ) 
			{
				$account->setIsBlocked(1);
				$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("ACL_ACCOUNT_BLOCKED_SUCCESSFULLY") ) );
			} else {
				$account->setIsBlocked(0);
				$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("ACL_ACCOUNT_UNBLOCKED_SUCCESSFULLY") ) );
			}
			$mdlAccount->save($account);
        	$this->redirect('accounts');
    	} catch (Exception $e) {
    		$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('accounts');
    	}
    }

    public function resetpasswordAction()
    {
        /* @var $translate Zend_Translate */
        $translate = Zend_Registry::get('Zend_Translate');
        try {
            $frmAccount = new Acl_Form_Account();
            $this->view->frmAccount = $frmAccount;
            
            $frmAccount->removeElement('fullname');
            $frmAccount->removeElement('email_alternative');
            $frmAccount->removeElement('role_id');
            $frmAccount->getElement('email')->removeValidator('Db_NoRecordExists');
            $frmAccount->getElement('submit')->setLabel('Send');
            $change = intval($this->getRequest()->getParam('change', 0));
            
            if ( $change == 1 ) {
                $hashtoken = $frmAccount->createElement('textarea', 'ht');
                $hashtoken->setAttrib('cols', 10);
                $hashtoken->setAttrib('rows', 5);
                $hashtoken->setLabel('Verification code');
                $hashtoken->addFilter( new Zend_Filter_StringTrim() );
                $hashtoken->addFilter( new Zend_Filter_Alnum() );
                $hashtoken->addValidator( new Zend_Validate_Alnum() );
                $hashtoken->addValidator( new Zend_Validate_NotEmpty() );
                $hashtoken->setOrder( $frmAccount->getElement('email')->getOrder()+1 );
                $frmAccount->addElement($hashtoken);
                $frmAccount->getElement('password')->setLabel("New password");
                $frmAccount->setAction( $this->_request->getBaseUrl() . "/changepassword" );
            } else {
                $frmAccount->removeElement('password');
                $frmAccount->removeElement('password2');
                $frmAccount->setAction( $this->_request->getBaseUrl() . "/resetpassword" );
            }
            
            if ( $this->getRequest()->isPost() )
			{
				if ( $frmAccount->isValid( $_POST ) )
				{
				    $mdlAccount = Acl_Model_AccountMapper::getInstance();
				    $account = $mdlAccount->getByEmail( $frmAccount->getValue('email') );
				    if ( $account )
				    {
				        if ( $change == 0 ) {
				            $salt = hash('SHA512',md5($account->email.'.'.uniqid(rand(), TRUE)).time().'.'.$account->id);
				            $account->recoverpwdtoken = $salt;
				            $account->save();
				            
				            $options = Zend_Registry::get('options');
				            $projectName = $options['resources']['layout']['projectname'];
				            $emailSupport = $options['resources']['layout']['email_support'];
				            $emailSupportName = $options['resources']['layout']['email_support_name'];
				            
				            $serverurl = new Zend_View_Helper_ServerUrl();
				            
				            $msg = sprintf($translate->translate("ACL_PASSWORD_RESET_REQUEST_BODY"), $projectName, $serverurl->serverUrl().'/changepassword', $salt, $emailSupport);
				            
				            $mail = new Zend_Mail();
				            $mail->setBodyHtml($msg);
				            $mail->setFrom($emailSupport, $emailSupportName);
				            $mail->addTo($account->email, $account->fullname);
				            $mail->setSubject($translate->translate('ACL_PASSWORD_RESET_EMAIL_SUBJECT'));
				            $mail->send();
				            
				            $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("ACL_VALIDATION_CODE_SENT_MESSAGE") ) );
				            $this->redirect('changepassword');
				        } else {
				            if ( strcasecmp($account->recoverpwdtoken, $frmAccount->getValue('ht')) == 0 ) {
				                $account->password = crypt($frmAccount->getValue('password'), '$6$5000$'.$salt.'$');
				                $account->recoverpwdtoken = "";
				                $mdlAccount->save($account);
				                $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("ACL_PASSWORD_CHANGED") ) );
				                $this->redirect('login');
				            } else {
				                $account->recoverpwdtoken = "";
				                $mdlAccount->save($account);
				                $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $translate->translate("ACL_VALIDATION_CODE_INVALID") ) );
				                $this->redirect('resetpassword');
				            }
				        }
				        
				    } else {
				        throw new Exception($translate->translate("ACL_EMAIL_NOT_FOUND"));
				    }
				    
				}
			}
        } catch (Exception $e) {
            #echo $e->getMessage();
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $translate->translate("ACL_ERROR_ON_RESET_PASSWORD") ) );
            $this->redirect('resetpassword');
        }
        return;
    }


}

