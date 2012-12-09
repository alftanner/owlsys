<?php
class OS_Application_Plugins_Router extends Zend_Controller_Plugin_Abstract
{
	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
		$mdlMenuItem = new menu_Model_Item();
		$menuItems = $mdlMenuItem->getListForRouting();
		if ( $menuItems->count() > 0 ) 
		{
			foreach ( $menuItems as $menuItem )
			{
				$route = new Zend_Controller_Router_Route(
					( strlen($menuItem->id_alias) > 0 
						? $menuItem->id_alias 
						: strtolower($menuItem->module.'-'.$menuItem->controller.'-'.$menuItem->actioncontroller) ),
					array(
						'module'     => $menuItem->module,
						'controller' => $menuItem->controller,
						'action'     => $menuItem->actioncontroller,
					)
				);
				Zend_Controller_Front::getInstance()->getRouter()->addRoute(
					( strlen($menuItem->id_alias) > 0 
						? $menuItem->id_alias 
						: strtolower($menuItem->module.'-'.$menuItem->controller.'-'.$menuItem->actioncontroller) ),
					$route
				);
			}
		}
		
		$route = new Zend_Controller_Router_Route(
			':module/:controller/:action/*',
			array( )
		);
		Zend_Controller_Front::getInstance()->getRouter()->addRoute('pagination', $route);
		
	}
	
}