<?php

/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package acl
 * @subpackage plugins
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Acl_Plugin_Lang extends Zend_Controller_Plugin_Abstract
{

    /**
     * Load language file for ACL Module
     *
     * @see Zend_Controller_Plugin_Abstract::routeShutdown()
     */
    public function routeShutdown (Zend_Controller_Request_Abstract $request)
    {
        /* @var $translate Zend_Translate */
        $translate = Zend_Registry::get("Zend_Translate");
        $locale = Zend_Registry::get('locale');
        
        $formatter = new Zend_Log_Formatter_Simple('%message%' . PHP_EOL);
        $writer = new Zend_Log_Writer_Stream(
                APPLICATION_PATH . '/data/log/translations.log');
        $writer->setFormatter($formatter);
        $logger = new Zend_Log($writer);
        
        $options = array(
            'clear' => false,
            'scan' => Zend_Translate::LOCALE_DIRECTORY,
            'disableNotices' => true,
            'log' => $logger,
            'logMessage' => '%locale%;%message%',
            'logUntranslated' => true,
        );
        
        $translateModule = new Zend_Translate('csv', 
                APPLICATION_PATH . "/modules/acl/languages/", 'auto', $options);
        $translate->getAdapter()->addTranslation($translateModule);
        /*$translate->getAdapter()->addTranslation(
                APPLICATION_PATH . "/modules/acl/languages/en.csv", "en", 
                $options);
        ;
        $translate->getAdapter()->addTranslation(
                APPLICATION_PATH . "/modules/acl/languages/es.csv", "es", 
                $options);*/
        $translate->setLocale($locale);
    }
}