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
	    
	    $frontController = Zend_Controller_Front::getInstance();
	    $router = $frontController->getRouter();
	    $options = array();
	    $options['module'] = 'acl';
	    
// account controller
	    $options['controller'] = 'account';
	    $options['action'] = 'resetpassword';
	    $options['change'] = 0;
	    $options['page'] = 1;
	    $route = new Zend_Controller_Router_Route( 'resetpassword', $options );
	    $router->addRoute('resetpassword', $route);
	    
	    $options['action'] = 'resetpassword';
	    $options['change'] = 1;
	    $route = new Zend_Controller_Router_Route( 'changepassword', $options );
	    $router->addRoute('changepassword', $route);
	    
	    $options['action'] = 'list';
	    $route = new Zend_Controller_Router_Route( 'accounts', $options );
	    $router->addRoute('accounts', $route);
	    
	    $route = new Zend_Controller_Router_Route( 'accounts/page/:page', $options );
	    $router->addRoute('accounts/page/:page', $route);
	    
	    $options['action'] = 'create';
	    $route = new Zend_Controller_Router_Route( 'account-new', $options );
	    $router->addRoute('account-new', $route);
	    
	    $options['action'] = 'edit';
	    $options['id'] = null;
	    $route = new Zend_Controller_Router_Route( 'account-edit/:id', $options );
	    $router->addRoute('account-edit/:id', $route);
	    
	    $options['action'] = 'update';
	    $route = new Zend_Controller_Router_Route( 'account-update/:id', $options );
	    $router->addRoute('account-update/:id', $route);
	    
	    $options['action'] = 'block';
	    $route = new Zend_Controller_Router_Route( 'account-block/:id', $options );
	    $router->addRoute('account-block/:id', $route);
	    
	    $options['action'] = 'delete';
	    $route = new Zend_Controller_Router_Route( 'account-delete/:id', $options );
	    $router->addRoute('account-delete/:id', $route);

// authentication controller
	    $options['controller'] = 'authentication';
	    $options['action'] = 'login';
	    $route = new Zend_Controller_Router_Route( 'login', $options );
	    $router->addRoute('login', $route);
	    
	    $options['action'] = 'logout';
	    $route = new Zend_Controller_Router_Route( 'logout', $options );
	    $router->addRoute('logout', $route);
	    
// resource controller
	    $options['controller'] = 'resource';
	    $options['action'] = 'list';
	    $route = new Zend_Controller_Router_Route( 'resources', $options );
	    $router->addRoute('resources', $route);
	    
	    $route = new Zend_Controller_Router_Route( 'resources/page/:page', $options );
	    $router->addRoute('resources/page/:page', $route);
	    
	    $options['action'] = 'sync';
	    $route = new Zend_Controller_Router_Route( 'resources-sync', $options );
	    $router->addRoute('resources-sync', $route);
	    
	    $options['action'] = 'delete';
	    $route = new Zend_Controller_Router_Route( 'resource-delete/:id', $options );
	    $router->addRoute('resource-delete/:id', $route);
	    
// role controller
	    $options['controller'] = 'role';
	    $options['action'] = 'list';
	    $route = new Zend_Controller_Router_Route( 'roles', $options );
	    $router->addRoute('roles', $route);
	    
	    $route = new Zend_Controller_Router_Route( 'roles/page/:page', $options );
	    $router->addRoute('roles/page/:page', $route);
	    
	    $options['action'] = 'create';
	    $route = new Zend_Controller_Router_Route( 'role-create', $options );
	    $router->addRoute('role-create', $route);
	    
	    $options['action'] = 'update';
	    $route = new Zend_Controller_Router_Route( 'role-update/:id', $options );
	    $router->addRoute('role-update/:id', $route);

	    $options['action'] = 'delete';
	    $route = new Zend_Controller_Router_Route( 'role-delete/:id', $options );
	    $router->addRoute('role-delete/:id', $route);
	    
// permission controller
	    $options['controller'] = 'permission';
	    $options['action'] = 'manage';
	    $options['role'] = 0;
	    $route = new Zend_Controller_Router_Route( 'permissions-manager/:role', $options );
	    $router->addRoute('permissions-manager/:role', $route);
	    
	    $options['action'] = 'update';
	    $route = new Zend_Controller_Router_Route( 'permissions-update/:id', $options );
	    $router->addRoute('permissions-update/:id', $route);
	}
	
}