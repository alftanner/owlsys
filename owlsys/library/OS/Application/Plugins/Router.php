<?php
class OS_Application_Plugins_Router extends Zend_Controller_Plugin_Abstract
{
	public function routeStartup(Zend_Controller_Request_Abstract $request)
	{
	    try {
	        $mdlMenuItem = new menu_Model_Item();
	        $menuItems = $mdlMenuItem->getListForRouting();
	        #Zend_Debug::dump($menuItems->toArray());
	        #die();
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
	    } catch (Exception $e) {
	        try {
	            $writer = new Zend_Log_Writer_Stream(APPLICATION_LOG_PATH . 'plugins.log');
	            $logger = new Zend_Log($writer);
	            $logger->log($e->getMessage(), Zend_Log::ERR);
	        } catch (Exception $e) {
	        }
	    }
		
	}
	
}