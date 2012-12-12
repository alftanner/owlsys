<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package contact
 * @subpackage controllers
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Menu_ItemController extends Zend_Controller_Action
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
     * Index action for item controller
     */
    public function indexAction()
    {
        // action body
    }

    /**
     * List action for item controller
     * @throws Exception
     */
    public function listAction()
    {
    	$translate = Zend_Registry::get('Zend_Translate');
        try {
        	$menuId = $this->getRequest()->getParam('menu', 0);
        	$mdlMenu = new menu_Model_Menu();
        	$menu = $mdlMenu->find( $menuId )->current();
        	if ( !$menu ) throw new Exception($translate->translate("MENU_ROW_NOT_FOUND"));
        	$mdlMenuItem = new menu_Model_Item();
        	$adapter = $mdlMenuItem->getPaginatorAdapterListByMenu($menu);
	        $paginator = new Zend_Paginator($adapter);
	        $paginator->setItemCountPerPage(10);
	        $pageNumber = $this->getRequest()->getParam('page',1);
	        $paginator->setCurrentPageNumber($pageNumber);
	        
	        $this->view->items = $paginator;
        	$this->view->menu = $menu;
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector('list', 'menu', 'menu');
        }
        return;
    }

    /**
     * Add action for item controller
     * @throws Exception
     * @return NULL
     */
    public function addAction()
    {
    	try {
    		$translate = Zend_Registry::get('Zend_Translate');
    		
    		$menuId = $this->getRequest()->getParam('menu_id', 0);
    		$mdlMenu = new menu_Model_Menu();
    		$menu = $mdlMenu->find( $menuId )->current();
    		if ( !$menu ) throw new Exception($translate->translate("MENU_ROW_NOT_FOUND"));
    		$this->view->menu = $menu;

    		#/mod/acl/mid/6/menu_id/1
    		
    		$module = $this->getRequest()->getParam('mod'); # module name in request param
    		$mid = $this->getRequest()->getParam('mid'); # menu item id in xml file <item id="?">
    		$menuFile = APPLICATION_PATH.'/modules/'.$module.'/menus.xml';
    		if ( !file_exists( $menuFile ) ) {
    			throw new Exception($translate->translate("MENU_XML_FILE_NOT_FOUND"));
    		}
    		
    		$sxe = new SimpleXMLElement( $menuFile, null, true);
    		$element = null;
    		foreach( $sxe as $sxeMenuItem ) {
    			if ( $sxeMenuItem['id'] == $mid ) {
    				$element = $sxeMenuItem;
    				break;
    			}
    		}
    		if ( !$element ) throw new Exception($translate->translate("MENU_XML_ITEM_ELEMENT_NOT_FOUND"));
    		
    		$mdlResource = new Acl_Model_Resource();
    		$resource = $mdlResource->getIdByDetail($module, strval($element->controller), strval($element->action));
    		if ( !$resource ) throw new Exception($translate->translate("ACL_RESOURCE_NOT_FOUND"));
    		
    		$frmMenuItem = ucfirst( strtolower( strval($element->module) ) ).'_Form_Menuitems';
    		$frmMenuItem = ( strtolower(strval($element->module)) == 'menu' ) ? 'menu_Form_Menuitems' : $frmMenuItem;
    		#$frmWidget = new $frmWidget( array('typeForm'=>'test') );
    		$frmMenuItem = new $frmMenuItem( array('menuType'=>strtolower(strval($element->menu_type))) );
    		
    		$frmMenuItem->getElement('mid')->setValue( (int) $mid );
    		$frmMenuItem->getElement('mod')->setValue( strval($module) );
    		$frmMenuItem->getElement('menu_id')->setValue( $menuId );
    		$frmMenuItem->getElement('resource_id')->setValue( $resource->id );
    		
    		$cbParentItem = $frmMenuItem->getElement('parent_id');
    		$mdlMenuItem = new menu_Model_Item();
    		$menuItemList = $mdlMenuItem->getListItemsByMenu($menu);
    		$cbParentItem->addMultiOption( 0, $translate->translate("MENU_NOT_PARENT") );
    		if ( $menuItemList )
    		{
	    		foreach ( $menuItemList as $menuItemRow ) {
	    			$cbParentItem->addMultiOption( $menuItemRow->id, $menuItemRow->title );
	    		}
    		}
    		
    		$frmMenuItem->setAction( $this->_request->getBaseUrl() . "/menu/item/add" );
    		$this->view->frmMenuItem = $frmMenuItem;
    		$this->view->menuitem = $element;
    		
    		if ( $this->getRequest()->isPost() )
    		{
    		    if ( $frmMenuItem->isValid( $this->getRequest()->getParams() ) )
    		    {
    		    	
					$mdlMenuItem = new menu_Model_Item();
					$menuItem = $mdlMenuItem->createRow( $this->getRequest()->getParams() );
					
					$parentItem = $mdlMenuItem->find( $menuItem->parent_id )->current();
					$menuItem->depth = $parentItem->depth+1;
					
					$mdlMenuItem->save($menuItem);
					
					$frmMIValues = $frmMenuItem->getValues();
					$params = array();
					foreach ( $frmMIValues as $wvk => $wv )
					{
						if ( !in_array($wvk, $frmMenuItem->defaultFormFields) )
						{
							$params[] = $wvk.'='.$wv.'';
						}
					}
					$params = implode("\n", $params);
					$menuItem->params = $params;
					
					$menuItem->save();
					
					$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("MENU_ITEM_ADDED_SUCCESSFULLY") ) );
					$this->_helper->redirector('list', 'item', 'menu', array('menu'=>$menu->id));
    		    } 
    		} else {
    			$fields = array();
    			foreach ( $frmMenuItem->getElements() as $element ) $fields[] = $element->getName();
    			$frmMenuItem->addDisplayGroup( $fields, 'form', array( 'legend' => "MENU_CREATE_MENUITEM", ) );
    		}
    		
    	}
    	catch (Exception $e) {
    		$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector('list', 'item', 'menu', array('menu'=>$menu->id));
    	}
    	return;
    }

    /**
     * Move action for item controller
     * @throws Exception
     */
    public function moveAction()
    {
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$id = $this->_request->getParam( 'id' );
        	$direction = $this->_request->getParam('direction');
        	$mdlMenuItem = new menu_Model_Item();
        	$menuItem = $mdlMenuItem->find( $id )->current();
        	if ( !$menuItem )
        	{
        		throw new Exception($translate->translate("MENU_ITEM_NOT_FOUND"));
        	}
        	if ( !in_array($direction, array('down', 'up')) ) {
        		throw new Exception($translate->translate("LBL_UP_DOWN_NOT_SPECIFIED"));
        	}
        	if ( $direction == "up" )
        	{
        		$mdlMenuItem->moveUp($menuItem);
        		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_MOVED_UP_SUCCESSFULLY") ) );
        	} elseif ( $direction == "down" )
        	{
        		$mdlMenuItem->moveDown($menuItem);
        		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_MOVED_DOWN_SUCCESSFULLY") ) );
        	}
        	$this->_helper->redirector('list', 'item', 'menu', array('menu'=>$menuItem->menu_id));
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector('list', 'menu', 'menu');
        }
        return;
    }

    /**
     * Update action for item controller
     * @throws Exception
     * @return NULL
     */
    public function updateAction()
    {
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	
        	$id = $this->getRequest()->getParam('id', 0);
        	$mdlMenuItem = new menu_Model_Item();
        	$menuItem = $mdlMenuItem->find($id)->current();
        	if ( !$menuItem ) throw new Exception($translate->translate("LBL_ROW_NOT_FOUND"));
        	
        	$mdlResource = new Acl_Model_Resource();
        	$resource = $mdlResource->find( $menuItem->resource_id )->current();
        	
        	$menuFile = APPLICATION_PATH.'/modules/'.$resource->module.'/menus.xml';
        	if ( !file_exists( $menuFile ) ) {
        		throw new Exception($translate->translate("MENU_XML_FILE_NOT_FOUND"));
        	}
        	
        	$sxe = new SimpleXMLElement( $menuFile, null, true);
        	$element = null;
        	foreach( $sxe as $sxeMenuItem ) {
        		if ( $sxeMenuItem['id'] == $menuItem->mid ) {
        			$element = $sxeMenuItem;
        			break;
        		}
        	}
        	if ( !$element ) throw new Exception($translate->translate("MENU_XML_ITEM_ELEMENT_NOT_FOUND"));
        	
        	$frmMenuItem = ucfirst( strtolower( strval($element->module) ) ).'_Form_Menuitems';
        	$frmMenuItem = ( strtolower(strval($element->module)) == 'menu' ) ? 'menu_Form_Menuitems' : $frmMenuItem;
        	$frmMenuItem = new $frmMenuItem( array('menuType'=>strtolower(strval($element->menu_type))) );
        	
        	$mdlMenu = new menu_Model_Menu();
        	$menu = $mdlMenu->find( $menuItem->menu_id )->current();
        	if ( !$menu ) throw new Exception($translate->translate("MENU_NOT_FOUND"));
        	$this->view->menu = $menu;
        	
        	$cbParentItem = $frmMenuItem->getElement('parent_id');
        	$mdlMenuItem = new menu_Model_Item();
        	$menuItemList = $mdlMenuItem->getListItemsByMenu($menu);
        	$cbParentItem->addMultiOption( 0, $translate->translate("MENU_NOT_PARENT") );
        	foreach ( $menuItemList as $menuItemRow ) {
        		$cbParentItem->addMultiOption( $menuItemRow->id, $menuItemRow->title );
        	}
        	
        	if ( $this->getRequest()->isPost() )
        	{
        	    if ( $frmMenuItem->isValid( $_POST ) )
        	    {
        	        $menuItem->setFromArray( $frmMenuItem->getValues() );
        	        
        	        $frmMIValues = $frmMenuItem->getValues();
        	        $params = array();
        	        foreach ( $frmMIValues as $wvk => $wv )
        	        {
        	        	if ( !in_array($wvk, $frmMenuItem->defaultFormFields) )
        	        	{
        	        		$params[] = $wvk.'='.$wv.'';
        	        	}
        	        }
        	        $params = implode("\n", $params);
        	        $menuItem->params = $params;
        	        
        	        $parentItem = $mdlMenuItem->find( $menuItem->parent_id )->current();
        	        $menuItem->depth = $parentItem->depth+1;
        	        
        	        $menuItem->save();
        	        
        	        $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("MENU_ITEM_UPDATED_SUCCESSFULLY") ) );
        	        $this->_helper->redirector('list', 'item', 'menu', array('menu'=>$menu->id));
        	    } 
        	    else {
        	        #Zend_Debug::dump($frmMenuItem);
        	    }
        	} else {
        	    $frmMenuItem->populate( $menuItem->toArray() );
        	    $params = explode("\n", $menuItem->params);
        	    foreach ( $params as $strParam )
        	    {
        	    	$paramKey = substr($strParam, 0, strpos($strParam, "="));
        	    	#Zend_Debug::dump($paramKey);
        	    	$paramValue = substr($strParam, strpos($strParam, "=")+1, strlen($strParam));
        	    	#Zend_Debug::dump($paramValue);
        	    	$output[$paramKey] = $paramValue;
        	    	$frmMenuItem->populate( $output );
        	    }
        	    $frmMenuItem->populate ( array('mod'=>$resource->module) );
        	    
        	    $fields = array();
        	    foreach ( $frmMenuItem->getElements() as $element ) $fields[] = $element->getName();
        	    $frmMenuItem->addDisplayGroup( $fields, 'form', array( 'legend' => "MENU_UPDATE_MENUITEM", ) );
        	}
        	$frmMenuItem->setAction( $this->_request->getBaseUrl() . "/menu/item/update" );
        	$this->view->frmMenuItem = $frmMenuItem;
			
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector('list', 'item', 'menu', array('menu'=>$menu->id));
        }
        
        return;
    }

    /**
     * Publish action for item controller
     * @throws Exception
     */
    public function publishAction()
    {
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$mdlMenuItem = new menu_Model_Item();
        	$id = $this->getRequest()->getParam('id', 0);
        	$menuItem = $mdlMenuItem->find( $id )->current();
        	if ( !$menuItem ) {
        		throw new Exception($translate->translate("MENU_ITEM_NOT_FOUND"));
        	}
        	if ( $menuItem->published == 0 ) {
        	    
        	    $menuItem->published = 1;
        	    $menuItem->save();
        	    $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("MENU_ITEM_PUBLISHED_SUCCESSFULLY") ) );
        	} else {
        	    $menuItem->published = 0;
        	    $menuItem->save();
        	    $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("MENU_ITEM_UNPUBLISHED_SUCCESSFULLY") ) );
        	}
        	$this->_helper->redirector('list', 'item', 'menu', array('menu'=>$menuItem->menu_id));
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector('list', 'item', 'menu', array('menu'=>$menuItem->menu_id));
        }
        return;
    }

    /**
     * Delete action for item controller
     * @throws Exception
     * @return NULL
     */
    public function deleteAction()
    {
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$mdlMenuItem = new menu_Model_Item();
        	$id = $this->getRequest()->getParam('id', 0);
        	$menuItem = $mdlMenuItem->find( $id )->current();
        	if ( !$menuItem ) {
        		throw new Exception($translate->translate("MENU_ITEM_NOT_FOUND"));
        	}
        	$menuId = $menuItem->menu_id;
        	$menuItem->delete();
        	$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("MENU_ITEM_DELETED_SUCCESSFULLY") ) );
        	$this->_helper->redirector('list', 'item', 'menu', array('menu'=>$menuId));
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector('list', 'item', 'menu', array('menu'=>$menuId));
        }
        return;
    }

    /**
     * Choose action for item controller
     * @throws Exception
     * @return NULL
     */
    public function chooseAction()
    {
        // action body
        try {
            $translate = Zend_Registry::get('Zend_Translate');
            $menuId = $this->getRequest()->getParam('menu', 0);
            $mdlMenu = new menu_Model_Menu();
            $menu = $mdlMenu->find( $menuId )->current();
            if ( !$menu ) throw new Exception($translate->translate("MENU_ROW_NOT_FOUND"));
            $this->view->menu = $menu;
            
            $mdlResource = new Acl_Model_Resource();
            $modules = $mdlResource->getModules();
            $menus = array();
            foreach ( $modules as $module )
            {
            	#echo APPLICATION_PATH.'/modules/'.$module->module.'<br>';
            	$menuFile = APPLICATION_PATH.'/modules/'.$module->module.'/menus.xml';
            	if ( file_exists( $menuFile ) )
            	{
            		#echo "si en ".$module->module.'<br>';
            		$sxe = new SimpleXMLElement( $menuFile, null, true);
            		foreach( $sxe as $menuTemp ) {
            			#Zend_Debug::dump($widget);
            			$menus[] = $menuTemp;
            		}
            	}
            	#
            }
            $this->view->menuItemTypes = $menus;
            	
        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
            $this->_helper->redirector('list', 'item', 'menu', array('menu'=>$menuId));
        }
        return;
    }

    /**
     * Weblink action for item controller
     * @return NULL
     */
    public function weblinkAction()
    {
        // action body
        return;
    }


}



