<?php
class OS_Plugins_Ids extends Zend_Controller_Plugin_Abstract
{

    function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        try {
            require_once 'IDS/Init.php';
            require_once 'IDS/Log/Composite.php';
            require_once 'IDS/Log/Database.php';
            #require_once 'IDS/Log/File.php';
            $request = array ('REQUEST' => $_REQUEST, 'GET' => $_GET, 'POST' => $_POST, 'COOKIE' => $_COOKIE );
            $init = IDS_Init::init (APPLICATION_PATH . '/../library/phpids/lib/IDS/Config/Config.ini.php' );
            
            $ids = new IDS_Monitor ( $request, $init );
            $result = $ids->run ();
            
            if (! $result->isEmpty ()) {
                // This is where you should put some code that
                // deals with potential attacks, e.g. throwing
                // an exception, logging the attack, etc.
                $compositeLog = new IDS_Log_Composite();
                $compositeLog->addLogger(IDS_Log_Database::getInstance($init));
                #$compositeLog->addLogger(IDS_Log_File::getInstance($init));
                $compositeLog->execute($result);
                
                echo $result;
                die('<h1>Go away!</h1>');
                #$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                #$redirector->gotoUrl('default/error/error/eh/ids')->redirectAndExit();
            }
            return $request;
        } catch (Exception $e) {
            try {
                $writer = new Zend_Log_Writer_Stream(APPLICATION_LOG_PATH . 'plugin-ids.log');
                $logger = new Zend_Log($writer);
                $logger->log($e->getMessage().' line '.$e->getLine().' file '.$e->getFile(), Zend_Log::ERR);
            } catch (Exception $e) {
            }
        }
        
    }
}