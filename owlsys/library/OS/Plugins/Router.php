<?php
class OS_Plugins_Router extends Zend_Controller_Plugin_Abstract
{
	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
	    try {
	        
	        $mdlMenuItemMapper = menu_Model_ItemMapper::getInstance();
	        
	        $menuItems = $mdlMenuItemMapper->getRegisteredRoutes();
	        foreach ( $menuItems as $menuItem ) {
	            $route = new Zend_Controller_Router_Route(
	                    $menuItem->getRoute(),
	                    array(
	                            'module'     => $menuItem->getResource()->getModule(),
	                            'controller' => $menuItem->getResource()->getController(),
	                            'action'     => $menuItem->getResource()->getActioncontroller(),
	                    )
	            );
	            Zend_Controller_Front::getInstance()->getRouter()->addRoute($menuItem->getRoute(), $route);
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