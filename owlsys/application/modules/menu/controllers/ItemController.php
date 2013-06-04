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
        	$mdlMenu = menu_Model_MenuMapper::getInstance();
        	$mdlMenuItem = menu_Model_ItemMapper::getInstance();
        	$menu = new menu_Model_Menu();
        	$mdlMenu->find($menuId, $menu);
        	
        	$menuItems = $mdlMenuItem->getListByMenu($menu);
        	$paginator = Zend_Paginator::factory($menuItems);
	        $paginator->setItemCountPerPage(25);
	        $pageNumber = $this->getRequest()->getParam('page',1);
	        $paginator->setCurrentPageNumber($pageNumber);
	        
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
    		$mdlMenu = new menu_Model_MenuMapper();
    		$menu = new menu_Model_Menu();
    		$mdlMenu->find($menuId, $menu);
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
    		
    		$mdlResource = Acl_Model_ResourceMapper::getInstance();
    		$resource = new Acl_Model_Resource();
    		$resource->setModule($module)
    		  ->setController($element->controller)
    		  ->setActioncontroller($element->action)
    		;
    		$mdlResource->getIdByDetail($resource);
    		
    		if ( !$resource ) throw new Exception($translate->translate("ACL_RESOURCE_NOT_FOUND"));
    		
    		$frmMenuItem = ucfirst( strtolower( strval($element->module) ) ).'_Form_Menuitems';
    		$frmMenuItem = ( strtolower(strval($element->module)) == 'menu' ) ? 'menu_Form_Menuitems' : $frmMenuItem;
    		/* @var $frmMenuItem menu_Form_Item */
    		$frmMenuItem = new $frmMenuItem( array('menuType'=>strtolower(strval($element->menu_type))) );
    		
    		$frmMenuItem->getElement('mid')->setValue( (int) $mid );
    		$frmMenuItem->getElement('mod')->setValue( strval($module) );
    		$frmMenuItem->getElement('menu')->setValue( $menuId );
    		$frmMenuItem->getElement('resource')->setValue( $resource->id );
    		
    		$cbParentItem = $frmMenuItem->getElement('parent');
    		$mdlMenuItem = menu_Model_ItemMapper::getInstance();
    		$mdlMenuItem->getByMenu($menu);
    		$menuItemList = $menu->getChildren();
    		$cbParentItem->addMultiOption( 0, $translate->translate("MENU_NOT_PARENT") );
    		if ( count($menuItemList) > 0 )
    		{
	    		foreach ( $menuItemList as $menuItemRow ) {
	    			$cbParentItem->addMultiOption( $menuItemRow->getId(), $menuItemRow->getTitle() );
	    		}
    		}
    		
    		$frmMenuItem->setAction( $this->_request->getBaseUrl() . "/menu-item-add/".$module.'/'.$mid.'/'.$menu->getId() );
    		$frmMenuItem->removeElement('id');
    		
    		if ( $this->getRequest()->isPost() )
    		{
    		    if ( $frmMenuItem->isValid( $this->getRequest()->getParams() ) )
    		    {
    		        
    		        $frmMIValues = $frmMenuItem->getValues();
					$menuItem = new menu_Model_Item();
					$menuItemParent = new menu_Model_Item();
					
					$menuItemParent->setId( $frmMenuItem->getValue('parent') );
					$menuItem->setDepth( $mdlMenuItem->find($frmMenuItem->getValue('parent'), $menuItemParent) ? $menuItemParent->getDepth()+1 : 1 );
					$menuItemParent->setId( ($menuItemParent->getId()>0) ? $menuItemParent->getId() : null);
					$menuItem->setParent( $menuItemParent );
					$menuItem->setCssClass( $frmMenuItem->getValue('css_class') );
					$menuItem->setDescription( $frmMenuItem->getValue('description') );
					$menuItem->setExternal($frmMenuItem->getValue('external'));
					$menuItem->setIsPublished($frmMenuItem->getValue('published'));
					$menuItem->setIsVisible($frmMenuItem->getValue('isvisible'));
					$menuItem->setMenu($menu);
					$menuItem->setMid($frmMenuItem->getValue('mid'));
					$menuItem->setResource($resource);
					$menuItem->setRoute($frmMenuItem->getValue('route'));
					$menuItem->setTitle($frmMenuItem->getValue('title'));
					$menuItem->setWtype($frmMenuItem->getValue('wtype'));
					
					$params = array();
					foreach ( $frmMIValues as $wvk => $wv )
					{
						if ( !in_array($wvk, $frmMenuItem->defaultFormFields) )
						{
						    $params[$wvk] = $wv;
						}
					}
					$menuItem->setParams(Zend_Json::encode($params));
					
					$mdlMenuItem->save($menuItem);
					
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
        	$mdlMenuItem = menu_Model_ItemMapper::getInstance();
        	$menuItem = new menu_Model_Item();
        	$id = (int)$this->getRequest()->getParam('id', 0);
        	$direction = $this->_request->getParam('direction');
        	$mdlMenuItem->find($id, $menuItem);
        	
        	if ( !in_array($direction, array('down', 'up')) ) {
        		throw new Exception($translate->translate("Direction unknowed"));
        	}
        	
        	if ( $direction == "up" ) {
        		$mdlMenuItem->moveUp($menuItem);
        	} elseif ( $direction == "down" ) {
        		$mdlMenuItem->moveDown($menuItem);
        	}
        	$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("The item was moved") ) );
        	$this->redirect('menu-items/'.$menuItem->getMenu()->getId());
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
        	
        	$mdlMenuItem = menu_Model_ItemMapper::getInstance();
        	$menuItem = new menu_Model_Item();
        	$id = (int)$this->getRequest()->getParam('id', 0);
        	$direction = $this->_request->getParam('direction');
        	$mdlMenuItem->find($id, $menuItem);
        	
        	$mdlResource = new Acl_Model_ResourceMapper();
        	$resource = new Acl_Model_Resource();
        	$mdlResource->find($menuItem->getResource()->getId(), $resource);
        	
        	$menuFile = APPLICATION_PATH.'/modules/'.$resource->getModule().'/menus.xml';
        	if ( !file_exists( $menuFile ) ) {
        		throw new Exception($translate->translate("MENU_XML_FILE_NOT_FOUND"));
        	}
        	
        	$sxe = new SimpleXMLElement( $menuFile, null, true);
        	$element = null;
        	foreach( $sxe as $sxeMenuItem ) {
        		if ( $sxeMenuItem['id'] == $menuItem->getMid() ) {
        			$element = $sxeMenuItem;
        			break;
        		}
        	}
        	if ( !$element ) throw new Exception($translate->translate("MENU_XML_ITEM_ELEMENT_NOT_FOUND"));
        	
        	$frmMenuItem = ucfirst( strtolower( strval($element->module) ) ).'_Form_Menuitems';
        	$frmMenuItem = ( strtolower(strval($element->module)) == 'menu' ) ? 'menu_Form_Menuitems' : $frmMenuItem;
        	/* @var $frmMenuItem menu_Form_Item */
        	$frmMenuItem = new $frmMenuItem( array('menuType'=>strtolower(strval($element->menu_type))) );
        	
        	$mdlMenu = menu_Model_MenuMapper::getInstance();
        	$menu = new menu_Model_Menu();
        	$mdlMenu->find($menuItem->getMenu()->getId(), $menu);
        	$this->view->menu = $menu;
        	
        	$cbParentItem = $frmMenuItem->getElement('parent');
        	$mdlMenuItem->getByMenu($menu);
        	$menuItemList = $menu->getChildren();
        	$cbParentItem->addMultiOption( 0, $translate->translate("MENU_NOT_PARENT") );
        	if ( count($menuItemList) > 0 ) {
            	foreach ( $menuItemList as $menuItemRow ) {
            		$cbParentItem->addMultiOption( $menuItemRow->getId(), $menuItemRow->getTitle() );
            	}
        	}
        	
        	if ( $this->getRequest()->isPost() )
        	{
        	    if ( $frmMenuItem->isValid( $_POST ) )
        	    {
        	        
        	        $frmMIValues = $frmMenuItem->getValues();
        	        $menuItem->setCssClass( $frmMenuItem->getValue('css_class') );
        	        $menuItem->setDescription( $frmMenuItem->getValue('description') );
        	        $menuItem->setExternal($frmMenuItem->getValue('external'));
        	        $menuItem->setIsPublished($frmMenuItem->getValue('published'));
        	        $menuItem->setIsVisible($frmMenuItem->getValue('isvisible'));
        	        $menuItem->setMid($frmMenuItem->getValue('mid'));
        	        $menuItem->setRoute($frmMenuItem->getValue('route'));
        	        $menuItem->setTitle($frmMenuItem->getValue('title'));
        	        $menuItem->setWtype($frmMenuItem->getValue('wtype'));
        	        
        	        $frmMIValues = $frmMenuItem->getValues();
        	        $params = array();
        	        foreach ( $frmMIValues as $wvk => $wv )
        	        {
        	        	if ( !in_array($wvk, $frmMenuItem->defaultFormFields) )
        	        	{
        	        	    $params[$wvk] = $wv;
        	        	}
        	        }
        	        $menuItem->setParams(Zend_Json::encode($params));
        	        
        	        $mdlMenuItem->save($menuItem);
        	        
        	        $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("Menu item updated") ) );
        	        $this->redirect('menu-items/'.$menu->id);
        	    } 
        	    
        	} else {
        	    $data = $menuItem->toArray();
        	    $values = array(
        	            'id' => $menuItem->getId(),
        	            'route' => $menuItem->getRoute(),
                        'menu' => $menuItem->getMenu()->getId(),
                        'resource' => $menuItem->getResource()->getId(),
                        'parent' => $menuItem->getParent()->getId(),
                        'wtype' => $menuItem->getWtype(),
                        'published' => $menuItem->getIsPublished(),
                        'title' => $menuItem->getTitle(),
                        'description' => $menuItem->getDescription(),
                        'external' => $menuItem->getExternal(),
                        'mid' => $menuItem->getMid(),
                        'isvisible' => $menuItem->getIsVisible(),
                        'css_class' => $menuItem->getCssClass(),
        	    );
        	    $frmMenuItem->populate($values);
        	    if ( strlen($menuItem->getParams()) > 0 ) {
        	        $params = Zend_Json::decode($menuItem->getParams());
        	        $frmMenuItem->populate( $params );
        	    }
        	    $frmMenuItem->populate ( array('mod'=>$resource->getModule()) );
        	}
        	$frmMenuItem->setAction( $this->_request->getBaseUrl() . "/menu-item-update/".$menuItem->getId() );
        	$this->view->frmMenuItem = $frmMenuItem;
			
        } catch (Exception $e) {
            Zend_Debug::dump($e->getMessage());
    	    Zend_Debug::dump($e->getTraceAsString());
    	    die();
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('menu-items/'.$menu->getId());
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
        	$mdlMenuItem = menu_Model_ItemMapper::getInstance();
        	$menuItem = new menu_Model_Item();
        	$id = $this->getRequest()->getParam('id', 0);
        	
        	$mdlMenuItem->find($id, $menuItem);
        	
        	if ( $menuItem->getIsPublished() == 0 ) {
        	    $menuItem->setIsPublished(1);
        	} else {
        	    $menuItem->setIsPublished(0);
        	}
        	
        	$mdlMenuItem->save($menuItem);
        	
        	$this->redirect('menu-items/'.$menuItem->getMenu()->getId());
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('menu-items/'.$menuItem->getMenu()->getId());
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
        	$mdlMenuItem = menu_Model_ItemMapper::getInstance();
        	$menuItem = new menu_Model_Item();
        	$id = (int)$this->getRequest()->getParam('id', 0);
        	
        	$mdlMenuItem->find($id, $menuItem);
        	
        	$menuId = $menuItem->getMenu()->getId();
        	$mdlMenuItem->remove($menuItem);
        	
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
            $mdlMenu = menu_Model_MenuMapper::getInstance();
            $menu = new menu_Model_Menu();
            $mdlMenu->find($menuId, $menu);
            $this->view->menu = $menu;
            
            $mdlResource = Acl_Model_ResourceMapper::getInstance();
            $resources = $mdlResource->getAll();
            $modules = array();
            foreach ( $resources as $resource ) {
                if ( !in_array($resource->getModule(), $modules) ) {
                    $modules[] = $resource->getModule();
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



