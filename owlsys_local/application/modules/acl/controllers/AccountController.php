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
			$frmAccount->setAction( $this->_request->getBaseUrl() . "/acl/account/create" );
			$this->view->frmAccount = $frmAccount;
			/*$fields = array();
			foreach ( $frmAccount->getElements() as $element ) $fields[] = $element->getName();
			$frmAccount->addDisplayGroup( $fields, 'form', array( 'legend' => "ACL_CREATE_ACCOUNT" ) );*/
			
			if ( $this->getRequest()->isPost() )
			{
				if ( $frmAccount->isValid( $_POST ) )
				{
					$mdlAccount = new Acl_Model_Account();
					$account = $mdlAccount->createRow( $frmAccount->getValues() );
					#$account->salt = hash('SHA512',md5(uniqid(rand(), TRUE)).time());
					$salt = hash('SHA512',md5(uniqid(rand(), TRUE)).time());
					$account->password = crypt($account->password, '$6$5000$'.$salt.'$');
					#$account->password = md5( $account->salt.$account->password );
					$account->save();
					$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_CHANGES_SAVED") ) );
					$this->_helper->redirector( "list", "account", "acl" );
				} else {
				    #print_r( $frmAccount->getErrorMessages() );
					#throw new Zend_Exception( 'w|'.$translate->translate("LABEL_FIELDS_NOT_VALID") );
					#$this->_helper->flashMessenger->addMessage( 'w|'.$translate->translate("LABEL_FIELDS_NOT_VALID") );
				}
			} 
		} catch (Exception $e) {
			$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
			$this->_helper->redirector( "list", "account", "acl" );
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
    	try {
    		$frmAccount = new Acl_Form_Account();
			$frmAccount->getElement('password')->setRequired(false);
			$frmAccount->getElement('password2')->setRequired(false);
			$id = $this->getRequest()->getParam( "id", null );
			$auth = Zend_Auth::getInstance();
			$identity = $auth->getIdentity();
			$id = null;
			if ( $identity->role_id != 1 ) {
				$id = $identity->id;
				$frmAccount->removeElement('role_id');
				$frmAccount->removeElement('id');
			} else {
				$id = $this->getRequest()->getParam( "id", $identity->id );
			}
			
			$mdlAccount = new Acl_Model_Account();
			$account = $mdlAccount->find( $id )->current();
			
			if ( !$account ) throw new Zend_Exception($translate->translate("LBL_ROW_NOT_FOUND"));
			
			$frmAccount->populate( $account->toArray() );
			/*$fields = array();
			foreach ( $frmAccount->getElements() as $element ) $fields[] = $element->getName();
			$frmAccount->addDisplayGroup( $fields, 'form', array( 'legend' => $translate->translate("ACL_UPDATE_ACCOUNT"), ) );*/
			$frmAccount->setAction( $this->_request->getBaseUrl() . "/update-account" );
			$this->view->frmAccount = $frmAccount;
			
			$frmAccount->getElement('email')->removeValidator('Db_NoRecordExists');
			$frmAccount->getElement('email_alternative')->removeValidator('Db_NoRecordExists');
	
			if ( $this->getRequest()->isPost() )
			{
				if ( $frmAccount->isValid( $_POST ) )
				{
				    #echo $id;
				    #die();
					$oldPassword = $account->password;
					$account->setFromArray( $frmAccount->getValues() );
					$password = $this->getRequest()->getParam('password');
					#if ( strlen($password) > 0 ) $account->password = md5($account->salt.$password);
					if ( strlen($password) > 0 ) {
					    $salt = hash('SHA512',md5(uniqid(rand(), TRUE)).time());
					    $account->password = crypt($account->password, '$6$5000$'.$salt.'$');
					    #$account->password = md5($account->salt.$password);
					}
					else $account->password = $oldPassword;
					$account->save();
					$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_CHANGES_SAVED") ) );
					if ( $identity->role_id == 1 )
					    $this->_helper->redirector( "list", "account", "acl" );
					else {
					    #$this->_helper->redirector( "update", "account", "acl" );
					    $this->_redirect('update-account');
					} 
				}
			} 
			
    	} catch (Exception $e) {
    		$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
			#$this->_helper->redirector( "update", "account", "acl" );
    		$this->_redirect('update-account');
    	}
    }

    /**
     * list action for account controller
     *
     */
    public function listAction()
    {
        try {
        	$mdlAccount = new Acl_Model_Account();
        	$adapter = $mdlAccount->getPaginatorAdapterList();
        	$paginator = new Zend_Paginator($adapter);
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
	        	$this->_helper->flashMessenger->addMessage( array('type'=>'warning', 'header'=>'', 'message' => $translate->translate("ACL_DEFAULT_ACCOUNT_COULD_NOT_BE_DROPPED") ) );
	        	return $this->_helper->redirector( "list", "account", "acl" );
	        }
			$mdlAccount = new Acl_Model_Account();
			$account = $mdlAccount->find( $id )->current();
			if ( !$account ) {
				$this->_helper->flashMessenger->addMessage( array('type'=>'warning', 'header'=>'', 'message' => $translate->translate("LBL_ROW_NOT_FOUND") ) );
			}
			$account->delete();
			$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_MENU_DELETED_SUCCESSFULLY") ) );
			$this->_helper->redirector( "list", "account", "acl" );
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "list", "account", "acl" );
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
        	$mdlAccount = new Acl_Model_Account();
        	$id = $this->getRequest()->getParam('id', 0);
        	$account = $mdlAccount->find( $id )->current();
        	if ( !$account ) {
        		throw new Zend_Exception($translate->translate("LABEL_ROW_NOT_FOUND"));
        	}
	    	if ( $account->block == 0 ) 
			{
				$account->block = 1;
				$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("ACL_ACCOUNT_BLOCKED_SUCCESSFULLY") ) );
			} else {
				$account->block = 0;
				$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("ACL_ACCOUNT_UNBLOCKED_SUCCESSFULLY") ) );
			}
        	$account->save();
        	$this->_helper->redirector( "list", "account", "acl" );
    	} catch (Exception $e) {
    		$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "list", "account", "acl" );
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
            $frmAccount->getElement('submit')->setLabel('ACL_SEND');
            $change = intval($this->getRequest()->getParam('change', 0));
            
            if ( $change == 1 ) {
                $hashtoken = $frmAccount->createElement('textarea', 'ht');
                $hashtoken->setAttrib('cols', 10);
                $hashtoken->setAttrib('rows', 5);
                $hashtoken->setLabel('ACL_VERIFICATION_CODE');
                $hashtoken->addFilter( new Zend_Filter_StringTrim() );
                $hashtoken->addFilter( new Zend_Filter_Alnum() );
                $hashtoken->addValidator( new Zend_Validate_Alnum() );
                $hashtoken->addValidator( new Zend_Validate_NotEmpty() );
                $hashtoken->setOrder( $frmAccount->getElement('email')->getOrder()+1 );
                $frmAccount->addElement($hashtoken);
                $frmAccount->getElement('password')->setLabel("ACL_NEW_PASSWORD");
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
				    $mdlAccount = new Acl_Model_Account();
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
				            
				            $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("ACL_VALIDATION_CODE_SENT_MESSAGE") ) );
				            $this->_helper->redirector( "changepassword", "account", "acl" );
				        } else {
				            if ( strcasecmp($account->recoverpwdtoken, $frmAccount->getValue('ht')) == 0 ) {
				                $account->password = crypt($frmAccount->getValue('password'), '$6$5000$'.$salt.'$');
				                $account->recoverpwdtoken = "";
				                $account->save();
				                $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("ACL_PASSWORD_CHANGED") ) );
				                $this->_helper->redirector( "login", "authentication", "acl" );
				            } else {
				                $account->recoverpwdtoken = "";
				                $account->save();
				                $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $translate->translate("ACL_VALIDATION_CODE_INVALID") ) );
				                $this->_helper->redirector( "resetpassword", "account", "acl" );
				            }
				        }
				        
				    } else {
				        throw new Exception("");
				    }
				    
				}
			}
        } catch (Exception $e) {
            #echo $e->getMessage();
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $translate->translate("ACL_ERROR_ON_RESET_PASSWORD") ) );
            $this->_helper->redirector( "resetpassword", "account", "acl" );
        }
        return;
    }


}

