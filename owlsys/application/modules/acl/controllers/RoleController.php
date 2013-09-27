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
        	$action = $this->_request->getBaseUrl() . "/role-create";
        	$frmRole->setAction($action);
        	$frmRole->removeElement('id');
        	$this->view->frmRole = $frmRole;
	    	if ( $this->getRequest()->isPost() )
			{
				if ( $frmRole->isValid( $this->getRequest()->getParams() ) )
				{
					$mdlRole = new Acl_Model_Role();
					$role = $mdlRole->createRow( $frmRole->getValues() );
					$role->save();
					
					/* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
					$cache = Zend_Registry::get('cache');
					$cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('role'));
					
					$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("LBL_CHANGES_SAVED") ) );
					$this->redirect('roles');
				}
			} 
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('roles');
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
	        $mdlRole = new Acl_Model_Role();
	        $id = $this->getRequest()->getParam( "id", 0 );
	        $role = $mdlRole->find($id)->current();
	        
	        $frmRole = new Acl_Form_Role();
	        $action = $this->_request->getBaseUrl() . "/role-update/".$role->id;
	        $frmRole->setAction($action);
	        
	        if ( $this->getRequest()->isPost() )
			{
				if ( $frmRole->isValid( $this->getRequest()->getParams() ) )
				{
					$role->setFromArray( $frmRole->getValues() );
					$role->save();
					
					/* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
					$cache = Zend_Registry::get('cache');
					$cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('role'));
					
					$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("The role was updated") ) );
					$this->redirect('roles');
				}
			} else {
				$frmRole->populate( $role->toArray() );
			}
	        $this->view->frmRole = $frmRole;
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('roles');
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
        	$paginator = Zend_Paginator::factory($mdlRole->getList());
        	$paginator->setItemCountPerPage(10);
        	$pageNumber = $this->getRequest()->getParam('page',1);
        	$paginator->setCurrentPageNumber($pageNumber);
        	$paginator->setCacheEnabled(true);
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
	        $mdlRole = new Acl_Model_Role();
	        $id = $this->getRequest()->getParam( "id", 0 );
	        $role = $mdlRole->find($id)->current();
	        $role->delete();
	        
	        /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
	        $cache = Zend_Registry::get('cache');
	        $cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('role'));
	        
	        $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("The role was deleted") ) );
	        $this->redirect('roles');
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('roles');
        }
        return;
    }


}

