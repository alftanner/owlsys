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
class OS_Application_Plugins_Widget extends Zend_Controller_Plugin_Abstract
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
            $acl = Zend_Registry::get('ZendACL');
            $mdlRole = new Acl_Model_Role();
            if ($auth->hasIdentity()) {
                $identity = $auth->getIdentity();
                $role = $mdlRole->find($identity->role_id)->current();
            } else {
                $role = $mdlRole->find(3)->current();
            }
            // Zend_Debug::dump($acl);
            //Zend_Debug::dump($role->id);
            $mdlWidget = new System_Model_Widget();
            $hookXml = APPLICATION_PATH . '/configs/hooks.xml';
            $sxeHook = new SimpleXMLElement($hookXml, null, true);
            $mdlResource = new Acl_Model_Resource();
            $mdlWidgetDetail = new System_Model_Widgetdetail();
            foreach ($sxeHook as $hook) {
				#/*
				#Zend_Debug::dump($hook); 
				$widgets = $mdlWidgetDetail->getWidgetsByHookAndItemId($navItem->id, $hook); 
				$hookContent = ''; 
				foreach ($widgets as $widget) 
				{
					#Zend_Debug::dump($widget->title); 
					$params = array();
					$widgetParams = explode("\n", $widget->params);
					
					foreach ( $widgetParams as $strParam ) 
					{
						$paramKey = substr($strParam, 0, strpos($strParam, "=")); 
						$paramValue = substr($strParam, strpos($strParam, "=")+1, strlen($strParam)); 
						if( strlen(trim($paramValue)) > 0 ) # $subItem['params'][$paramKey ] = $paramValue; 
							$params[ $paramKey ] = trim($paramValue); 
					} 
					$rsACL = strtolower($widget->module.':'.$widget->controller); 
					$prvACL = strtolower($widget->actioncontroller);
					#Zend_Debug::dump( $acl->isAllowed($role->id, $rsACL, $prvACL) );
					
					if ( $acl->isAllowed($role->id, $rsACL, $prvACL) ) 
					{
					    $hookContent .= ($widget->showtitle == 1) ? "<h3>".$widget->title."</h3>" : "";
						$hookContent .= $viewHelperAction->action($widget->actioncontroller, $widget->controller, $widget->module, $params);
					} 
				}
                #*/
                Zend_Layout::getMvcInstance()->assign(strval($hook), $hookContent);
            }
        } catch (Exception $e) { 
            echo $e->getMessage(); 
        }
    }
}