<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package menu
 * @subpackage plugins
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Menu_Plugin_Lang extends Zend_Controller_Plugin_Abstract
{
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Plugin_Abstract::routeShutdown()
     */
	public function routeShutdown( Zend_Controller_Request_Abstract $request)
	{
	    $translate = Zend_Registry::get("Zend_Translate");
	    $options = array('clear'=>false);
	    $translate->getAdapter()->addTranslation(
    		APPLICATION_PATH."/modules/menu/languages/en.csv", "en", $options
	    );
	    $translate->getAdapter()->addTranslation(
    		APPLICATION_PATH."/modules/menu/languages/es.csv", "es", $options
	    );
	}
}