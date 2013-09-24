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
    		$mdlRole = new Acl_Model_Role();
    		
    		$registeredList = $mdlResource->getAll();
    		 
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
    		
    		$roleList = $mdlRole->getList();
    		foreach ( $roleList as $role ) {
    		    /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
                $cache = Zend_Registry::get('cache');
                $cacheId = 'role_find_'.$role->id;
                if ( $cache->test($cacheId) ) {
                    $cache->remove($cacheId);
                }
                $cacheId = 'cacheACL_'.$role->id;
                if ( $cache->test($cacheId) ) {
                    $cache->remove($cacheId);
                }
    		}
    		
    		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("Resources are synchronized") ) );
    		$this->redirect('resources');
    	} catch (Exception $e) {
//     	    Zend_Debug::dump($e->getMessage());
//     	    Zend_Debug::dump($e->getTraceAsString());
//     	    die();
    		$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
    		$this->redirect('resources');
    	}
    	return;
    }

    /**
     * List action for resource controller
     */
    public function listAction()
    {
        try {
        	$mdlResource = new Acl_Model_Resource();
        	$paginator = Zend_Paginator::factory($mdlResource->getAll());
        	$paginator->setItemCountPerPage(20);
        	$pageNumber = $this->getRequest()->getParam('page',1);
        	$paginator->setCurrentPageNumber($pageNumber);
        	$paginator->setCacheEnabled(true);
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
			$resource = $mdlResource->find($id)->current();
			$resource->delete();
			
			$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("The resource was deleted") ) );
			$this->redirect('resources');
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('resources');
        }
        return;
    }


}







