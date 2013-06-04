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
		    
		    $role = new Acl_Model_Role();
		    
		    // fetch the current user
		    $auth = Zend_Auth::getInstance();
		    if ( $auth->hasIdentity() ) {
		        $identity = $auth->getIdentity();
		        $role->setId($identity->role_id);
		        // get an instance of Zend_Session_Namespace used by Zend_Auth
                #$authns = new Zend_Session_Namespace($auth->getStorage()->getNamespace());
                // set an expiration on the Zend_Auth namespace where identity is held
                #$authns->setExpirationSeconds(60 * 30);  // expire auth storage after 30 min 
		    } else {
		        $role->setId(3); # guess
		    }
		    
		    $cacheACL = false;
		    $cacheId = 'cacheACL_'.$role->getId();
		    if ( $cache->test($cacheId) ) {
		        $cacheACL = $cache->load($cacheId);
		    }
		    
		    if ( $cacheACL == false ) {
		    
    			// set up acl
    			$acl = new Zend_Acl();
    			$mdlRoleMapper = Acl_Model_RoleMapper::getInstance();
    			$mdlResourceMapper = Acl_Model_ResourceMapper::getInstance();
    			$mdlPermissionMapper = Acl_Model_PermissionMapper::getInstance();
    			
    			
    			$acl->addRole(new Zend_Acl_Role($role->getId()));
    			$mdlRoleMapper->find($role->getId(), $role);
    	        // getting resources
    	        $resources = $mdlResourceMapper->getAll();
    	        // getting permissions
    	        $allowedResources = $mdlPermissionMapper->getAllowedByRole($role);
    	        // add resources
	        	foreach ( $resources as $resource ) {
	        		$resourceName = strtolower($resource->getModule().':'.$resource->getController());
	        		if ( !$acl->has( new Zend_Acl_Resource($resourceName) ) ) {
	        			$acl->addResource( new Zend_Acl_Resource($resourceName) );
	        		}
	        		if ( $role->getId() == 1 ) {
	        		    $acl->allow($role->getId(), $resourceName, $resource->getActioncontroller());
	        		} else {
    	        		if ( in_array($resource->getId(), $allowedResources) ) {
    	        		    $acl->allow($role->getId(), $resourceName, $resource->getActioncontroller());
    	        		} else {
    	        		    $acl->deny($role->getId(), $resourceName, $resource->getActioncontroller());
    	        		}
	        		}
	        	}
	        	
    	        $cache->save($acl, 'cacheACL_'.$role->getId());
    	        Zend_Registry::set('ZendACL', $acl);
		    } else {
		        Zend_Registry::set('ZendACL', $cacheACL);
		    }
		    Zend_Registry::set('cacheACL', $cache);
		    
		} catch (Exception $e) {
// 		    Zend_Debug::dump($e->getMessage());
// 		    Zend_Debug::dump($e->getTraceAsString());
// 		    die();
		    try {
		        $writer = new Zend_Log_Writer_Stream(APPLICATION_LOG_PATH . 'plugins.log');
		        $logger = new Zend_Log($writer);
		        $logger->log($e->getMessage(), Zend_Log::ERR);
		    } catch (Exception $e) {
		    }
		}
		
	}
	
}