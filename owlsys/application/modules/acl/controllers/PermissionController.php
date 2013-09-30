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
class Acl_PermissionController extends Zend_Controller_Action
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
     * Index action for permission controller
     */
    public function indexAction()
    {
    }
    
    /**
     * Manage action for Permission controller
     * @throws Zend_Exception
     * @return NULL
     */
    public function manageAction() 
    {
        try {
            $translate = Zend_Registry::get('Zend_Translate');
            $roleId = $this->getRequest()->getParam('role', 0);
            $mdlRole = new Acl_Model_Role();
            $role = $mdlRole->find($roleId)->current();
             
            $frmManageResource = new Acl_Form_ManageResources();
            $action = $this->_request->getBaseUrl() . "/permissions-update/".$role->id;
            $frmManageResource->setAction($action);
            $frmManageResource->getElement('id')->setValue($role->id);
            
            $mdlPermission = new Acl_Model_Permission();
            $privileges = $mdlPermission->getResourcesByRole($role);
            
            $zfelements = array();
            $resourceIds = array();
            $modules = array();
            $order = 1;
            
            foreach ( $privileges as $permission ) {
                $resourceIds[] = $permission->resource_id;
                if ( !in_array($permission->module, $modules) ) {
                    $modules[] = strtolower($permission->module);
                }
                
                $cbResource = new Zend_Form_Element_Select( "cb_res_".$permission->resource_id );
                $zfelements[strtolower($permission->module)][] = "cb_res_".$permission->resource_id;
                $lblResource = $permission->controller.' / '.$permission->actioncontroller;
                $cbResource->setLabel( $lblResource );
                
                $cbResource->addMultiOption( 0, $translate->translate("Deny") );
                $cbResource->addMultiOption( 1, $translate->translate("Allow") );
                
                $cbResource->setOrder($order);
                $frmManageResource->addElement( $cbResource );
                $cbResource->setValue($permission->isAllowed);
                $order++;
            }
            
			
            $resourceDataIds = implode(',', $resourceIds);
            $hrs = new Zend_Session_Namespace('resourceDataIds');
            $hrs->hrs = $resourceDataIds;
            $this->view->modules = $modules;
            $this->view->zfelements = $zfelements;
            
            $this->view->role = $role;
            $this->view->formResources = $frmManageResource;
            
            $fields = array();
            foreach ( $frmManageResource->getElements() as $element ) $fields[] = $element->getName();
            $frmManageResource->addDisplayGroup( $fields, 'form', array( 'legend' => "Update", ) );
        } catch (Exception $e) {
//             Zend_Debug::dump($e->getMessage());
//             Zend_Debug::dump($e->getTraceAsString());
//             die();
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
            $this->redirect('roles');
        }
        return null;
    }

    /**
     * Update action for permission controller
     * @throws Zend_Exception
     * @return NULL
     */
    public function updateAction()
    {
        $translate = Zend_Registry::get('Zend_Translate');
        $mdlPermission = new Acl_Model_Permission();
        $mdlRole = new Acl_Model_Role();
        $adapter = $mdlPermission->getAdapter();
        try {
        	
        	$roleId = $this->getRequest()->getParam('id', 0);
	        $role = $mdlRole->find( $roleId )->current();
	        
	        $mdlPermission = new Acl_Model_Permission();
	        $hrs = new Zend_Session_Namespace('resourceDataIds');
	        $resources = $hrs->hrs; // Hidden ReSources hrs
	        zend_session::namespaceUnset('resourceDataIds');
	        
	        $arrResources = explode(',', $resources);
	        
	        $adapter->beginTransaction();
	        $mdlPermission->deleteByRole($role);
	        
	        foreach ($arrResources as $resourceId) {
	          $permission = $mdlPermission->createRow();
	          $permission->isAllowed = $this->getRequest()->getParam('cb_res_'.$resourceId, 0) ;
	          $permission->resource_id = $resourceId;
	          $permission->role_id = $role->id;
              $permission->save();
	        }
	        
	        $adapter->commit();
	        /* @var $cache Zend_Cache_Backend_File */
	        $cache = Zend_Registry::get('cacheACL');
            if ( $cache->test('cacheACL_'.$role->id) ) {
                $cache->remove('cacheACL_'.$role->id);
            }
            $cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('resource','permissions'));
	        
	        $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("Changes saved") ) );
	        $this->redirect('roles');
        } catch (Exception $e) {
            $adapter->rollBack();
            Zend_Debug::dump($e->getMessage());
            Zend_Debug::dump($e->getTraceAsString());
            die();
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->redirect('roles');
        }
    }


}





