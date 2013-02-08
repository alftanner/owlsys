<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package acl
 * @subpackage plugins
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Acl_Plugin_Router extends Zend_Controller_Plugin_Abstract
{
    
	public function routeStartup( Zend_Controller_Request_Abstract $request )
	{
	    $mdlMenuItem = new menu_Model_Item();
	    $menuItems = $mdlMenuItem->getListForRouting();
	    $frontController = Zend_Controller_Front::getInstance();
	    $router = $frontController->getRouter();
	    $options = array();
	    $options['module'] = 'acl';
	    /*($menuItems as $menuItem) {
	        if (strcasecmp($menuItem->controller, "authentication") == 0 &&
	                strcasecmp($menuItem->actioncontroller, "login") == 0) {
	            $params = Zend_Json::decode($menuItem->params);
	            $options['controller'] = 'authentication';
	            $options['action'] = 'login';
	            $options['mid'] = $menuItem->id;
	            $route = new Zend_Controller_Router_Route( $menuItem->id_alias, $options );
	            $router->addRoute($menuItem->id_alias, $route);
	        }
	    }*/
	    $options = array();
	    $options['module'] = 'acl';
	    $options['controller'] = 'account';
	    $options['action'] = 'resetpassword';
	    $options['change'] = 0;
	    $route = new Zend_Controller_Router_Route( 'resetpassword', $options );
	    $router->addRoute('resetpassword', $route);
	    
	    $options['action'] = 'resetpassword';
	    $options['change'] = 1;
	    $route = new Zend_Controller_Router_Route( 'changepassword', $options );
	    $router->addRoute('changepassword', $route);
	    
	    $options['action'] = 'edit';
	    $route = new Zend_Controller_Router_Route( 'edit-account', $options );
	    $router->addRoute('edit-account', $route);
	    
	    $options['action'] = 'update';
	    $route = new Zend_Controller_Router_Route( 'update-account', $options );
	    $router->addRoute('update-account', $route);

	    $options['controller'] = 'authentication';
	    $options['action'] = 'login';
	    $route = new Zend_Controller_Router_Route( 'login', $options );
	    $router->addRoute('login', $route);
	    
	    $options['controller'] = 'authentication';
	    $options['action'] = 'logout';
	    $route = new Zend_Controller_Router_Route( 'logout', $options );
	    $router->addRoute('logout', $route);
	    
	}
	
}