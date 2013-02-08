<?php

class Content_Plugin_Router extends Zend_Controller_Plugin_Abstract
{

    public function routeStartup (Zend_Controller_Request_Abstract $request)
    {
        $mdlMenuItem = new menu_Model_Item();
        $menuItems = $mdlMenuItem->getListForRouting();
        $frontController = Zend_Controller_Front::getInstance();
        $router = $frontController->getRouter();
        if ($menuItems->count() > 0) {
            foreach ($menuItems as $menuItem) {
                if (strcasecmp($menuItem->controller, "article") == 0 &&
                         strcasecmp($menuItem->actioncontroller, "view") == 0) {
                    $params = Zend_Json::decode($menuItem->params);
                    $options = array();
                    $options['module'] = 'content';
                    $options['controller'] = 'article';
                    $options['action'] = 'view';
                    foreach ( $params as $param )
                    {
                        $options['aid'] = $param['aid'];
                    }
                    $options['mid'] = $menuItem->id;
                    $route = new Zend_Controller_Router_Route( $menuItem->id_alias, $options );
                    $router->addRoute($menuItem->id_alias, $route);
                }
            }
        }
    }


}