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
class OS_Plugins_Acl extends Zend_Controller_Plugin_Abstract
{
	
    /**
     * PreDispatch method for ACL Plugin. It checks if current user has privileges for resources requested 
     * @see Zend_Controller_Plugin_Abstract::preDispatch()
     * @param Zend_Controller_Request_Abstract $request 
     */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		try {
		    
		    /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
		    $cache = Zend_Registry::get('cache');
		    
		    $roleId = 3;
		    
		    // fetch the current user
		    $auth = Zend_Auth::getInstance();
		    if ( $auth->hasIdentity() ) {
		        $identity = $auth->getIdentity();
		        $roleId = $identity->role_id;
		        // get an instance of Zend_Session_Namespace used by Zend_Auth
                #$authns = new Zend_Session_Namespace($auth->getStorage()->getNamespace());
                // set an expiration on the Zend_Auth namespace where identity is held
                #$authns->setExpirationSeconds(60 * 30);  // expire auth storage after 30 min 
		    }
		    
		    $cacheACL = false;
		    $cacheId = 'cacheACL_'.$roleId;
		    if ( $cache->test($cacheId) ) {
		        $cacheACL = $cache->load($cacheId);
		    }
		    
		    if ( $cacheACL == false ) {
		    
    			// set up acl
    			$acl = new Zend_Acl();
    			$mdlRole = new Acl_Model_Role();
    			$mdlResource = new Acl_Model_Resource();
    			$mdlPermission = new Acl_Model_Permission();
    			
    			$acl->addRole(new Zend_Acl_Role($roleId));
    			
    			$role = $mdlRole->findRow($roleId)->current();
    	        // getting resources
    	        $resources = $mdlResource->getAll();
    	        // getting permissions
    	        $allowedResources = $mdlPermission->getAllowedByRole($role);
    	        $allowedResourcesTemp = array();
    	        foreach ( $allowedResources as $ar ) {
    	          $allowedResourcesTemp[] = $ar->resource_id;
    	        }
    	        $allowedResources = $allowedResourcesTemp;
    	        // add resources
	        	foreach ( $resources as $resource ) {
	        		$resourceName = strtolower($resource->module.':'.$resource->controller);
	        		if ( !$acl->has( new Zend_Acl_Resource($resourceName) ) ) {
	        			$acl->addResource( new Zend_Acl_Resource($resourceName) );
	        		}
	        		if ( $role->id == 1 ) {
	        		    $acl->allow($role->id, $resourceName, $resource->actioncontroller);
	        		} else {
    	        		if ( in_array($resource->id, $allowedResources) ) {
    	        		    $acl->allow($role->id, $resourceName, $resource->actioncontroller);
    	        		} else {
    	        		    $acl->deny($role->id, $resourceName, $resource->actioncontroller);
    	        		}
	        		}
	        	}
	        	
    	        $cache->save($acl, 'cacheACL_'.$role->id);
    	        Zend_Registry::set('ZendACL', $acl);
		    } else {
		        Zend_Registry::set('ZendACL', $cacheACL);
		    }
		    Zend_Registry::set('cacheACL', $cache);
		    
		} catch (Exception $e) {
		    Zend_Debug::dump($e->getMessage());
		    Zend_Debug::dump($e->getTraceAsString());
		    die();
		    try {
		        $writer = new Zend_Log_Writer_Stream(APPLICATION_LOG_PATH . 'plugins.log');
		        $logger = new Zend_Log($writer);
		        $logger->log($e->getMessage(), Zend_Log::ERR);
		    } catch (Exception $e) {
		    }
		}
		
	}
	
}