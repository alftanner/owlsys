<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package modules
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Add FlashMessages View Helper to the view
     * Add Thumbnail View Helper to the view
     * @return Zend_View
     */
    protected function _initView()
    {
    	try {
    	    Zend_Registry::set('options', $this->getOptions());
    	    
    		// Initialize View
    		$view = new Zend_View();
    		 
    		$rs = $this->getOption('resources');
    		 
    		$view->doctype( $rs['layout']['doctype'] );
    		$view->headTitle( $rs['layout']['web_site_title'] );
    		#echo Zend_Version::VERSION;
    		
    		// Add it to the ViewRenderer
    		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
    		$viewRenderer->setView($view);
    		
    		$moduleDirectory = $rs['frontController']['moduleDirectory'];
    		$view->addHelperPath( $moduleDirectory.'/default/views/helpers/', 'Zend_View_Helper_FlashMessages');
    		$view->addHelperPath( $moduleDirectory.'/default/views/helpers/', 'Zend_View_Helper_Thumbnail');
    		$view->addHelperPath( $moduleDirectory.'/default/views/helpers/', 'Zend_View_Helper_TemplateHelper');
    		
    		$view->addScriptPath( APPLICATION_PATH."/modules/default/views/scripts/partials/");
    		$view->addScriptPath( APPLICATION_PATH."/modules/default/views/scripts/");
    		
    		// Return it, so that it can be stored by the boostrap
    		return $view;
    	} catch (Exception $e) {
    	}
    
    }
	
    /**
     * It registers OwlSys namespace
     * @return Zend_Loader_Autoloader
     */
	public function _initAutoload ( )
	{
		try {
			// Add autoloader empty namespace
			$autoLoader = Zend_Loader_Autoloader::getInstance();
			$autoLoader->registerNamespace( 'OS_' );
			$autoLoader->registerNamespace( 'Twitter_' );
			return $autoLoader;
		} catch (Exception $e) {
		}
	}
	
	/**
	 * Set db prefix from global config
	 */
	public function _initDbPrefix()
	{
		try {
			$configArray = $this->getOptions();
			$this->config = new Zend_Config($configArray);
			$config = $this->config;
			$tablePrefix = $config->resources->multidb->front_db->prefix;
			Zend_Registry::set("tablePrefix", $tablePrefix);
		} catch (Exception $e) {
		}
	}
	
	public function _initLocale()
	{
	    Zend_Locale::setDefault('en');
	}
	
	/**
	 * Call plugins.
	 */
	public function _initPlugins() 
	{
		try {
			$this->bootstrap('frontController') ;
			$front = $this->getResource('frontController') ;
			$front->registerPlugin( new OS_Plugins_Ids() );
			$front->registerPlugin( new OS_Plugins_Cache() );
			$front->registerPlugin( new OS_Plugins_Locale() );
			//$front->registerPlugin( new OS_Application_Plugins_Ssl() );
			$front->registerPlugin( new OS_Plugins_Router() );
			$front->registerPlugin( new OS_Plugins_Acl() );
			$front->registerPlugin( new OS_Plugins_Aclrouter() );
            $front->registerPlugin( new OS_Plugins_Layout() );
			$front->registerPlugin( new OS_Plugins_Navigation() );
			$front->registerPlugin( new OS_Plugins_Skin());
			$front->registerPlugin( new OS_Plugins_Widget() );
		} catch (Exception $e) {
		}
	}
	
	/**
	 * It defines dir_mod_contact_img_uploads and dir_mod_contact_thumbs_uploads
	 */
	public function _initDefines()
	{
		define('APPLICATION_CACHE_PATH', APPLICATION_PATH.'/../data/cache/');
		define('APPLICATION_LOG_PATH', APPLICATION_PATH.'/../data/log/');
		
	}
	
	/**
	 * @link http://framework.zend.com/manual/1.12/en/zend.db.table.html#zend.db.table.metadata.caching
	 */
	public function _initMetadaCache()
	{
		$frontendOptions = array('automatic_serialization'=>true);
		$backendOptions = array('cache_dir' => APPLICATION_CACHE_PATH);
		$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
		Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
	}
	
	/**
	 * @link http://framework.zend.com/manual/1.12/en/zend.paginator.advanced.html#zend.paginator.advanced.caching
	 */
	public function _initPaginatorCache()
	{
		$frontendOptions = array('automatic_serialization'=>true,'lifetime'=>3600);
		$backendOptions = array('cache_dir' => APPLICATION_CACHE_PATH);
		$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
		Zend_Paginator::setCache($cache);
	}
	
}