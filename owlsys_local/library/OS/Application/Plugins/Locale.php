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
class OS_Application_Plugins_Locale extends Zend_Controller_Plugin_Abstract
{
    
	#public function preDispatch( Zend_Controller_Request_Abstract $request)
	/**
	 * (non-PHPdoc)
	 * @see Zend_Controller_Plugin_Abstract::routeShutdown()
	 */
	public function routeShutdown( Zend_Controller_Request_Abstract $request)
	{
		$translate = new Zend_Translate( "csv",  APPLICATION_PATH."/languages/", null, array("scan"=>Zend_Translate::LOCALE_FILENAME) );
		Zend_Registry::set("Zend_Translate", $translate);
	}
	
	public function preDispatch($request)
	{
	    $translate = Zend_Registry::get("Zend_Translate");
	    $zl = new Zend_Locale();
	    $zl->setLocale( Zend_Locale::BROWSER );
	    $requestedLanguage = key( $zl->getBrowser() );
	    
	    if ( in_array($requestedLanguage, $translate->getList()) || self::isSubStrList($translate->getList(), $requestedLanguage) ){
	    	$language = self::isSubStrList($translate->getList(), $requestedLanguage);
	    } else {
	    	$language = "en";
	    }
	    $translate->setLocale($language);
	}
	
	static function isSubStrList( $array, $value ) {
		foreach ( $array as $element ) {
			if ( stristr($value, $element) !== false ) {
				return $element;
			}
		}
		return false;
	}
	
}