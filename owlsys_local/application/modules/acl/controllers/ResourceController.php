<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package acl
 * @subpackage controller
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Acl_ResourceController extends Zend_Controller_Action
{

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        /* Initialize action controller here */
    }

    /**
     * Index action for resource controller
     */
    public function indexAction()
    {
        // action body
    }

    /**
     * Sync action for resource controller
     */
    public function syncAction()
    {
    	$translate = Zend_Registry::get('Zend_Translate');
    	try {
    		$mdlResource = new Acl_Model_Resource();
    		 
    		$registeredList = $mdlResource->getRegisteredList();
    		#print_r($registeredList);
    		#die();
    		 
    		$resourcesAvailable = array();
    		$sxe = new SimpleXMLElement( APPLICATION_PATH.'/../.zfproject.xml', null, true);
    		foreach( $sxe->projectDirectory->applicationDirectory->modulesDirectory->moduleDirectory as $module ) {
    			#echo "<h1>Modulo:".$module['moduleName']."</h1>";
    			foreach ( $module->controllersDirectory->controllerFile as $controller ) {
    				#echo "<h2>controlador: ".$controller['controllerName']."<h2>";
    				foreach ( $controller->actionMethod as $action) {
    					#echo "<h3>action: ".$action['actionName']."<h3>";
    					$resourcesAvailable[] = $module['moduleName'].'-'.$controller['controllerName'].'-'.$action['actionName'];
    				}
    			}
    		}
    		
    		foreach ( $resourcesAvailable as $rsa ) {
    			$isRegistered = false;
    			foreach ($registeredList as $rsRegistered) {
    				$rsTemp = $rsRegistered->module.'-'.$rsRegistered->controller.'-'.$rsRegistered->actioncontroller;
    				if ( strcasecmp($rsa, $rsTemp) == 0 ) {
    					$isRegistered = true;
    				}
    			}
    			if ( ! $isRegistered ) {
    				$arrResource = explode('-', $rsa);
    				$resource = $mdlResource->createRow();
    				$resource->module = $arrResource[0];
    				$resource->controller = $arrResource[1];
    				$resource->actioncontroller = $arrResource[2];
    				$resource->save();
    			}
    		}
    		
    		/* @var $cache Zend_Cache_Backend_File */
    		$cache = Zend_Registry::get('cacheACL');
    		$mdlRole = new Acl_Model_Role();
    		$roles = $mdlRole->getList();
    		foreach( $roles as $role ) {
    		    if ( $cache->test('cacheACL_'.$role->id) ) {
    		        $cache->remove('cacheACL_'.$role->id);
    		    }
    		}
    		
    		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("ACL_RESOURCES_SYNCD") ) );
    		return $this->_helper->redirector( "list", "resource", "acl" );
    	} catch (Exception $e) {
    		$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
    		return $this->_helper->redirector( "list", "resource", "acl" );
    	}
    	return;
    }

    /**
     * List action for resource controller
     */
    public function listAction()
    {
        try {
            /*
            $dir = new DirectoryIterator(APPLICATION_PATH.'/modules/');
            
            foreach ($dir as $fileinfo) {
                if (!$fileinfo->isDot() && $fileinfo->isDir()) {
                    $moduleController_dir = new DirectoryIterator(APPLICATION_PATH.'/modules/'.$fileinfo->getFilename().'/controllers/');
                    Zend_Debug::dump($fileinfo->getFilename(), 'Module Dir');
                    foreach ($moduleController_dir as $moduleControllerDir) {
                        if ( !$fileinfo->isDot() && $moduleControllerDir->isFile() ){
                            $controller = ucfirst($fileinfo->getFilename()).'_'.$moduleControllerDir->getFilename();
                            $controller = str_replace('.php', '', $controller);
                            #Zend_Debug::dump($moduleControllerDir->getFilename(),'Controller in '.$moduleControllerDir);
                            $class_exists = class_exists($controller);
                            if ( $class_exists ) {
                                $class_methods = get_class_methods($controller);
                                Zend_Debug::dump($controller, 'Controller');
                                Zend_Debug::dump($class_methods, 'Methods');
                            } else {
                                Zend_Debug::dump($class_exists, 'Controller '.$controller.' doesnt exist');
                            }
                        }
                    }
                } 
            }
            */
            #Zend_Debug::dump(APPLICATION_PATH.'modules/');
        
            #$s = new System_WidgetController(null, null);
            #var_dump($s);
            
        	$mdlResource = new Acl_Model_Resource();
        	$adapter = $mdlResource->getPaginatorAdapterList();
        	$paginator = new Zend_Paginator($adapter);
        	$paginator->setItemCountPerPage(20);
        	$pageNumber = $this->getRequest()->getParam('page',1);
        	$paginator->setCurrentPageNumber($pageNumber);
        	$this->view->resources = $paginator;
        } catch (Exception $e) {
        	echo $e->getMessage();
        }
        return;
    }

    /**
     * Delete action for resource controller
     * @throws Zend_Exception
     */
    public function deleteAction()
    {
    	$translate = Zend_Registry::get('Zend_Translate');
        try {
        	$id = $this->getRequest()->getParam( "id" );
			$mdlResource = new Acl_Model_Resource();
			$resource = $mdlResource->find( $id )->current();
			if ( !$resource ) {
				throw new Zend_Exception($translate->translate("LBL_ROW_NOT_FOUND"));
			}
			$resource->delete();
			$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_CHANGES_SAVED") ) );
			$this->_helper->redirector( "list", "resource", "acl" );
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "list", "resource", "acl" );
        }
        return;
    }


}







