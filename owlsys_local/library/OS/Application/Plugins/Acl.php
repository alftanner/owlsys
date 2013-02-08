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
		    
		    $frontendOptions = array('lifetime'=>43200, 'automatic_serialization'=>true);
		    $backendOptions = array('cache_dir'=> APPLICATION_CACHE_PATH );
		    $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
		    
		    // fetch the current user
		    $auth = Zend_Auth::getInstance();
		    if ( $auth->hasIdentity() ) {
		        $identity = $auth->getIdentity();
		        $objRole->id = $identity->role_id;
		        // get an instance of Zend_Session_Namespace used by Zend_Auth
                #$authns = new Zend_Session_Namespace($auth->getStorage()->getNamespace());
                // set an expiration on the Zend_Auth namespace where identity is held
                #$authns->setExpirationSeconds(60 * 30);  // expire auth storage after 30 min 
		    } else {
		        $objRole->id = 3; # guess
		    }
		    
		    $cacheACL = false;
		    if ( $cache->load('cacheACL_'.$objRole->id) && $cache->test('cacheACL_'.$objRole->id) )
		        $cacheACL = $cache->load('cacheACL_'.$objRole->id);
		    
		    if ( $cacheACL == false ) {
		    
    			// set up acl
    			$acl = new Zend_Acl();
    			$mdlRole = new Acl_Model_Role();
    			$mdlResource = new Acl_Model_Resource();
    			$mdlPermission = new Acl_Model_Permission();
    			
    			#$role = $mdlRole->createRow();
    			$acl->addRole(new Zend_Acl_Role($objRole->id));
    	        $role = $mdlRole->find( $objRole->id )->current();
    	        
    	        #var_dump($role, $objRole->id);
    	        #die();
    	        
    	        if ( $role == null ) throw new Zend_Exception( 'Role not found' );
    	        
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

    	        $cache->save($acl, 'cacheACL_'.$objRole->id);
    	        Zend_Registry::set('ZendACL', $acl);
		    } else {
		        Zend_Registry::set('ZendACL', $cacheACL);
		    }
		    Zend_Registry::set('cacheACL', $cache);
			
		} catch (Exception $e) {
		    try {
		        $writer = new Zend_Log_Writer_Stream(APPLICATION_LOG_PATH . 'plugins.log');
		        $logger = new Zend_Log($writer);
		        $logger->log($e->getMessage(), Zend_Log::ERR);
		    } catch (Exception $e) {
		    }
		}
		
	}
	
}