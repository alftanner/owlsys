<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package system
 * @subpackage controllers
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class System_IndexController extends Zend_Controller_Action
{

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        /* Initialize action controller here */
    }

    /**
     * index action for index controller
     */
    public function indexAction()
    {
        // action body
        $translate = Zend_Registry::get('Zend_Translate');
        try{
        
        } catch (Exception $e) {
        	echo $e->getMessage();
        }
    }

    /**
     * extension action for index controller
     */
    public function extensionAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$mdlResource = new Acl_Model_Resource();
        	$modules = $mdlResource->getModules();
        	$modData = array();
        	foreach ( $modules as $module )
        	{
        		$moduleInfoFile = APPLICATION_PATH.'/modules/'.$module->module.'/about.xml';
        		if ( file_exists( $moduleInfoFile ) )
        		{
        			$sxe = new SimpleXMLElement( $moduleInfoFile, null, true);
        			foreach( $sxe as $mod ) {
        				$modData[] = $mod;
        			}
        		}
        	}
        	$this->view->modules = $modData;
        } catch (Exception $e) {
        	echo $e->getMessage();
        }
    }


}



