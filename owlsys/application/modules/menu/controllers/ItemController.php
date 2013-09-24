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
        	$mdlMenuItem = new menu_Model_Item();
        	$menu = $mdlMenu->find($menuId)->current();
        	
        	$menuItems = $mdlMenuItem->getListByMenu($menu);
        	$paginator = Zend_Paginator::factory($menuItems);
	        $paginator->setItemCountPerPage(25);
	        $pageNumber = $this->getRequest()->getParam('page',1);
	        $paginator->setCurrentPageNumber($pageNumber);
	        $paginator->setCacheEnabled(true);
	        
	        $this->view->items = $paginator;
        	$this->view->menu = $menu;
        	$this->view->rows = $menuItems;
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('menus');
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
    	$menuId = null;
    	try {
    		$translate = Zend_Registry::get('Zend_Translate');
    		
    		$menuId = $this->getRequest()->getParam('menu', 0);
    		$mdlMenu = new menu_Model_Menu();
    		$menu = $mdlMenu->find($menuId)->current();
    		$this->view->menu = $menu;
    		$menuId = $menu->id;

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
    		$resource = $mdlResource->createRow();
    		$resource->module = $module;
    		$resource->controller = $element->controller;
    		$resource->actioncontroller = $element->action;
    		$resource = $mdlResource->getIdByDetail($resource);
    		
    		if ( !$resource ) throw new Exception($translate->translate("ACL_RESOURCE_NOT_FOUND"));
    		
    		$frmMenuItem = ucfirst( strtolower( strval($element->module) ) ).'_Form_Menuitems';
    		$frmMenuItem = ( strtolower(strval($element->module)) == 'menu' ) ? 'menu_Form_Menuitems' : $frmMenuItem;
    		/* @var $frmMenuItem menu_Form_Item */
    		$frmMenuItem = new $frmMenuItem( array('menuType'=>strtolower(strval($element->menu_type))) );
    		
    		$frmMenuItem->getElement('mid')->setValue( (int) $mid );
    		$frmMenuItem->getElement('mod')->setValue( strval($module) );
    		$frmMenuItem->getElement('menu')->setValue( $menuId );
    		$frmMenuItem->getElement('resource')->setValue( $resource->id );
    		$frmMenuItem->getElement('route')->setValue( $element->menu_type );
    		
    		$cbParentItem = $frmMenuItem->getElement('parent_id');
    		$mdlMenuItem = new menu_Model_Item();
    		$menuItemList = $mdlMenuItem->getListByMenu($menu);
    		$cbParentItem->addMultiOption( 0, $translate->translate("MENU_NOT_PARENT") );
    		if ( count($menuItemList) > 0 )
    		{
	    		foreach ( $menuItemList as $menuItemRow ) {
	    			$cbParentItem->addMultiOption( $menuItemRow->id, $menuItemRow->title );
	    		}
    		}
    		
    		$frmMenuItem->setAction( $this->_request->getBaseUrl() . "/menu-item-add/".$module.'/'.$mid.'/'.$menu->id );
    		$frmMenuItem->removeElement('id');
    		
    		if ( $this->getRequest()->isPost() )
    		{
    		    if ( $frmMenuItem->isValid( $this->getRequest()->getParams() ) )
    		    {
    		        
    		        $frmMIValues = $frmMenuItem->getValues();
					$menuItem = $mdlMenuItem->createRow();
					$menuItemParent = $mdlMenuItem->find($frmMenuItem->getValue('parent'))->current();
					
					if ( !$menuItemParent ) {
					  $menuItem->parent_id = $menuItemParent->id;
					  $menuItem->depth = $menuItemParent->depth+1;
					} else {
					  $menuItem->parent_id = null;
					  $menuItem->depth = 1;
					}
					
					$menuItem->css_class = $frmMenuItem->getValue('css_class');
					$menuItem->description = $frmMenuItem->getValue('description');
					$menuItem->external = $frmMenuItem->getValue('external');
					$menuItem->isPublished = $frmMenuItem->getValue('published');
					$menuItem->isVisible = $frmMenuItem->getValue('isvisible');
					$menuItem->menu_id = $menu->id;
					$menuItem->mid = $frmMenuItem->getValue('mid');
					$menuItem->resource_id = $resource->id;
					$menuItem->route = $frmMenuItem->getValue('route');
					$menuItem->title = $frmMenuItem->getValue('title');
					$menuItem->wtype = $frmMenuItem->getValue('wtype');
					$menuItem->parent_id = $frmMenuItem->getValue('parent_id');
					
					$params = array();
					foreach ( $frmMIValues as $wvk => $wv )
					{
						if ( !in_array($wvk, $frmMenuItem->defaultFormFields) )
						{
						    $params[$wvk] = $wv;
						}
					}
					$menuItem->params = Zend_Json::encode($params);
					$menuItem->save();
					
					$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("MENU_ITEM_ADDED_SUCCESSFULLY") ) );
					$this->redirect('menu-items/'.$menu->id);
    		    } 
    		} 
    		$this->view->frmMenuItem = $frmMenuItem;
    		$this->view->menuitem = $element;
    	}
    	catch (Exception $e) {
//     	    Zend_Debug::dump($e->getMessage());
//     	    Zend_Debug::dump($e->getTraceAsString());
//     	    die();
    		$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('menu-items/'.$menu->id);
    	}
    	return;
    }

    /**
     * Move action for item controller
     * @throws Exception
     */
    public function moveAction()
    {
        $id = null;
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$id = (int)$this->_request->getParam( 'id' );
        	$mdlMenuItem = new menu_Model_Item();
        	
        	$id = (int)$this->getRequest()->getParam('id', 0);
        	$direction = $this->_request->getParam('direction');
        	$menuItem = $mdlMenuItem->find($id)->current();
        	
        	if ( !in_array($direction, array('down', 'up')) ) {
        		throw new Exception($translate->translate("Unknown direction"));
        	}
        	
        	if ( $direction == "up" ) {
        		$mdlMenuItem->moveUp($menuItem);
        	} elseif ( $direction == "down" ) {
        		$mdlMenuItem->moveDown($menuItem);
        	}
        	$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("The item was moved") ) );
        	$this->redirect('menu-items/'.$menuItem->menu_id);
        } catch (Exception $e) {
            Zend_Debug::dump($e->getMessage());
    	    Zend_Debug::dump($e->getTraceAsString());
    	    die();
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('menus');
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
        	
        	$mdlMenu = new menu_Model_Menu();
        	$mdlMenuItem = new menu_Model_Item();
        	$mdlResource = new Acl_Model_Resource();
        	
        	$id = (int)$this->getRequest()->getParam('id', 0);
        	$direction = $this->_request->getParam('direction');
        	$menuItem = $mdlMenuItem->find($id)->current();
        	
        	$resource = $mdlResource->find($menuItem->resource_id)->current();
        	
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
        	/* @var $frmMenuItem menu_Form_Item */
        	$frmMenuItem = new $frmMenuItem( array('menuType'=>strtolower(strval($element->menu_type))) );
        	$frmMenuItem->removeElement('menu_id');
        	$frmMenuItem->removeElement('resource_id');
        	
        	$menu = $mdlMenu->find($menuItem->menu_id)->current();
        	$this->view->menu = $menu;
        	
        	$cbParentItem = $frmMenuItem->getElement('parent_id');
        	$menuItemList = $mdlMenuItem->getAllByMenu($menu);
        	$cbParentItem->addMultiOption( 0, $translate->translate("MENU_NOT_PARENT") );
        	if ( count($menuItemList) > 0 ) {
            	foreach ( $menuItemList as $menuItemRow ) {
            		$cbParentItem->addMultiOption( $menuItemRow->id, $menuItemRow->title );
            	}
        	}
        	
        	if ( $this->getRequest()->isPost() )
        	{
        	    if ( $frmMenuItem->isValid( $_POST ) )
        	    {
        	        
        	        $frmMIValues = $frmMenuItem->getValues();
        	        
        	        $menuItem->css_class = $frmMenuItem->getValue('css_class');
        	        $menuItem->description = $frmMenuItem->getValue('description');
        	        $menuItem->external = $frmMenuItem->getValue('external');
        	        $menuItem->isPublished = $frmMenuItem->getValue('published');
        	        $menuItem->isVisible = $frmMenuItem->getValue('isvisible');
        	        $menuItem->mid = $frmMenuItem->getValue('mid');
        	        $menuItem->route = $frmMenuItem->getValue('route');
        	        $menuItem->title = $frmMenuItem->getValue('title');
        	        $menuItem->wtype = $frmMenuItem->getValue('wtype');
        	        $menuItem->parent_id = $frmMenuItem->getValue('parent_id');
        	        
        	        $params = array();
        	        foreach ( $frmMIValues as $wvk => $wv )
        	        {
        	        	if ( !in_array($wvk, $frmMenuItem->defaultFormFields) )
        	        	{
        	        	    $params[$wvk] = $wv;
        	        	}
        	        }
        	        $menuItem->params = Zend_Json::encode($params);
					$menuItem->save();
        	        
        	        $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("Menu item updated") ) );
        	        $this->redirect('menu-items/'.$menu->id);
        	    } else {
        	      Zend_Debug::dump( $frmMenuItem->getMessages() );
        	    }
        	    
        	} else {
        	    $data = $menuItem->toArray();
        	    $values = array(
        	            'id' => $menuItem->id,
        	            'route' => $menuItem->route,
                        'parent_id' => $menuItem->parent_id,
                        'wtype' => $menuItem->wtype,
                        'published' => $menuItem->isPublished,
                        'title' => $menuItem->title,
                        'description' => $menuItem->description,
                        'external' => $menuItem->external,
                        'mid' => $menuItem->mid,
                        'isvisible' => $menuItem->isVisible,
                        'css_class' => $menuItem->css_class,
        	    );
        	    $frmMenuItem->populate($values);
        	    if ( strlen($menuItem->params) > 0 ) {
        	        $params = Zend_Json::decode($menuItem->params);
        	        $frmMenuItem->populate( $params );
        	    }
        	    $frmMenuItem->populate ( array('mod'=>$resource->module) );
        	}
        	$frmMenuItem->setAction( $this->_request->getBaseUrl() . "/menu-item-update/".$menuItem->id );
        	$this->view->frmMenuItem = $frmMenuItem;
			
        } catch (Exception $e) {
            Zend_Debug::dump($e->getMessage());
    	    Zend_Debug::dump($e->getTraceAsString());
    	    die();
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('menu-items/'.$menu->id);
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
        	
        	$menuItem = $mdlMenuItem->find($id)->current();
        	
        	if ( $menuItem->isPublished == 0 ) {
        	    $menuItem->isPublished = 1;
        	} else {
        	    $menuItem->isPublished = 0;
        	}
        	$menuItem->save();
        	
        	$this->redirect('menu-items/'.$menuItem->menu_id);
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('menu-items/'.$menuItem->id);
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
        $id = 0;
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$mdlMenuItem = new menu_Model_Item();
        	$id = (int)$this->getRequest()->getParam('id', 0);
        	
        	$menuItem = $mdlMenuItem->find($id)->current();
        	
        	$menuId = $menuItem->menu_id;
        	$menuItem->delete();
        	
        	$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("Menu item deleted") ) );
        	$this->redirect('menu-items/'.$menuId);
        } catch (Exception $e) {
//             Zend_Debug::dump($e->getMessage());
//     	    Zend_Debug::dump($e->getTraceAsString());
//     	    die();
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('menu-items/'.$menuId);
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
            $mdlResource = new Acl_Model_Resource();
            
            $menu = $mdlMenu->find($menuId)->current();
            $this->view->menu = $menu;
            
            $resources = $mdlResource->getAll();
            $modules = array();
            foreach ( $resources as $resource ) {
                if ( !in_array($resource->module, $modules) ) {
                    $modules[] = $resource->module;
                }
            }
            
            $menus = array();
            foreach ( $modules as $module )
            {
            	#echo APPLICATION_PATH.'/modules/'.$module->module.'<br>';
            	$menuFile = APPLICATION_PATH.'/modules/'.$module.'/menus.xml';
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
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
            $this->redirect('menu-items/'.$menuId);
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



