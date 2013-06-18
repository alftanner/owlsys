<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package acl
 * @subpackage controller
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Acl_AuthenticationController extends Zend_Controller_Action
{

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        /* Initialize action controller here */
    }

    /**
     * Index action for authentication controller
     */
    public function indexAction()
    {
        // action body
    }

    /**
     * Login action for authentication controller
     */
    public function loginAction()
    {
    	$translate = Zend_Registry::get('Zend_Translate');
        try {
            $auth = Zend_Auth::getInstance();
            $identity = $auth->getIdentity();
            $role = $auth->hasIdentity() ? $auth->getIdentity()->role_id : 3;
            
        	$frmLogin = new Acl_Form_Login();
        	$this->view->identity = ($role != 3) ? $identity : null;
        	
        	$frmLogin->setAction( $this->_request->getBaseUrl() . '/login' );
        	$this->view->frmLogin = $frmLogin;
	        if ( $this->getRequest()->isPost() )
			{
				if ( $frmLogin->isValid( $this->getRequest()->getParams() ) ) {
					$mdlAccountMapper = Acl_Model_AccountMapper::getInstance();
					$account = new Acl_Model_Account();
					$account->setEmail( $frmLogin->getValue('email') );
					$account->setPassword( $frmLogin->getValue('password') );
					
					if ( $mdlAccountMapper->login($account) ) {
					    $role_id = $auth->getInstance()->getIdentity()->role_id;
					    // custom redirector here
					    if ( $role < 3 ) {
					        // is root or super administrator
					    }
					} else {
						throw new Exception( $translate->translate("ACL_ACCESS_DENIED") );
					}
				} else {
				    // invalid form data
				}
			} 
			
			$fields = array();
			foreach ( $frmLogin->getElements() as $element ) $fields[] = $element->getName();
			$frmLogin->addDisplayGroup( $fields, 'form', array( 'legend' => "ACL_LOGIN", ) );
			
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->redirect('login');
        }
    }

    /**
     * Logout action for authentication controller
     */
    public function logoutAction()
    {
        try {
            Zend_Auth::getInstance()->clearIdentity();
            $this->redirect('login');
        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
            $this->redirect('login');
        }
    }

}

