<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package system
 * @subpackage controllers
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class System_WidgetController extends Zend_Controller_Action
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
     * index action for widget controller
     */
    public function indexAction()
    {
        // action body
    }

    /**
     * choose action for widget controller
     */
    public function chooseAction()
    {
        try {
        	$mdlResource = Acl_Model_ResourceMapper::getInstance();
        	$resources = $mdlResource->getAll();
        	$modules = array();
        	foreach ( $resources as $resource ) {
        	    if ( !in_array($resource->getModule(), $modules) ) {
        	        $modules[] = $resource->getModule();
        	    }
        	}
        	
        	$widgets = array();
        	foreach ( $modules as $module )
        	{
        	    $widgetFile = APPLICATION_PATH.'/modules/'.$module.'/widgets.xml';
        	    if ( file_exists( $widgetFile ) )
        	    {
        	        $sxe = new SimpleXMLElement( $widgetFile, null, true);
        	        foreach( $sxe as $widget ) {
        	           $widgets[] = $widget; 
        	        }
        	    }
        	    #
        	}
        	$this->view->widgets = $widgets;
        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
            $this->redirect('widgets');
        }
        return;
    }

    /**
     * list action for widget controller
     */
    public function listAction()
    {
        try {
        	$mdlWidget = System_Model_WidgetMapper::getInstance();
        	$paginator = Zend_Paginator::factory($mdlWidget->getList());
        	$paginator->setItemCountPerPage(10);
        	$pageNumber = $this->getRequest()->getParam('page',1);
        	$paginator->setCurrentPageNumber($pageNumber);
        	$this->view->widgets = $paginator;
        } catch (Exception $e) {
        	echo $e->getMessage();
        }
        return;
    }

    /**
     * new action for widget controller
     * @throws Exception
     */
    public function newAction()
    {
        // action body
        try {
            $translate = Zend_Registry::get('Zend_Translate');
            $module = $this->getRequest()->getParam('mod');
            $wid = $this->getRequest()->getParam('wid');
            $widgetFile = APPLICATION_PATH.'/modules/'.$module.'/widgets.xml';
            if ( !file_exists( $widgetFile ) ) {
                throw new Exception($translate->translate("SYSTEM_WIDGET_NOT_FOUND"));
            }

            $sxe = new SimpleXMLElement( $widgetFile, null, true);
			$element = null;
			foreach( $sxe as $widget ) {
				if ( $widget['id'] == $wid ) {
					$element = $widget;
					break;
				}
			}
			if ( !$element ) throw new Exception($translate->translate("SYSTEM_WIDGET_ELEMENT_NOT_FOUND"));
			
			$mdlResource = Acl_Model_ResourceMapper::getInstance();
			$resource = new Acl_Model_Resource();
			$resource->setModule($module)
    		  ->setController($element->controller)
    		  ->setActioncontroller($element->action)
    		;
    		$mdlResource->getIdByDetail($resource);
			
			if ( !$resource ) throw new Exception($translate->translate("ACL_RESOURCE_NOT_FOUND"));
			
			$frmWidget = ucfirst( strtolower( strval($element->module) ) ).'_Form_Widgets';
			$frmWidget = ( strtolower(strval($element->module)) == 'menu' ) ? 'menu_Form_Widgets' : $frmWidget;
			/* @var $frmWidget System_Form_Widget */
			$frmWidget = new $frmWidget( array('widgetType'=>strtolower(strval($element->widget_type))) );

			$frmWidget->getElement('wid')->setValue( (int) $wid );
			$frmWidget->getElement('mod')->setValue( strval($module) );
			
			$hookXml = APPLICATION_PATH.'/configs/hooks.xml';
			$sxeHook = new SimpleXMLElement( $hookXml, null, true);
			$cbPosition = $frmWidget->getElement("position");
			foreach ( $sxeHook as $hook ) {
			    $cbPosition->addMultiOption( $hook, $hook );
			}
			
			$mdlMenu = menu_Model_MenuMapper::getInstance();
			$mdlMI = menu_Model_ItemMapper::getInstance();
			$menus = $mdlMenu->getList();
			$cbMenuItem = $frmWidget->getElement('menuitem');
			$menus = $mdlMenu->getByStatus(1);
			foreach ( $menus as $menu ) {
			    $mdlMI->getByMenu($menu);
			    if ( $menu->getChildren() > 0 ) {
			        foreach ( $menu->getChildren() as $menuItem ) {
			            /* @var $menuItem menu_Model_Item */
			            $mdlMI->getMenuItemsRecursively($menuItem);
			        }
			    }
			}
			foreach ( $menus as $menu )
			{
			    $menuItemData = array();
			    if ( $menu->getChildren() > 0 ) {
			        foreach ( $menu->getChildren() as $menuItem ) {
			            $this->_loadMenuItems($menuItem, $menuItemData);
			        }
			    }
			    $cbMIData[$menu->name] = $menuItemData;
			    $cbMenuItem->addMultiOptions( $cbMIData );
			}
			
			$frmWidget->setAction( $this->_request->getBaseUrl() . "/widget-new/".$module.'/'.$wid );
			$frmWidget->removeElement('id');
			
			$this->view->frmWidget = $frmWidget;
			$this->view->widget = $element;
			
			if ( $this->getRequest()->isPost() )
			{
			    if ( $frmWidget->isValid( $this->getRequest()->getParams() ) )
			    {
			        $defaultFormFields = array('id', 'wid', 'mod', 'position', 'title', 'published', 'menuitem', 'token', 'showtitle');
			        $mdlWidget = System_Model_WidgetMapper::getInstance();
			        $mdlWidgetDetail = System_Model_WidgetdetailMapper::getInstance();
			        $widget = new System_Model_Widget();
			        
			        $frmWidgetValues = $frmWidget->getValues();
			        $params = array();
			        foreach ( $frmWidgetValues as $wvk => $wv )
			        {
			            if ( !in_array($wvk, $defaultFormFields) )
			            {
			                $params[] = array($wvk=>$wv);
			            }
			        }
			        
			        $widget->setIsPublished($frmWidget->getValue('published'));
			        $widget->setPosition($frmWidget->getValue('position'));
			        $widget->setTitle($frmWidget->getValue('title'));
			        $widget->setShowtitle($frmWidget->getValue('showtitle'));
			        $widget->setWid($frmWidget->getValue('wid'));
			        $widget->setResource($resource);
			        $widget->setParams( Zend_Json::encode($params) );
			        
			        $mdlWidget->save($widget);
			        
			        if ( $frmWidget->getElement('renderfor')->getValue() == 0 )
			        {
			            $widgetDetail = new System_Model_Widgetdetail();
			            $widgetDetail->setWidget($widget);
			            $widgetDetail->setMenuItem(null);
			            $mdlWidgetDetail->save($widgetDetail);
			        }else {
				        foreach ( $frmWidget->getValue('menuitem') as $mi )
				        {
				            $widgetDetail = new System_Model_Widgetdetail();
				            $widgetDetail->setWidget($widget);
				            $menuItem = new menu_Model_Item();
				            $menuItem->setId($mi);
				            $widgetDetail->setMenuItem($menuItem);
				            $mdlWidgetDetail->save($widgetDetail);
				        }
			        }
			        
			        $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("New widget added") ) );
			        $this->redirect('widgets');
			    }
			}

        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
            $this->redirect('widgets');
        }
        return;
    }
    
    private function _loadMenuItems(menu_Model_Item $menuItem, &$data, $level=0)
    {
        $data[$menuItem->getId()] = str_repeat('-', $level).' '.$menuItem->getTitle();
        if ( count($menuItem->getChildren()) > 0 ) {
            foreach ( $menuItem->getChildren() as $child )
                $this->_loadMenuItems($child, $data, $level++);
        }
    }

    /**
     * update action for widget controller
     * @throws Exception
     */
    public function updateAction()
    {
        try {
            $translate = Zend_Registry::get('Zend_Translate');
            
            $id = $this->getRequest()->getParam('id', 0);
            $mdlWidget = System_Model_WidgetMapper::getInstance();
            $widget = new System_Model_Widget();
            $mdlWidget->find($id, $widget);

            $mdlResource = Acl_Model_ResourceMapper::getInstance();
            $resource = new Acl_Model_Resource();
            $mdlResource->find($widget->getResource()->getId(), $resource);
            
            $widgetFile = APPLICATION_PATH.'/modules/'.$resource->getModule().'/widgets.xml';
            if ( !file_exists( $widgetFile ) ) {
            	throw new Exception($translate->translate("SYSTEM_WIDGET_NOT_FOUND"));
            }
            
            $sxe = new SimpleXMLElement($widgetFile, null, true);
            $element = null;
            foreach( $sxe as $widgetElement ) {
            	if ( $widgetElement['id'] == $widget->getWid() ) {
            		$element = $widgetElement;
            		break;
            	}
            }
            
            if ( !$element ) throw new Exception($translate->translate("SYSTEM_WIDGET_ELEMENT_NOT_FOUND"));
            
            $frmWidget = ucfirst( strtolower( strval($element->module) ) ).'_Form_Widgets';
            $frmWidget = ( strtolower(strval($element->module)) == 'menu' ) ? 'menu_Form_Widgets' : $frmWidget;
            $frmWidget = new $frmWidget( array('widgetType'=>strtolower(strval($element->widget_type))) );
			
			$hookXml = APPLICATION_PATH.'/configs/hooks.xml';
			$sxeHook = new SimpleXMLElement( $hookXml, null, true);
			$cbPosition = $frmWidget->getElement("position");
			foreach ( $sxeHook as $hook ) {
				$cbPosition->addMultiOption( $hook, $hook );
			}
			
            $mdlMenu = menu_Model_MenuMapper::getInstance();
			$mdlMI = menu_Model_ItemMapper::getInstance();
			$menus = $mdlMenu->getList();
			$cbMenuItem = $frmWidget->getElement('menuitem');
			$menus = $mdlMenu->getByStatus(1);
			foreach ( $menus as $menu ) {
			    $mdlMI->getByMenu($menu);
			    if ( $menu->getChildren() > 0 ) {
			        foreach ( $menu->getChildren() as $menuItem ) {
			            /* @var $menuItem menu_Model_Item */
			            $mdlMI->getMenuItemsRecursively($menuItem);
			        }
			    }
			}
			foreach ( $menus as $menu )
			{
			    $menuItemData = array();
			    if ( $menu->getChildren() > 0 ) {
			        foreach ( $menu->getChildren() as $menuItem ) {
			            $this->_loadMenuItems($menuItem, $menuItemData);
			        }
			    }
			    $cbMIData[$menu->name] = $menuItemData;
			    $cbMenuItem->addMultiOptions( $cbMIData );
			}
			
			$frmWidget->setAction( $this->_request->getBaseUrl() . "/widget-update/".$widget->id );
			$frmWidget->populate( $widget->toArray() );
			
			$mdlWidgetDetail = System_Model_WidgetdetailMapper::getInstance();
			$menuItemsRegistered = $mdlWidgetDetail->getByWidget($widget);
			if ( $menuItemsRegistered !==  false ) {
			    $frmWidget->populate( array('menuitem' => $menuItemsRegistered) );
			    $frmWidget->getElement('renderfor')->setValue(1);
			} else {
			    $frmWidget->getElement('renderfor')->setValue(0);
			    $frmWidget->getElement('menuitem')->setAttrib('disabled', true);
			}
			
			$params = Zend_Json::decode($widget->params);
			foreach ( $params as $param ) {
			    $frmWidget->populate( $param );
			}
			
			if ( $this->getRequest()->isPost() )
			{
			    if ( $frmWidget->isValid( $this->getRequest()->getParams() ) )
			    {
			        $adapter = $mdlWidget->getAdapter();
			        $adapter->beginTransaction();
			        
			        $defaultFormFields = array('id', 'wid', 'mod', 'position', 'title', 'published', 'menuitem', 'token', 'showtitle');
			         
			        $frmWidgetValues = $frmWidget->getValues();
			        $params = array();
			        foreach ( $frmWidgetValues as $wvk => $wv )
			        {
			            if ( !in_array($wvk, $defaultFormFields) )
			            {
			                $params[] = array($wvk=>$wv);
			            }
			        }
			        
			        $widget->setIsPublished($frmWidget->getValue('published'));
			        $widget->setTitle($frmWidget->getValue('title'));
			        $widget->setShowtitle($frmWidget->getValue('showtitle'));
			        $widget->setParams( Zend_Json::encode($params) );
			        
			        $mdlWidget->save($widget);
			        $mdlWidgetDetail->delete($widget);
			        if ( $frmWidget->getElement('renderfor')->getValue() == 0 )
			        {
			            $widgetDetail = new System_Model_Widgetdetail();
			            $widgetDetail->setWidget($widget);
			            $widgetDetail->setMenuItem(null);
			            $mdlWidgetDetail->save($widgetDetail);
			        }else {
			            foreach ( $frmWidget->getValue('menuitem') as $mi )
			            {
			                $widgetDetail = new System_Model_Widgetdetail();
			                $widgetDetail->setWidget($widget);
			                $menuItem = new menu_Model_Item();
			                $menuItem->setId($mi);
			                $widgetDetail->setMenuItem($menuItem);
			                $mdlWidgetDetail->save($widgetDetail);
			            }
			        }
			        
			        // old
// 			        $defaultFormFields = array('id', 'wid', 'mod', 'position', 'title', 'published', 'menuitem', 'token', 'widget_id', 'showtitle');
// 			        $widget->title = $frmWidget->getElement('title')->getValue();
// 			        $widget->published = $frmWidget->getElement('published')->getValue();
// 			        $widget->position = $frmWidget->getElement('position')->getValue();
// 			        $widget->showtitle = $frmWidget->getElement('showtitle')->getValue();
			        
// 			        $frmWidgetValues = $frmWidget->getValues();
// 			        $params = array();
// 			        foreach ( $frmWidgetValues as $wvk => $wv )
// 			        {
// 			        	if ( !in_array($wvk, $defaultFormFields) )
// 			        	{
// 			        	    $params[$wvk] = $wv; 
// 			        	}
// 			        }
// 			        $widget->params = Zend_Json::encode($params);
			        
// 			        $widget->save();
			        
// 			        $menuItemsWidget = $widget->findDependentRowset('System_Model_Widgetdetail', 'Widget');
// 			        foreach ( $menuItemsWidget as $miw ) $miw->delete();
			        
// 			        if ( $frmWidget->getElement('renderfor')->getValue() == 0 )
// 			        {
// 			        	$widgetDetail = $mdlWidgetDetail->createRow();
// 			        	$widgetDetail->widget_id = $widget->id;
// 			        	$widgetDetail->menuitem_id = null;
// 			        	$widgetDetail->save();
// 			        } else {
// 			        	foreach ( $frmWidget->getValue('menuitem') as $mi )
// 			        	{
// 			        		$widgetDetail = $mdlWidgetDetail->createRow();
// 			        		$widgetDetail->widget_id = $widget->id;
// 			        		$widgetDetail->menuitem_id = $mi;
// 			        		$widgetDetail->save();
// 			        	}
// 			        }
			        $adapter->commit();
			        $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("Changes saved") ) );
			        $this->redirect('widgets');
			    }
			}
			
			$this->view->frmWidget = $frmWidget;
			$this->view->widget = $element;
            
        } catch (Exception $e) {
            Zend_Debug::dump($e->getMessage());
            Zend_Debug::dump($e->getTraceAsString());
            die();
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
            $this->redirect('widgets');
        } 
        return;
    }

    /**
     * publish action for widget controller
     * @throws Exception
     */
    public function publishAction()
    {
        // action body
        try {
            $translate = Zend_Registry::get('Zend_Translate');
            $id = $this->getRequest()->getParam('id', 0);
            $mdlWidget = System_Model_WidgetMapper::getInstance();
            $widget = new System_Model_Widget();
            $mdlWidget->find($id, $widget);
            
            if ( $widget->getIsPublished() == 0 ) {
                $widget->setIsPublished(1);
        	} else {
        	    $widget->setIsPublished(0);
        	}
        	$mdlWidget->save($widget);
        	$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("Changes saved") ) );
        	$this->redirect('widgets');
        } catch (Exception $e) {
//             Zend_Debug::dump($e->getMessage());
//     	    Zend_Debug::dump($e->getTraceAsString());
//     	    die();
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
            $this->redirect('widgets');
        }
        return;
    }

    /**
     * delete action for widget controller
     * @throws Exception
     */
    public function deleteAction()
    {
    	try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$id = $this->getRequest()->getParam('id', 0);
            $mdlWidget = System_Model_WidgetMapper::getInstance();
            $widget = new System_Model_Widget();
            $widget->setId($id);
        	$mdlWidget->remove($widget);
        	$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("The item was removed.") ) );
        	$this->redirect('widgets');
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('widgets');
        }
        return;
    }

    /**
     * move action for widget controller
     * @throws Exception
     */
    public function moveAction()
    {
    	try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$id = $this->_request->getParam( 'id' );
        	$direction = $this->_request->getParam('direction');
        	$mdlWidget = System_Model_WidgetMapper::getInstance();
            $widget = new System_Model_Widget();
            $mdlWidget->find($id, $widget);
        	
        	if ( !in_array($direction, array('down', 'up')) ) {
        		throw new Exception($translate->translate("Direction not found"));
        	}
        	if ( $direction == "up" ) {
        		$mdlWidget->moveUp($widget);
        	} elseif ( $direction == "down" ) {
        		$mdlWidget->moveDown($widget);
        	}
        	$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("The widget was moved") ) );
        	$this->redirect('widgets');
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('widgets');
        }
    }


}

