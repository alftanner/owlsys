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
        // action body
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
            $roleId = $this->getRequest()->getParam( 'role', 0);
            $mdlRole = new Acl_Model_Role();
            $role = $mdlRole->find( $roleId );
            if ( ! $role ) {
            	throw new Exception($translate->translate("LBL_ROW_NOT_FOUND"));
            }
             
            $select = $mdlRole->select()->order('priority DESC')->limit(1);
            $childRole = $role->findDependentRowset('Acl_Model_Role', null, $select)->current();
            
            $frmManageResource = new Acl_Form_ManageResources();
            $action = $this->_request->getBaseUrl() . "/acl/permission/update";
            $frmManageResource->setAction($action);
            $frmManageResource->getElement('id')->setValue($role->id);
             
            $mdlResource = new Acl_Model_Resource();
            $mdlPermission = new Acl_Model_Permission();
            $modules = $mdlResource->getModules();
            
            $zfelements = array();
            $resourceDataIds = array();
            $order = 1;
            foreach ( $modules as $module ): 
                $resources = $mdlResource->getByModule($module);
            	if ( !array_key_exists( strtolower($module->module), $zfelements) ) {
            	    $zfelements[strtolower($module->module)] = array();
            	}
            	
                foreach ( $resources as $resource ) :
                    $resourceDataIds[] = $resource->id;
                    $cbResource = new Zend_Form_Element_Select( "cb_res_".$resource->id );
                    $zfelements[strtolower($module->module)][] = "cb_res_".$resource->id;
                    
                    $lblResource = $resource->controller.' / '.$resource->actioncontroller;
                    $cbResource->setLabel( $lblResource );
                    
                    $childPrivilege = ($childRole) ? $mdlPermission->getByResource($resource, $childRole) : null;
                    $rolePrivilege = $mdlPermission->getByResource($resource, $role);
                    
                    #var_dump($childPrivilege, $rolePrivilege, $lblResource);
                    /*if ( strcasecmp($resource->controller, 'tag') == 0 && strcasecmp($resource->actioncontroller, 'list') == 0 ){
                        Zend_Debug::dump( $childPrivilege, 'child privilege' );
                        Zend_Debug::dump( $rolePrivilege, 'role privilege' );
                    }*/
                    
                    if ( ( $childPrivilege == null && $rolePrivilege == null ) )
                    {
                        $cbResource->addMultiOption( 'deny', $translate->translate("ACL_DENIED_DEFAULT") );
                        $cbResource->addMultiOption( 'allow', $translate->translate("ACL_ALLOW") );
                    } elseif ( 
                    	isset($rolePrivilege->privilege) &&
                    	strcasecmp($rolePrivilege->privilege, 'allow') == 0 
                    ) {
                        $cbResource->addMultiOption( 'allow', $translate->translate("ACL_ALLOW") );
                        $cbResource->addMultiOption( 'deny', $translate->translate("ACL_DENY") );
                    } elseif (
                    	isset($rolePrivilege->privilege) &&
                    	strcasecmp($rolePrivilege->privilege, 'deny') == 0  
                    ) {
                        $cbResource->addMultiOption( 'deny', $translate->translate("ACL_DENY") );
                        $cbResource->addMultiOption( 'allow', $translate->translate("ACL_ALLOW") );
                    } elseif (
                    	( $childPrivilege && strcasecmp($childPrivilege->privilege, 'allow') == 0 && !$rolePrivilege ) 
                    ) {
                        $cbResource->addMultiOption( 'allow', sprintf( $translate->translate("ACL_ALLOWED_INHERITED_FROM"), $privilege->name ) );
						$cbResource->addMultiOption( 'deny', $translate->translate("ACL_DENY") );
                    } elseif (
                    	( $childPrivilege && strcasecmp($childPrivilege->privilege, 'deny') == 0 && !$rolePrivilege )
                    ) {
                        $cbResource->addMultiOption( 'deny', sprintf( $translate->translate("ACL_DENIED_INHERITED_FROM"), $privilege->name ) );
                        $cbResource->addMultiOption( 'allow', $translate->translate("ACL_ALLOW") );
                    } 
                    
					$cbResource->setOrder($order);
					$frmManageResource->addElement( $cbResource );
					$order++;
				endforeach;

			endforeach;
			
			#$frmManageResource->getMessages()
			
            $resourceDataIds = implode(',', $resourceDataIds);
            $hrs = new Zend_Session_Namespace('resourceDataIds');
            $hrs->hrs = $resourceDataIds;
            #$frmManageResource->getElement('hrs')->setValue( $resourceDataIds );
            $this->view->modules = $modules;
            $this->view->zfelements = $zfelements;
            
            $this->view->role = $role;
            $this->view->formResources = $frmManageResource;
            
            $fields = array();
            foreach ( $frmManageResource->getElements() as $element ) $fields[] = $element->getName();
            $frmManageResource->addDisplayGroup( $fields, 'form', array( 'legend' => "ACL_UPDATE_ROLE", ) );
        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
            $this->_helper->redirector( "list", "role", "acl" );
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
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$roleId = $this->getRequest()->getParam( 'id', 0);
	        $mdlRole = new Acl_Model_Role();
	        $role = $mdlRole->find( $roleId );
	        if ( ! $role ) throw new Exception($translate->translate("LBL_ROW_NOT_FOUND"));
	        
	        #Zend_Debug::dump( $this->getRequest()->getParams() );
	        $mdlPermission = new Acl_Model_Permission();
	        #$resources = $this->getRequest()->getParam( 'hrs');
	        $hrs = new Zend_Session_Namespace('resourceDataIds');
	        $resources = $hrs->hrs;
	        zend_session::namespaceUnset('resourceDataIds');
	        
	        $arrResources = explode(',', $resources);
	        #$mdlPermission->deleteByRole($role);
	        $permissions = $role->findDependentRowset('Acl_Model_Permission', 'Role');
	        foreach ( $permissions as $perm ) 
	        {
	            #$perm = $mdlPermission->find()->current();
	            $perm->delete();
	        }
	        foreach ($arrResources as $resourceId) {
	        	#echo $this->getRequest()->getParam('cb_res_'.$resourceId, 'deny')."<br>";
	        	$permission = $mdlPermission->createRow();
	        	$permission->role_id = $role->id;
	        	$permission->resource_id = $resourceId;
	        	$permission->privilege = $this->getRequest()->getParam('cb_res_'.$resourceId, 'deny');
	        	$permission->save();
	        }
	        
	        /* @var $cache Zend_Cache_Backend_File */
	        $cache = Zend_Registry::get('cacheACL');
	        $mdlRole = new Acl_Model_Role();
	        $roles = $mdlRole->getList();
	        foreach( $roles as $role ) {
	            if ( $cache->test('cacheACL_'.$role->id) ) {
	                $cache->remove('cacheACL_'.$role->id);
	            }
	        }
	        
	        $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_CHANGES_SAVED") ) );
	       	//$this->_helper->redirector( "manage", "permission", "acl", array('role'=>$role->id) );
	        $this->_helper->redirector( "list", "role", "acl" );
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "list", "role", "acl" );
        }
        return null;
    }


}





