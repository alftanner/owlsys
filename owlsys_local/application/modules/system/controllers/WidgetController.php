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
        	$mdlResource = new Acl_Model_Resource();
        	$modules = $mdlResource->getModules();
        	$widgets = array();
        	foreach ( $modules as $module )
        	{
        	    #echo APPLICATION_PATH.'/modules/'.$module->module.'<br>';
        	    $widgetFile = APPLICATION_PATH.'/modules/'.$module->module.'/widgets.xml';
        	    if ( file_exists( $widgetFile ) )
        	    {
        	        #echo "si en ".$module->module.'<br>';
        	        $sxe = new SimpleXMLElement( $widgetFile, null, true);
        	        foreach( $sxe as $widget ) {
        	           #Zend_Debug::dump($widget);
        	           $widgets[] = $widget; 
        	        }
        	    }
        	    #
        	}
        	$this->view->widgets = $widgets;
        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
            $this->_helper->redirector( "list", "widget", "system" );
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
        	$adapter = $mdlWidget->getPaginatorAdapterList();
        	$paginator = new Zend_Paginator($adapter);
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
			
			$mdlResource = new Acl_Model_Resource();
			$resource = $mdlResource->getIdByDetail($module, strval($element->controller), strval($element->action));
			if ( !$resource ) throw new Exception($translate->translate("ACL_RESOURCE_NOT_FOUND"));
			
			$frmWidget = ucfirst( strtolower( strval($element->module) ) ).'_Form_Widgets';
			$frmWidget = ( strtolower(strval($element->module)) == 'menu' ) ? 'menu_Form_Widgets' : $frmWidget;
			#$frmWidget = new $frmWidget( array('typeForm'=>'test') );
			$frmWidget = new $frmWidget( array('widgetType'=>strtolower(strval($element->widget_type))) );

			#Zend_Debug::dump($frmWidget); die();
			
			$frmWidget->getElement('wid')->setValue( (int) $wid );
			$frmWidget->getElement('mod')->setValue( strval($module) );
			
			$hookXml = APPLICATION_PATH.'/configs/hooks.xml';
			$sxeHook = new SimpleXMLElement( $hookXml, null, true);
			$cbPosition = $frmWidget->getElement("position");
			foreach ( $sxeHook as $hook ) {
			    $cbPosition->addMultiOption( $hook, $hook );
			}
			
			$mdlMenu = new menu_Model_Menu();
			$mdlMI = new menu_Model_Item();
			$menus = $mdlMenu->getMenus();
			$cbMenuItem = $frmWidget->getElement('menuitem');
			foreach ( $menus as $menu ) 
			{
				$menuItemData = array();
				$mdlMI->getMenuItemsForWidget(null, $menu, null, $menuItemData);
				$cbMIData[$menu->name] = $menuItemData;
				$cbMenuItem->addMultiOptions( $cbMIData );
			};
			
			$frmWidget->setAction( $this->_request->getBaseUrl() . "/system/widget/new" );
			
			$this->view->frmWidget = $frmWidget;
			$this->view->widget = $element;
			
			if ( $this->getRequest()->isPost() )
			{
			    if ( $frmWidget->isValid( $this->getRequest()->getParams() ) )
			    {
			        $defaultFormFields = array('id', 'wid', 'mod', 'position', 'title', 'published', 'menuitem', 'csrf_token', 'token', 'widget_id','resource_id', 'showtitle');
			        $mdlWidget = new System_Model_Widget();
			        $mdlWidgetDetail = new System_Model_Widgetdetail();
			        $widget = $mdlWidget->createRow( $this->getRequest()->getParams() );
			        $widget->ordering = $mdlWidget->getLastPosition($widget)+1;
			        #var_dump($module, $element->controller, $element->action);
			        $widget->resource_id = $resource->id;
			        #Zend_Debug::dump($widget);
			        
			        $widget->widget_id = $wid;
			        $frmWidgetValues = $frmWidget->getValues();
			        $params = array();
			        foreach ( $frmWidgetValues as $wvk => $wv )
			        {
			            if ( !in_array($wvk, $defaultFormFields) )
			            {
			                $params[] = $wvk.'='.$wv.'';
			            }
			        }
			        $params = implode("\n", $params);
			        $widget->params = $params;
			        $widget->save();
			        #Zend_Debug::dump($widget);
			        #print_r( $frmWidget->getValues() );
			        
			        if ( $frmWidget->getElement('renderfor')->getValue() == 0 )
			        {
			            $widgetDetail = $mdlWidgetDetail->createRow();
			            $widgetDetail->widget_id = $widget->id;
			            $widgetDetail->menuitem_id = 0;
			            $widgetDetail->save();
			        }else {
				        foreach ( $frmWidget->getValue('menuitem') as $mi )
				        {
				            $widgetDetail = $mdlWidgetDetail->createRow();
				            $widgetDetail->widget_id = $widget->id;
				            $widgetDetail->menuitem_id = $mi;
				            $widgetDetail->save();
				            #Zend_Debug::dump($widgetDetail);
				        }
			        }
			        
			        $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("SYSTEM_WIDGET_ADDED_SUCCESSFULLY") ) );
			        $this->_helper->redirector( "list", "widget", "system" );
			    }
			} else {
				$fields = array();
				foreach ( $frmWidget->getElements() as $wfelement ) $fields[] = $wfelement->getName();
				$frmWidget->addDisplayGroup( $fields, 'form', array( 'legend' => "SYSTEM_NEW_WIDGET", ) );
			}

        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
            $this->_helper->redirector( "list", "widget", "system" );
        }
        return;
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
            $mdlWidget = new System_Model_Widget();
            $widget = $mdlWidget->find($id)->current();
            if ( !$widget ) throw new Exception($translate->translate("LBL_ROW_NOT_FOUND"));

            $mdlResource = new Acl_Model_Resource();
            $resource = $mdlResource->find( $widget->resource_id )->current();
            
            $widgetFile = APPLICATION_PATH.'/modules/'.$resource->module.'/widgets.xml';
            if ( !file_exists( $widgetFile ) ) {
            	throw new Exception($translate->translate("SYSTEM_WIDGET_NOT_FOUND"));
            }
            
            $sxe = new SimpleXMLElement( $widgetFile, null, true);
            $element = null;
            foreach( $sxe as $widgetElement ) {
            	if ( $widgetElement['id'] == $widget->widget_id ) {
            		$element = $widgetElement;
            		break;
            	}
            }
            if ( !$element ) throw new Exception($translate->translate("SYSTEM_WIDGET_ELEMENT_NOT_FOUND"));
            
            #$frmWidget = strval($element->form);
			#$frmWidget = new $frmWidget();
            $frmWidget = ucfirst( strtolower( strval($element->module) ) ).'_Form_Widgets';
            $frmWidget = ( strtolower(strval($element->module)) == 'menu' ) ? 'menu_Form_Widgets' : $frmWidget;
            #$frmWidget = new $frmWidget( array('typeForm'=>'test') );
            $frmWidget = new $frmWidget( array('widgetType'=>strtolower(strval($element->widget_type))) );
			
			$hookXml = APPLICATION_PATH.'/configs/hooks.xml';
			$sxeHook = new SimpleXMLElement( $hookXml, null, true);
			$cbPosition = $frmWidget->getElement("position");
			foreach ( $sxeHook as $hook ) {
				$cbPosition->addMultiOption( $hook, $hook );
			}
			
			$mdlMenu = new menu_Model_Menu();
			$mdlMI = new menu_Model_Item();
			$menus = $mdlMenu->getMenus();
			$cbMenuItem = $frmWidget->getElement('menuitem');
			foreach ( $menus as $menu )
			{
				$menuItemData = array();
				$mdlMI->getMenuItemsForWidget(null, $menu, null, $menuItemData);
				$cbMIData[$menu->name] = $menuItemData;
				$cbMenuItem->addMultiOptions( $cbMIData );
			};
			
			$frmWidget->setAction( $this->_request->getBaseUrl() . "/system/widget/update" );
			
			$frmWidget->populate( $widget->toArray() );
			
			$mdlWidgetDetail = new System_Model_Widgetdetail();
			$renderForAll = $mdlWidgetDetail->isRenderForAll( $widget );
			if( $renderForAll === false ){
			    $rowsSelected = array();
			     $menuItems = $widget->findManyToManyRowset('menu_Model_Item', 'System_Model_Widgetdetail', 'Widget');
			    foreach ( $menuItems as $menuItemSelected ) $rowsSelected[] = $menuItemSelected->id;
			    $frmWidget->populate( array('menuitem' => $rowsSelected) );
			    $frmWidget->getElement('renderfor')->setValue(1);
			}else{
			    $frmWidget->getElement('renderfor')->setValue(0);
			    $frmWidget->getElement('menuitem')->setAttrib('disabled', true);
			}
			
			#parse_str( $widget->params, $output );
			#$frmWidget->populate( $output );
			$params = explode("\n", $widget->params);
			foreach ( $params as $strParam )
			{
				$paramKey = substr($strParam, 0, strpos($strParam, "="));
				$paramValue = substr($strParam, strpos($strParam, "=")+1, strlen($strParam));
				$output[$paramKey] = $paramValue;
				$frmWidget->populate( $output );
			}
			
			if ( $this->getRequest()->isPost() )
			{
			    if ( $frmWidget->isValid( $this->getRequest()->getParams() ) )
			    {
			        
			        $defaultFormFields = array('id', 'wid', 'mod', 'position', 'title', 'published', 'menuitem', 'csrf_token', 'token', 'widget_id', 'showtitle');
			        $widget->title = $frmWidget->getElement('title')->getValue();
			        $widget->published = $frmWidget->getElement('published')->getValue();
			        $widget->position = $frmWidget->getElement('position')->getValue();
			        $widget->showtitle = $frmWidget->getElement('showtitle')->getValue();
			        
			        $frmWidgetValues = $frmWidget->getValues();
			        $params = array();
			        foreach ( $frmWidgetValues as $wvk => $wv )
			        {
			        	if ( !in_array($wvk, $defaultFormFields) )
			        	{
			        		$params[] = $wvk.'='.$wv.'';
			        	}
			        }
			        $params = implode("\n", $params);
			        $widget->params = $params;
			        $widget->save();
			        
			        $menuItemsWidget = $widget->findDependentRowset('System_Model_Widgetdetail', 'Widget');
			        foreach ( $menuItemsWidget as $miw ) $miw->delete();
			        
			        if ( $frmWidget->getElement('renderfor')->getValue() == 0 )
			        {
			        	$widgetDetail = $mdlWidgetDetail->createRow();
			        	$widgetDetail->widget_id = $widget->id;
			        	$widgetDetail->menuitem_id = 0;
			        	$widgetDetail->save();
			        }else {
			        	foreach ( $frmWidget->getValue('menuitem') as $mi )
			        	{
			        		$widgetDetail = $mdlWidgetDetail->createRow();
			        		$widgetDetail->widget_id = $widget->id;
			        		$widgetDetail->menuitem_id = $mi;
			        		$widgetDetail->save();
			        		#Zend_Debug::dump($widgetDetail);
			        	}
			        }
			        #Zend_Debug::dump( $menuItemsWidget );
			        
			        $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("SYSTEM_WIDGET_UPDATED_SUCCESSFULLY") ) );
			        $this->_helper->redirector( "list", "widget", "system" );
			    }
			} else {
				$fields = array();
				foreach ( $frmWidget->getElements() as $wfelement ) $fields[] = $wfelement->getName();
				$frmWidget->addDisplayGroup( $fields, 'form', array( 'legend' => "SYSTEM_UPDATE_WIDGET", ) );
			}
			
			$this->view->frmWidget = $frmWidget;
			$this->view->widget = $element;
			#Zend_Debug::dump($element);
            
        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
            $this->_helper->redirector( "list", "widget", "system" );
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
            $wid = $this->getRequest()->getParam('id', 0);
            $mdlWidget = new System_Model_Widget();
            $widget = $mdlWidget->find( (int) $wid )->current();

            if ( !$widget ) throw new Exception($translate->translate("SYSTEM_WIDGET_NOT_FOUND"));
            
            if ( $widget->published == 0 ) {
        	    $widget->published = 1;
        	    $widget->save();
        	    $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("SYSTEM_WIDGET_PUBLISHED_SUCCESSFULLY") ) );
        	} else {
        	    $widget->published = 0;
        	    $widget->save();
        	    $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("SYSTEM_WIDGET_UNPUBLISHED_SUCCESSFULLY") ) );
        	}
        	
        	$this->_helper->redirector( "list", "widget", "system" );
            
        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
            $this->_helper->redirector( "list", "widget", "system" );
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
        	$mdlWidget = new System_Model_Widget();
        	$id = $this->getRequest()->getParam('id', 0);
        	$widget = $mdlWidget->find( (int) $id )->current();
        	if ( !$widget ) throw new Exception($translate->translate("SYSTEM_WIDGET_NOT_FOUND"));
        	
        	$menuItemsWidget = $widget->findDependentRowset('System_Model_Widgetdetail', 'Widget');
        	foreach ( $menuItemsWidget as $miw ) $miw->delete();
        	
        	$widget->delete();
        	$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("SYSTEM_WIDGET_DELETED_SUCCESSFULLY") ) );
        	$this->_helper->redirector( "list", "widget", "system" );
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "list", "widget", "system" );
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
        	$widget = $mdlWidget->find( (int)$id )->current();
        	if ( !$widget ) throw new Exception($translate->translate("SYSTEM_WIDGET_NOT_FOUND"));
        	
        	if ( !in_array($direction, array('down', 'up')) ) {
        		throw new Exception($translate->translate("LBL_UP_DOWN_NOT_SPECIFIED"));
        	}
        	if ( $direction == "up" )
        	{
        		$mdlWidget->moveUp($widget);
        		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("SYSTEM_WIDGET_DELETED_SUCCESSFULLY") ) );
        	} elseif ( $direction == "down" )
        	{
        		$mdlWidget->moveDown($widget);
        		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_MOVED_DOWN_SUCCESSFULLY") ) );
        	}
        	$this->_helper->redirector( "list", "widget", "system" );
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "list", "widget", "system" );
        }
    }


}

