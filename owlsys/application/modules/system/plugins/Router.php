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
class System_Plugin_Router extends Zend_Controller_Plugin_Abstract
{
    
	public function routeStartup( Zend_Controller_Request_Abstract $request )
	{
		$frontController = Zend_Controller_Front::getInstance();
		$router = $frontController->getRouter();
		$options = array();
		$options['module'] = 'system';
		$options['controller'] = 'widget';
		
		$options['action'] = 'list';
		$route = new Zend_Controller_Router_Route( 'widgets', $options );
		$router->addRoute('widgets', $route);
		
		$options['action'] = 'choose';
		$route = new Zend_Controller_Router_Route( 'widget-choose', $options );
		$router->addRoute('widget-choose', $route);
		
		$options['action'] = 'new';
		$options['mod'] = null;
		$options['wid'] = null;
		$route = new Zend_Controller_Router_Route( 'widget-new/:mod/:wid', $options );
		$router->addRoute('widget-new/:mod/:wid', $route);
		
		$options['action'] = 'update';
		$options['id'] = null;
		$route = new Zend_Controller_Router_Route( 'widget-update/:id', $options );
		$router->addRoute('widget-update/:id', $route);
		
		$options['action'] = 'publish';
		$route = new Zend_Controller_Router_Route( 'widget-publish/:id', $options );
		$router->addRoute('widget-publish/:id', $route);
		
		$options['action'] = 'delete';
		$route = new Zend_Controller_Router_Route( 'widget-delete/:id', $options );
		$router->addRoute('widget-delete/:id', $route);
		
		$options['action'] = 'move';
		$options['direction'] = null;
		$route = new Zend_Controller_Router_Route( 'widget-move/:id/:direction', $options );
		$router->addRoute('widget-move/:id/:direction', $route);
		
		$options['controller'] = 'skin';
		$options['action'] = 'list';
		$route = new Zend_Controller_Router_Route( 'skins', $options );
		$router->addRoute('skins', $route);
		
		$options['action'] = 'select';
		$route = new Zend_Controller_Router_Route( 'skin-select/:id', $options );
		$router->addRoute('skin-select/:id', $route);
		
		$options['controller'] = 'index';
		$options['action'] = 'extension';
		$route = new Zend_Controller_Router_Route( 'extensions', $options );
		$router->addRoute('extensions', $route);
		
	}
	
}