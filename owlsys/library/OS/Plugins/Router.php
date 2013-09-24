<?php
class OS_Plugins_Router extends Zend_Controller_Plugin_Abstract
{
	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
	    try {
	        
	        $mdlMenuItem = new menu_Model_Item();
	        
	        $menuItems = $mdlMenuItem->getRegisteredRoutes();
	        foreach ( $menuItems as $menuItem ) {
	            $route = new Zend_Controller_Router_Route(
	                    $menuItem->route,
	                    array(
	                            'module'     => $menuItem->module,
	                            'controller' => $menuItem->controller,
	                            'action'     => $menuItem->actioncontroller,
	                    )
	            );
	            Zend_Controller_Front::getInstance()->getRouter()->addRoute($menuItem->route, $route);
	        }

	    } catch (Exception $e) {
	        //echo $e->getMessage();
	        //die();
	        try {
	            $writer = new Zend_Log_Writer_Stream(APPLICATION_LOG_PATH . 'plugins.log');
	            $logger = new Zend_Log($writer);
	            $logger->log($e->getMessage(), Zend_Log::ERR);
	        } catch (Exception $e) {
	        }
	    }
		
	}
	
}