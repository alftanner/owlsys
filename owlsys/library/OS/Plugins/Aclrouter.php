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
class OS_Plugins_Aclrouter extends Zend_Controller_Plugin_Abstract
{
	
    /**
     * PreDispatch method for ACL Plugin. It checks if current user has privileges for resources requested 
     * @see Zend_Controller_Plugin_Abstract::preDispatch()
     * @param Zend_Controller_Request_Abstract $request 
     */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		try {
	        
	        $module = strtolower($request->getParam('module'));
			$controller = strtolower($request->getParam('controller'));
			$action 	= strtolower($request->getParam('action'));
			$resource	= $module.":".$controller;
			
			$acl = Zend_Registry::get('ZendACL');
			
			$auth = Zend_Auth::getInstance();
			$identity = $auth->getIdentity();
			$roleID = $auth->hasIdentity() ? $auth->getIdentity()->role_id : 3;
			
// 			var_dump($acl->isAllowed( $roleID, $resource, $action), $roleID, $resource, $action, $module, $controller, $action);
// 			Zend_Debug::dump($module, $controller, $action, $roleID, $acl);
// 			die();
			
			$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
			if ( $acl->isAllowed( $roleID, $resource, $action) != true )
			{
			    $redirector->gotoUrl('logout')->redirectAndExit();
			} else {
				return;
			}
			
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