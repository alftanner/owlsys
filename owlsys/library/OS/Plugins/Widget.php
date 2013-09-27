<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package OS\Application\Plugins
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class OS_Plugins_Widget extends Zend_Controller_Plugin_Abstract
{
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Plugin_Abstract::preDispatch()
     */
    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {
        try {
            $viewHelperAction = new Zend_View_Helper_Action();
            $navigation = Zend_Registry::get('Zend_Navigation');
            $navItem = $navigation->findBy('active', true);
            $auth = Zend_Auth::getInstance();
            /* @var $acl Zend_Acl */
            $acl = Zend_Registry::get('ZendACL');
            
            $mdlRole = new Acl_Model_Role();
            $mdlWidget = new System_Model_Widget();
            $mdlWidgetDetail = new System_Model_Widgetdetail();
            $mdlResource = new Acl_Model_Resource();
            $role = null;
            if ($auth->hasIdentity()) {
                $identity = $auth->getIdentity();
                $role = $mdlRole->findRow( intval($identity->role_id) )->current();
            } else {
                $role = $mdlRole->findRow( 3 )->current();
            }
			
            $hookXml = APPLICATION_PATH . '/configs/hooks.xml';
            $sxeHook = new SimpleXMLElement($hookXml, null, true);
            
            $hooks = array();
            foreach ($sxeHook as $hook) {
                $hooks[] = strval($hook);
            }
            
            $widgets = $mdlWidgetDetail->getWidgetsByHooksAndItemId($navItem->id, $hooks);
            foreach ($widgets as $widget) {
                $hookContent = '';
                $params = array();
                $widgetParams = Zend_Json::decode($widget->params);
                foreach ( $widgetParams as $strParam => $valParam ) {
                    $params[ $strParam ] = $valParam;
                }
                $resource = strtolower($widget->module.':'.$widget->controller);
                $privilege = strtolower($widget->actioncontroller);
                if ( $acl->isAllowed($role->id, $resource, $privilege) ) {
                    $hookContent .= ($widget->showtitle == 1) ? "<h3>".$widget->title."</h3>" : "";
                    $hookContent .= $viewHelperAction->action(
                            $widget->actioncontroller, 
                            $widget->controller, 
                            $widget->module, 
                            $params);
                }
                Zend_Layout::getMvcInstance()->assign($widget->position, $hookContent);
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