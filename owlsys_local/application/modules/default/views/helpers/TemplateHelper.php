<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package default
 * @subpackage helpers
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Zend_View_Helper_TemplateHelper extends Zend_View_Helper_Abstract
{
	
	public function TemplateHelper(  )
	{
	}
	
	function getCSSPath()
	{
	    $url = Zend_Controller_Front::getInstance()->getBaseUrl().'/skins/';
	    $url .= Zend_Registry::get('skin').'/css/';
	    return $url;
	}
	
	function getJSPath()
	{
	    $url = Zend_Controller_Front::getInstance()->getBaseUrl().'/skins/';
	    $url .= Zend_Registry::get('skin').'/js/';
	    return $url;
	}
	
	function getImagePath()
	{
	    $url = Zend_Controller_Front::getInstance()->getBaseUrl().'/skins/';
	    $url .= Zend_Registry::get('skin').'/images/';
	    return $url;
	}
	
}
