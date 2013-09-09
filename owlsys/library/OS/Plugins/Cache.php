<?php
class OS_Plugins_Cache extends Zend_Controller_Plugin_Abstract
{
    public function routeStartup()
    {
        $frontendOptions = array('lifetime'=>60*60*24, 'automatic_serialization'=>true);
        $backendOptions = array('cache_dir'=> APPLICATION_CACHE_PATH );
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        Zend_Registry::set('cache', $cache);
    }
}