<?php
/**
 * Copyright 2012 Roger CastaÃ±eda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package OS\Application\Plugins
 * @author roger castaÃ±eda <rogercastanedag@gmail.com>
 * @version 1
 */
class OS_Application_Plugins_Acl extends Zend_Controller_Plugin_Abstract
{
	
    /**
     * PreDispatch method for ACL Plugin. It checks if current user has privileges for resources requested 
     * @see Zend_Controller_Plugin_Abstract::preDispatch()
     * @param Zend_Controller_Request_Abstract $request 
     */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		try {
			// set up acl
			$acl = new Zend_Acl();
			// fetch the current user
			$auth = Zend_Auth::getInstance();
			
			$mdlRole = new Acl_Model_Role();
			$objRole = $mdlRole->createRow();
			
			if ( $auth->hasIdentity() ) {
				$identity = $auth->getIdentity();
				$objRole->id = $identity->role_id;
			} else {
				$objRole->id = 3; # guess
			}
			
			$acl->addRole(new Zend_Acl_Role($objRole->id));
			
			$mdlRole = new Acl_Model_Role();
			$mdlResource = new Acl_Model_Resource();
	        $mdlPermission = new Acl_Model_Permission();
	        
	        $role = $mdlRole->find( $objRole->id )->current();
	        if ( ! $role ) throw new Zend_Exception( 'Role not found' );
	        
	        $select = $mdlRole->select()->order('priority DESC')->limit(1);
	        $childRole = $role->findDependentRowset('Acl_Model_Role', null, $select)->current();
	        
	        $resources = $mdlResource->getRegisteredList();
	        #if ( !$resources ) throw new Zend_Exception('Resources not available');
	        if ( $resources->count() > 0 ) {
	        	foreach ( $resources as $resource ) {
	        		$resourceTemp = strtolower($resource->module.':'.$resource->controller);
	        		if ( !$acl->has( new Zend_Acl_Resource($resourceTemp) ) ) {
	        			$acl->addResource( new Zend_Acl_Resource($resourceTemp) );
	        		}
	        	}
	        } else {
        		throw new Zend_Exception('Resources not available');
        	}
        	
	        if ( $resources->count() > 0 ) 
	        {
	        	foreach ( $resources as $resource ) 
	        	{
	        	    $resourceTemp = strtolower($resource->module.':'.$resource->controller);
	        	    $childPrivilege = ($childRole) ? $mdlPermission->getByResource($resource, $childRole) : null;
	        	    $rolePrivilege = $mdlPermission->getByResource($resource, $role);
	        	    
	        	    if ( $objRole->id < 2  ) {
	        	        $acl->allow( $objRole->id, $resourceTemp, $resource->actioncontroller );
	        	    } elseif ( 
	        	    	( !$childRole && !$rolePrivilege ) ||
	        	     	( strcasecmp($rolePrivilege->privilege, 'deny') == 0 ) || 
	        	    	( $childPrivilege && strcasecmp($childPrivilege->privilege, 'deny') == 0 && !$rolePrivilege )
	        	    ) {
	        	        $acl->deny( $objRole->id, $resourceTemp, $resource->actioncontroller );
	        	    } elseif (
	        	    	( strcasecmp($rolePrivilege->privilege, 'allow') == 0 ) || 
	        	    	( $childPrivilege && strcasecmp($childPrivilege->privilege, 'allow') == 0 && !$rolePrivilege )
	        	    ) {
	        	    	$acl->allow( $objRole->id, $resourceTemp, $resource->actioncontroller );
	        	    } 
	        	} # foreach ( $resources as $resource ) 
	        } # if ( $resources->count() > 0 ) 
	        
	        $module = strtolower($this->_request->getParam('module'));
			$controller = strtolower($this->_request->getParam('controller'));
			$action 	= strtolower($this->_request->getParam('action'));
			$resource	= $module.":".$controller;
			
			Zend_Registry::set('ZendACL', $acl);
			/*
			Zend_Debug::dump( $resource );
			Zend_Debug::dump( $action );
			Zend_Debug::dump( $role->id );
			Zend_Debug::dump( $acl->isAllowed( $objRole->id, $resource, $action) );
			*/
			#die();
			$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
			if ( ! $acl->isAllowed( $objRole->id, $resource, $action) )
			{
			    $redirector->gotoUrl('acl/authentication/logout')->redirectAndExit();
			} else {
				return;
				#$redirector->gotoUrl('default/index/index')->redirectAndExit();
			}
			
			
		} catch (Exception $e) {
		    trigger_error( $e->__toString() );
		}
		
	}
	
}