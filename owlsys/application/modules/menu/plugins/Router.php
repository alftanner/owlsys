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
class menu_Plugin_Router extends Zend_Controller_Plugin_Abstract
{
    
	public function routeStartup( Zend_Controller_Request_Abstract $request )
	{
		$frontController = Zend_Controller_Front::getInstance();
		$router = $frontController->getRouter();
		$options = array();
// menu
		$options['module'] = 'menu';
		$options['controller'] = 'menu';
		$options['page'] = 1;
		
		$options['action'] = 'list';
		$route = new Zend_Controller_Router_Route( 'menus', $options );
		$router->addRoute('menus', $route);
		
		$options['action'] = 'create';
		$route = new Zend_Controller_Router_Route( 'menu-create', $options );
		$router->addRoute('menu-create', $route);
		
		$options['action'] = 'update';
		$options['id'] = null;
		$route = new Zend_Controller_Router_Route( 'menu-update/:id', $options );
		$router->addRoute('menu-update/:id', $route);
		
		$options['action'] = 'publish';
		$route = new Zend_Controller_Router_Route( 'menu-publish/:id', $options );
		$router->addRoute('menu-publish/:id', $route);
		
		$options['action'] = 'delete';
		$route = new Zend_Controller_Router_Route( 'menu-delete/:id', $options );
		$router->addRoute('menu-delete/:id', $route);
// item
		$options['controller'] = 'item';
		$options['action'] = 'list';
		$options['menu'] = 0;
		$route = new Zend_Controller_Router_Route( 'menu-items/:menu', $options );
		$router->addRoute('menu-items/:menu', $route);
		
		$route = new Zend_Controller_Router_Route( 'menu-items/page/:page/:menu', $options );
		$router->addRoute('menu-items/page/:page/:menu', $route);
		
		$options['action'] = 'add';
		$options['mod'] = null;
		$options['mid'] = 0;
		$route = new Zend_Controller_Router_Route( 'menu-item-add/:mod/:mid/:menu', $options );
		$router->addRoute('menu-item-add/:mod/:mid/:menu', $route);
		
		$options['action'] = 'publish';
		$route = new Zend_Controller_Router_Route( 'menu-item-publish/:id', $options );
		$router->addRoute('menu-item-publish/:id', $route);
		
		$options['action'] = 'update';
		$route = new Zend_Controller_Router_Route( 'menu-item-update/:id', $options );
		$router->addRoute('menu-item-update/:id', $route);
		
		$options['action'] = 'delete';
		$route = new Zend_Controller_Router_Route( 'menu-item-delete/:id', $options );
		$router->addRoute('menu-item-delete/:id', $route);
		
		$options['action'] = 'choose';
		$options['menu'] = 0;
		$route = new Zend_Controller_Router_Route( 'menu-item-choose/:menu', $options );
		$router->addRoute('menu-item-choose/:menu', $route);
		
		$options['action'] = 'move';
		$options['direction'] = 0;
		$route = new Zend_Controller_Router_Route( 'menu-item-move/:direction/:id', $options );
		$router->addRoute('menu-item-move/:direction/:id', $route);
	}
	
}