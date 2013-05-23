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
    
    public function preDispatch ($request)
    {
        try {
            
            $locale = new Zend_Locale();
            $locale->setDefault('en');
            $locale->setLocale(Zend_Locale::BROWSER);
            $requestedLanguage = key($locale->getBrowser());
            
            $formatter = new Zend_Log_Formatter_Simple('%message%' . PHP_EOL);
            $writer = new Zend_Log_Writer_Stream(APPLICATION_LOG_PATH . 'translations.log');
            $writer->setFormatter($formatter);
            $logger = new Zend_Log($writer);
            
            $frontendOptions = array(
                    'cache_id_prefix' => 'translation', 
                    'lifetime' => 86400,
                    'automatic_serialization' => true
            );
            $backendOptions = array(
                    'cache_dir' => APPLICATION_CACHE_PATH
            );
            $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
            
            $options = array(
                    'adapter' => 'gettext',
                    'scan' => Zend_Translate::LOCALE_FILENAME,
                    'content' => APPLICATION_PATH.'/languages/en/en.mo',
                    'locale' => 'auto',
                    'disableNotices' => true,
            #        'log' => $logger,
            #        'logMessage' => '%locale%;%message%',
            #        'logUntranslated' => true,
            );
            
            $translate = new Zend_Translate($options);
            
            if (! $translate->isAvailable($locale->getLanguage())) {
                $locale->setLocale('en');
            } else {
                $translate->setLocale($locale);
            }
            
            $translate->setCache($cache);
            
            Zend_Registry::set('locale', $locale->getLanguage());
            Zend_Registry::set('Zend_Translate', $translate);
            
            
        } catch (Exception $e) {
            try {
		        $writer = new Zend_Log_Writer_Stream(APPLICATION_LOG_PATH . 'plugin-locale.log');
		        $logger = new Zend_Log($writer);
		        $logger->log($e->getMessage(), Zend_Log::ERR);
		    } catch (Exception $e) {
		    }
        }
    }

}