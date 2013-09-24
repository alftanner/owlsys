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
    }

    /**
     * choose action for widget controller
     */
    public function chooseAction()
    {
        try {
        	$mdlResource = new Acl_Model_Resource();
        	$resources = $mdlResource->getAll();
        	$modules = array();
        	foreach ( $resources as $resource ) {
        	    if ( !in_array($resource->module, $modules) ) {
        	        $modules[] = $resource->module;
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
        	$mdlWidget = new System_Model_Widget();
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
      $mdlResource = new Acl_Model_Resource();
      $mdlMenu = new menu_Model_Menu();
      $mdlMI = new menu_Model_Item();
      $mdlWidget = new System_Model_Widget();
      $mdlWidgetDetail = new System_Model_Widgetdetail();
      
      $adapter = $mdlMI->getAdapter();
      
      /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
//       $cache = Zend_Registry::get('cache');
//       Zend_Debug::dump($cache->getTags());
      
      
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
			
			
			$resource = $mdlResource->createRow();
			$resource->module = $module;
    		$resource->controller = $element->controller;
    		$resource->actioncontroller = $element->action;
    		$resource = $mdlResource->getIdByDetail($resource);
			
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
			
			$menus = $mdlMenu->getMenus();
			$cbMenuItem = $frmWidget->getElement('menuitem');
			
			$cbMIData = array();
			foreach ( $menus as $menu )
			{
			    $menuItemData = array();
			    $childrenItems = $mdlMI->getAllByMenu($menu);
			    if ( $childrenItems->count() > 0 ) {
			      foreach ( $childrenItems as $menuItem ) {
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
			        $adapter->beginTransaction();
			        
			        $widget = $mdlWidget->createRow();
			        $frmWidgetValues = $frmWidget->getValues();
			        $params = array();
			        foreach ( $frmWidgetValues as $wvk => $wv )
			        {
			            if ( !in_array($wvk, $defaultFormFields) )
			            {
			                $params[$wvk] = $wv;
			            }
			        }
			        
			        $widget->isPublished = $frmWidget->getValue('published');
			        $widget->position = $frmWidget->getValue('position');
			        $widget->title = $frmWidget->getValue('title');
			        $widget->showtitle = $frmWidget->getValue('showtitle');
			        $widget->wid = $frmWidget->getValue('wid');
			        $widget->resource_id = $resource->id;
			        $widget->params = Zend_Json::encode($params);
			        
			        $widget->save();
// 			        Zend_Debug::dump($resource->toArray());
// 			        Zend_Debug::dump($widget->toArray());
// 			        die();
			        
			        if ( $frmWidget->getElement('renderfor')->getValue() == 0 )
			        {
			            $widgetDetail = $mdlWidgetDetail->createRow();
			            $widgetDetail->widget_id = $widget->id;
			            $widgetDetail->menuitem_id = null;
			            $widgetDetail->save();
			        }else {
				        foreach ( $frmWidget->getValue('menuitem') as $mi )
				        {
				          $widgetDetail = $mdlWidgetDetail->createRow();
				          $widgetDetail->widget_id = $widget->id;
				          $widgetDetail->menuitem_id = $mi;
				          $widgetDetail->save();
				        }
			        }
			        $adapter->commit();
			        
			        $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("New widget added") ) );
			        $this->redirect('widgets');
			    }
			}

        } catch (Exception $e) {
//           $adapter->rollBack();
            Zend_Debug::dump($e->getMessage(),'Error at '.$e->getLine());
            Zend_Debug::dump($e->getTraceAsString());
            die();
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
            $this->redirect('widgets');
        }
        return;
    }
    
    private function _loadMenuItems($menuItem, &$data, $level=0)
    {
        $data[$menuItem->id] = str_repeat('-', $level).' '.$menuItem->title;
        $mdlMenuItem = new menu_Model_Item ();
        $children = $mdlMenuItem->getChildren($menuItem);
        if (count ( $children ) > 0) {
          foreach ( $children as $child )
            $this->_loadMenuItems($child, $data, $level++);
        }
    }

    /**
     * update action for widget controller
     * @throws Exception
     */
    public function updateAction()
    {
      $mdlWidget = new System_Model_Widget();
      $mdlResource = new Acl_Model_Resource();
      $mdlMenu = new menu_Model_Menu();
      $mdlMI = new menu_Model_Item();
      $mdlWidgetDetail = new System_Model_Widgetdetail();
      
        try {
          $translate = Zend_Registry::get('Zend_Translate');
            $id = $this->getRequest()->getParam('id', 0);
            $widget = $mdlWidget->find($id)->current();
            $resource = $mdlResource->find($widget->resource_id)->current();
            
            $widgetFile = APPLICATION_PATH.'/modules/'.$resource->module.'/widgets.xml';
            if ( !file_exists( $widgetFile ) ) {
            	throw new Exception($translate->translate("SYSTEM_WIDGET_NOT_FOUND"));
            }
            
            $sxe = new SimpleXMLElement($widgetFile, null, true);
            $element = null;
            foreach( $sxe as $widgetElement ) {
            	if ( $widgetElement['id'] == $widget->wid ) {
            		$element = $widgetElement;
            		break;
            	}
            }
            
            if ( !$element ) throw new Exception($translate->translate("SYSTEM_WIDGET_ELEMENT_NOT_FOUND"));
            
            $frmWidget = ucfirst( strtolower( strval($element->module) ) ).'_Form_Widgets';
//             var_dump($frmWidget);die();
            $frmWidget = ( strtolower(strval($element->module)) == 'menu' ) ? 'menu_Form_Widgets' : $frmWidget;
            $frmWidget = new $frmWidget( array('widgetType'=>strtolower(strval($element->widget_type))) );
			
			$hookXml = APPLICATION_PATH.'/configs/hooks.xml';
			$sxeHook = new SimpleXMLElement( $hookXml, null, true);
			$cbPosition = $frmWidget->getElement("position");
			foreach ( $sxeHook as $hook ) {
				$cbPosition->addMultiOption( $hook, $hook );
			}
			
			$cbMenuItem = $frmWidget->getElement('menuitem');
			$menus = $mdlMenu->getByStatus(1);
			
			$cbMIData = array();
			foreach ( $menus as $menu )
			{
			  $menuItemData = array();
			  $childrenItems = $mdlMI->getAllByMenu($menu);
			  if ( $childrenItems->count() > 0 ) {
			    foreach ( $childrenItems as $menuItem ) {
			      $this->_loadMenuItems($menuItem, $menuItemData);
			    }
			  }
			  $cbMIData[$menu->name] = $menuItemData;
			  $cbMenuItem->addMultiOptions( $cbMIData );
			}
			
			$frmWidget->setAction( $this->_request->getBaseUrl() . "/widget-update/".$widget->id );
			$frmWidget->populate( $widget->toArray() );
			
			$menuItemsRegistered = $mdlWidgetDetail->getByWidget($widget);
			if ( $menuItemsRegistered->count() > 0 ) {
			  $temp = array();
			  foreach ( $menuItemsRegistered as $mir ) {
			    $temp[] = $mir->menuitem_id;
			  }
              $frmWidget->populate( array('menuitem' => $temp) );!
              $frmWidget->getElement('renderfor')->setValue(1);
			} else {
			    $frmWidget->getElement('renderfor')->setValue(0);
			    $frmWidget->getElement('menuitem')->setAttrib('disabled', true);
			}
			
			$params = Zend_Json::decode($widget->params);
			$frmWidget->populate( $params );
			
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
			                $params[$wvk] = $wv;
			            }
			        }
			        
			        $widget->isPublished = $frmWidget->getValue('published');
			        $widget->position = $frmWidget->getValue('position');
			        $widget->title = $frmWidget->getValue('title');
			        $widget->showtitle = $frmWidget->getValue('showtitle');
			        $widget->wid = $frmWidget->getValue('wid');
			        $widget->params = Zend_Json::encode($params);
			        
			        $widget->save();
			        $mdlWidgetDetail->deleteByWidget($widget);
			        if ( $frmWidget->getElement('renderfor')->getValue() == 0 )
			        {
			            $widgetDetail = $mdlWidgetDetail->createRow();
			            $widgetDetail->widget_id = $widget->id;
			            $widgetDetail->menuitem_id = null;
			            $widgetDetail->save();
			        }else {
			            foreach ( $frmWidget->getValue('menuitem') as $mi )
			            {
			                $widgetDetail = $mdlWidgetDetail->createRow();
			                $widgetDetail->widget_id = $widget->id;
			                $widgetDetail->menuitem_id = $mi;
			                $widgetDetail->save();
			            }
			        }
			        
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
            $mdlWidget = new System_Model_Widget();
            $widget = $mdlWidget->find($id)->current();
            
            if ( $widget->isPublished == 0 ) {
                $widget->isPublished = 1;
        	} else {
        	    $widget->isPublished = 0;
        	}
        	$widget->save();
        	$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("Changes saved") ) );
        	$this->redirect('widgets');
        } catch (Exception $e) {
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
      $mdlWidget = new System_Model_Widget();
      $adapter = $mdlWidget->getAdapter();
    	try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$id = $this->getRequest()->getParam('id', 0);
            $adapter->beginTransaction();
            $widget = $mdlWidget->createRow();
            $widget->id = $id;
            $widget->delete();
        	$adapter->commit();
        	$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("The item was removed.") ) );
        	$this->redirect('widgets');
        } catch (Exception $e) {
          $adapter->rollBack();
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
        	$mdlWidget = new System_Model_Widget();
            $widget = $mdlWidget->find($id)->current();
        	
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

