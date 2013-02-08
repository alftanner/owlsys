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
class Acl_RoleController extends Zend_Controller_Action
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
     * Index action for role controller
     */
    public function indexAction()
    {
        // action body
    }

    /**
     * Create action for role controller
     */
    public function createAction()
    {
    	$translate = Zend_Registry::get('Zend_Translate');
        try {
        	$frmRole = new Acl_Form_Role();
        	$action = $this->_request->getBaseUrl() . "/acl/role/create";
        	$frmRole->setAction($action);
        	$this->view->frmRole = $frmRole;
	    	if ( $this->getRequest()->isPost() )
			{
				if ( $frmRole->isValid( $this->getRequest()->getParams() ) )
				{
					$childRoles = $frmRole->getValue("childrole_id" );
					$mdlRole = new Acl_Model_Role();
					$role = $mdlRole->createRow( );
					$role->name = $frmRole->getValue("name" );
					$role->parent_id = $frmRole->getValue("parent_id" );
					$role->priority = $frmRole->getValue("priority" );
					$role->save();
					$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_CHANGES_SAVED") ) );
					$this->_helper->redirector( "list", "role", "acl" );
				}
			} else {
				/*$fields = array();
				foreach ( $frmRole->getElements() as $element ) $fields[] = $element->getName();
				$frmRole->addDisplayGroup( $fields, 'form', array( 'legend' => "ACL_CREATE_ROLE", ) );*/
			}
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	return $this->_helper->redirector( "create", "role", "acl" );
        }
        return;
    }

    /**
     * Update action for role controller
     * @throws Zend_Exception
     */
    public function updateAction()
    {
    	$translate = Zend_Registry::get('Zend_Translate');
        try {
        	$frmRole = new Acl_Form_Role();
	        $action = $this->_request->getBaseUrl() . "/acl/role/update";
	        $frmRole->setAction($action);
	        
	        $mdlRole = new Acl_Model_Role();
	        $id = $this->getRequest()->getParam( "id", 0 );
	        $role = $mdlRole->find( $id )->current();
	        if ( !$role ) throw new Zend_Exception($translate->translate("LBL_ROW_NOT_FOUND"));
	        #Zend_Debug::dump( $role->findParentRow('Acl_Model_Role') );
	        #Zend_Debug::dump( $role->findDependentRowset('Acl_Model_Role') );
	        #$rows = $role->findManyToManyRowset('Acl_Model_Role', 'Acl_Model_Roleparent', 'RoleParent', 'Role', null);
	        #Zend_Debug::dump($rows->toArray());
	        if ( $this->getRequest()->isPost() )
			{
				if ( $frmRole->isValid( $this->getRequest()->getParams() ) )
				{
					$role->setFromArray( $frmRole->getValues() );
					$role->name = $this->getRequest()->getParam( "name" );
					$role->parent_id = $this->getRequest()->getParam( "parent_id" );
					$role->priority = $this->getRequest()->getParam("priority" );
					$role->save();
					$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_CHANGES_SAVED") ) );
					return $this->_helper->redirector( "list", "role", "acl" );
				}
			} else {
				$rowSelected = array();
				$frmRole->populate( $role->toArray() );
				
				/*$fields = array();
				foreach ( $frmRole->getElements() as $element ) $fields[] = $element->getName();
				$frmRole->addDisplayGroup( $fields, 'form', array( 'legend' => $translate->translate("ACL_UPDATE_ROLE"), ) );*/
			}
	        $this->view->frmRole = $frmRole;
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	return $this->_helper->redirector( "list", "role", "acl" );
        }
        return;
    }

    /**
     * List action for role controller
     */
    public function listAction()
    {
        try {
        	$mdlRole = new Acl_Model_Role();
        	$adapter = $mdlRole->getList();
        	$paginator = Zend_Paginator::factory($adapter);
        	$paginator->setItemCountPerPage(10);
        	$pageNumber = $this->getRequest()->getParam('page',1);
        	$paginator->setCurrentPageNumber($pageNumber);
        	$this->view->roles = $paginator;
        } catch (Exception $e) {
        	echo $e->getMessage();
        }
        return;
    }

    /**
     * Delete action for role controller
     * @throws Zend_Exception
     */
    public function deleteAction()
    {
    	$translate = Zend_Registry::get('Zend_Translate');
        try {
	        $id = $this->getRequest()->getParam( 'id' );
	        if ( $id < 4 ) throw new Zend_Exception( $translate->translate( "ACL_DEFAULT_ROLE_COULD_NOT_BE_DROPPED" ) ); 
	        $mdlRole = new Acl_Model_Role();
	        $role = $mdlRole->find( $id )->current();
	        if ( !$role ) {
	        	throw new Zend_Exception($translate->translate("LBL_ROW_NOT_FOUND"));
	        }
	        $role->delete();
	        $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_CHANGES_SAVED") ) );
	        return $this->_helper->redirector( "list", "role", "acl" );
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	return $this->_helper->redirector( "list", "role", "acl" );
        }
        return;
    }


}

