<?php

class OS_Application_Plugins_Ssl extends Zend_Controller_Plugin_Abstract
{

    /**
     * Check the application.ini file for security settings.
     * If the url requires being secured, rebuild a secure url
     * and redirect.
     *
     * @param Zend_Controller_Request_Abstract $request            
     * @return void
     * @author Travis Boudreaux
     */
    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {
        try {
            $shouldSecureUrl = false;
            
            $options = Zend_Registry::getInstance()->get('options');
            // ar_dump($options['ssl'][$request->module][$request->controller][$request->action]['require_ssl']);
            // ie();
            
            // f (APPLICATION_ENV == ENV_PRODUCTION ) {
            
            // check configuration file for one of three require_ssl directives
            // secure an entire module with modules.module_name.require_ssl =
            // true
            // secure an entire controller with
            // modules.module_name.controller_name.require_ssl = true
            // secure an action with
            // modules.module_name.controller_name.action_name.require_ssl =
            // true
            if (@$options['ssl'][$request->module]['require_ssl'] ||
                     @$options['ssl'][$request->module][$request->controller]['require_ssl'] ||
                     @$options['ssl'][$request->module][$request->controller][$request->action]['require_ssl']) {
                $shouldSecureUrl = true;
            }
            
            if ($shouldSecureUrl) {
                $this->_secureUrl($request, true);
            } else {
                $this->_secureUrl($request, false);
            }
            //
        } catch (Exception $e) {
            try {
                $writer = new Zend_Log_Writer_Stream(
                        APPLICATION_LOG_PATH . 'plugins.log');
                $logger = new Zend_Log($writer);
                $logger->log($e->getMessage(), Zend_Log::ERR);
            } catch (Exception $e) {}
        }
    }

    /**
     * Check the request to see if it is secure.
     * If it isn't
     * rebuild a secure url, redirect and exit.
     *
     * @param Zend_Controller_Request_Abstract $request            
     * @param boolean $required            
     * @return void
     * @author Travis Boudreaux
     */
    protected function _secureUrl (Zend_Controller_Request_Abstract $request, 
            $required)
    {
        $server = $request->getServer();
        $hostname = $server['HTTP_HOST'];
        
        if (! $request->isSecure() && $required == true) {
            // url scheme is not secure so we rebuild url with secureScheme
            $url = Zend_Controller_Request_Http::SCHEME_HTTPS . "://" . $hostname .
                     $request->getPathInfo();
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper(
                    'redirector');
            $redirector->setGoToUrl($url);
            $redirector->redirectAndExit();
        } else {}
    }
}