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
    		$mdlResource = Acl_Model_ResourceMapper::getInstance();

    		$registeredList = $mdlResource->getAll();
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
    				$rsTemp = $rsRegistered->getModule().'-'.$rsRegistered->getController().'-'.$rsRegistered->getActioncontroller();
    				if ( strcasecmp($rsa, $rsTemp) == 0 ) {
    					$isRegistered = true;
    				}
    			}
    			if ( ! $isRegistered ) {
    				$arrResource = explode('-', $rsa);
    				$resource = new Acl_Model_Resource();
    				$resource->setModule($arrResource[0]);
    				$resource->setController($arrResource[1]);
    				$resource->setActioncontroller($arrResource[2]);
    				$mdlResource->save($resource);
    			}
    		}
    		
    		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("Resources are synchronized") ) );
    		$this->redirect('resources');
    	} catch (Exception $e) {
    	    Zend_Debug::dump($e->getMessage());
    	    Zend_Debug::dump($e->getTraceAsString());
    	    die();
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
        	$mdlResource = Acl_Model_ResourceMapper::getInstance();
        	$paginator = Zend_Paginator::factory($mdlResource->getAll());
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
			$mdlResource = Acl_Model_ResourceMapper::getInstance();
			$resource = new Acl_Model_Resource();
			$mdlResource->find($id, $resource);
			$mdlResource->remove($resource);
			
			$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("The resource was deleted") ) );
			$this->redirect('resources');
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('resources');
        }
        return;
    }


}







