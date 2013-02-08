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
class OS_Application_Plugins_Skin extends Zend_Controller_Plugin_Abstract
{
	
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Plugin_Abstract::preDispatch()
     */
	public function preDispatch( Zend_Controller_Request_Abstract $request)
	{
		try {
			$layout = Zend_Layout::getMvcInstance()->getLayout();
			
			$boostrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
			$userAgent = $boostrap->getResource('useragent');
			$device = $userAgent->getDevice();
			
			$mdlSkin = new System_Model_Skin();
			$skin = $mdlSkin->getSkinSelected();
			$skinName = is_null($skin) ? 'default' : strtolower($skin->name);
			$vr = Zend_Controller_Action_HelperBroker::getStaticHelper('Layout');
			$view = $vr->getView();
			$skinData = new Zend_Config_Xml('./skins/'.$skinName.'/skin.xml');
			
			# css files
			$stylesheet = ( (int) $device->getFeature('is_desktop') == 1 ) ? $skinData->files->stylesheet : $skinData->files->stylesheetMobile;
			$view->headLink()->prependStylesheet($view->baseUrl().'/skins/'.$skinName.'/css/'.$stylesheet);
			$view->headLink()->headLink( array('rel' => 'favicon', 'href' => $view->baseUrl().'/skins/'.$skinName.'/favicon.ico'), 'PREPEND');
			
			Zend_Registry::set('skin', $skinName);
			
			# javascript files
			$jsfiles = $skinData->files->js->toArray();
			foreach ( $jsfiles as $js )
			{
				$view->headScript()->prependFile( $view->baseUrl(). '/skins/'.$skinName.'/js/'.$js);
			}
			
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