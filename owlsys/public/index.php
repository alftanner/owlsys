<?php
try {
    // Define path to application directory
    defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
    
    // Define application environment
    defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
    
    // Ensure library/ is on include_path
    set_include_path(
        implode(
            PATH_SEPARATOR, array(
                realpath(APPLICATION_PATH . '/../library'),
                get_include_path(),
                APPLICATION_PATH . '/../library/phpids/lib',
                APPLICATION_PATH . '/../library/phpids/lib/IDS',
            )
        )
    );
    
    /** Zend_Application */
    require_once 'Zend/Application.php';
    
    // Create application, bootstrap, and run
    $application = new Zend_Application(
            APPLICATION_ENV,
            APPLICATION_PATH . '/configs/application.ini'
    );
    $application->bootstrap()->run();
} catch (Exception $e) {
    echo "<!DOCTYPE HTML><html>
            <head><title>OWLSys</title></head>
            <body style='font-family: sans-serif; font-size: 12px;'>";
    echo '<p>Exception: '.$e->getTraceAsString().'<br>';
    echo 'Line: '.$e->getLine().'<br>';
    echo 'File: '.$e->getFile().'</p>';
    echo "</body></html>";
}