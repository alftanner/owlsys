<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc., 51 Franklin
 * St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package menu
 * @subpackage controllers
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 *
 */

class Menu_MenuController extends Zend_Controller_Action
{

    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action::init()
     *
     */
    public function init()
    {
        /* Initialize action controller here */
    }

    /**
     * Index action for index controller
     *
     */
    public function indexAction()
    {
        // action body
    }

    /**
     * Create action for index controller
     * @return NULL
     *
     */
    public function createAction()
    {
        try {
            $translate = Zend_Registry::get('Zend_Translate');
            $frmMenu = new menu_Form_Menu();
            if ( $this->getRequest()->isPost() )
            {
            	if ( $frmMenu->isValid( $this->getRequest()->getParams() ) )
            	{
            		$mdlMenu = new menu_Model_Menu();
            		$menu = $mdlMenu->createRow( $frmMenu->getValues() );
            		$menu->save();
            		
            		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("MENU_MENU_CREATED_SUCCESSFULLY") ) );
            		$this->_helper->redirector( "list", "menu", "menu" );
            	}
            } else {
            	$fields = array();
            	foreach ( $frmMenu->getElements() as $element ) $fields[] = $element->getName();
            	$frmMenu->addDisplayGroup( $fields, 'form', array( 'legend' => "MENU_ADD_MENU", ) );
            }
            $frmMenu->setAction( $this->_request->getBaseUrl() . '/menu/menu/create' );
            $this->view->form = $frmMenu;
        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
            $this->_helper->redirector( "list", "menu", "menu" );
        }
        return null;
    }

    /**
     * Update action for index controller
     * @throws Exception
     * @return NULL
     *
     */
    public function updateAction()
    {
        try {
            $translate = Zend_Registry::get('Zend_Translate');
            $id = $this->getRequest()->getParam("id");
            $frmMenu = new menu_Form_Menu();
            $mdlMenu = new menu_Model_Menu();
            $menu = $mdlMenu->find($id)->current();
            if ( !$menu ) throw new Exception($translate->translate("LBL_ROW_NOT_FOUND"));
            if ( $this->getRequest()->isPost() )
            {
            	if ( $frmMenu->isValid( $_POST ) )
            	{
            		$menu->setFromArray( $frmMenu->getValues() );
            		$menu->save();
            		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("MENU_MENU_UPDATED_SUCCESSFULLY") ) );
            		$this->_helper->redirector( "list", "menu", "menu" );
            	}
            } else {
            	$frmMenu->populate( $menu->toArray() );
            	$fields = array();
            	foreach ( $frmMenu->getElements() as $element ) $fields[] = $element->getName();
            	$frmMenu->addDisplayGroup( $fields, 'form', array( 'legend' => "MENU_UPDATE_MENU", ) );
            }
            $frmMenu->setAction( $this->_request->getBaseUrl() . '/menu/menu/update' );
            $this->view->form = $frmMenu;
        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
            $this->_helper->redirector( "list", "menu", "menu" );
        }
        return null;
    }

    /**
     * Delete action for index controller
     * @throws Exception
     *
     */
    public function deleteAction()
    {
        try {
            $translate = Zend_Registry::get('Zend_Translate');
            $id = $this->getRequest()->getParam("id");
            $mdlMenu = new menu_Model_Menu();
            $menu = $mdlMenu->find( $id )->current();
            if ( !$menu )  throw new Exception($translate->translate("LBL_ROW_NOT_FOUND"));
            $menu->delete();
            $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("MENU_MENU_DELETED_SUCCESSFULLY") ) );
            $this->_helper->redirector( "list", "menu", "menu" );
        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
            $this->_helper->redirector( "list", "menu", "menu" );
        }
        return null;
    }

    /**
     * List action for index controller
     * @return NULL
     *
     */
    public function listAction()
    {
        try {
            $mdlMenu = new menu_Model_Menu();
            $adapter = $mdlMenu->getPaginatorAdapterList();
            $paginator = new Zend_Paginator($adapter);
            $paginator->setItemCountPerPage(10);
            $pageNumber = $this->getRequest()->getParam('page',1);
            $paginator->setCurrentPageNumber($pageNumber);
            $this->view->menus = $paginator;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
		return null;
    }

    /**
     * Publish action for index controller
     * @throws Exception
     * @return NULL
     *
     */
    public function publishAction()
    {
        try {
            $translate = Zend_Registry::get('Zend_Translate');
            $id = $this->getRequest()->getParam( "id" );
            $mdlMenu = new menu_Model_Menu();
            $menu = $mdlMenu->find($id)->current();
            if ( !$menu )  throw new Exception($translate->translate("LBL_ROW_NOT_FOUND"));
            if ( $menu->published == 1 ) {
            	$menu->published = 0;
            	$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("MENU_MENU_UNPUBLISHED_SUCCESSFULLY") ) );
            }else {
            	$menu->published = 1;
            	$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("MENU_MENU_PUBLISHED_SUCCESSFULLY") ) );
            }
            #$menu->published = ( $menu->published == 1 ) ? 0 : 1 ;
            $menu->save();;
            $this->_helper->redirector( "list", "menu", "menu" );
        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
            $this->_helper->redirector( "list", "menu", "menu" );
        }
        return null;
    }

    /**
     * Render action for index controller
     * @return NULL
     *
     */
    public function renderAction()
    {
        // action body
        try {
            $auth = Zend_Auth::getInstance();
            $acl = Zend_Registry::get('ZendACL');
            if ( $auth->hasIdentity() ) {
            	$identity = $auth->getIdentity();
            	$role = intval( $identity->role_id );
            }else{
            	$role=3;
            }
            
            $params = $this->getRequest()->getParams();
            $navigation = Zend_Layout::getMvcInstance()->getView()->navigation();
            $navigation->setAcl($acl)->setRole( strval($role) );
            #Zend_Debug::dump($navigation);
            #die();
            $menuId = trim($params['menuId']);
            $menuSelected = $navigation->findOneById( 'menu-'.$menuId );
            $menuItemSelected = $navigation->findBy( 'active', 1 );
            if ( $menuSelected->id == 0 ) $menuItemSelected = $menuItemSelected->_parent;
            #Zend_Debug::dump($menuItemSelected);
            #die();
            $this->view->menuItemSelected = $menuItemSelected;
            $this->view->menuId = $menuId;
            $menu = $navigation->menu();
            $css = "navigation menu-".$menuId.' ';
            if ( array_key_exists('css', $params) ) {
                $css .= trim($params['css'])." ";
            } 
            if ( array_key_exists('dropdownmultilevel', $params) ) {
                if ( trim($params['dropdownmultilevel']) == 1 ) $css .= " horizontal-dropdown-multilevel ";
                $this->view->dropdownmultilevel = trim($params['dropdownmultilevel']);
            }
            $menu->setUlClass( $css );
            #if ( array_key_exists('css', $params) ) $this->view->menuSelected = $menuSelected;
            echo $menu->renderMenu($menuSelected);
            
            #$translate = Zend_Registry::get('Zend_Translate');
            #$this->_helper->flashMessenger->addMessage( array('type'=>'success', 'message'=>$translate->translate("MENU_MENU_CREATED_SUCCESSFULLY"), 'header'=>'') );
            #Zend_Debug::dump( $navigation->menu()->setUlClass('clase')->renderMenu($menuSelected) );
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return null;
    }

    /**
     * Breadcrumb action for index controller
     * @return NULL
     *
     */
    public function breadcrumbAction()
    {
        // action body
        try {
            $params = $this->getRequest()->getParams();
            #print_r($params);
            $navigation = Zend_Layout::getMvcInstance()->getView()->navigation();
            echo $navigation->breadcrumbs()
            	->setMinDepth( $params['depth'] )
            	->setLinkLast( $params['lastlink'] )
            	->setSeparator( $params['separator'] )
            	->setPartial( array('partials/breadcrumb.phtml', 'menu') )
            ;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return;
    }

    /**
     * Sitemap action for index controller
     *
     */
    public function sitemapAction()
    {
        // action body
        try {
            $auth = Zend_Auth::getInstance();
            $acl = Zend_Registry::get('ZendACL');
            if ( $auth->hasIdentity() ) {
            	$identity = $auth->getIdentity();
            	$role = $identity->role_id;
            }else{
            	$role=3;
            }
            
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            $sitemap = $this->view->navigation()->sitemap()
	            ->setUseXmlDeclaration(true)
	            ->setFormatOutput(true)
	            ->setUseSitemapValidators(true)
	            ->setRole( strval($role) );
            $this->getResponse()->setHeader('Content-Type', 'application/xml')->setBody($sitemap);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return;
    }

    /**
     * render a menu for bootstrap framework with custom style class
     */
    public function renderbootstrapAction()
    {
        // action body
        try {
        	$auth = Zend_Auth::getInstance();
        	$acl = Zend_Registry::get('ZendACL');
        	if ( $auth->hasIdentity() ) {
        		$identity = $auth->getIdentity();
        		$role = intval( $identity->role_id );
        	}else{
        		$role=3;
        	}
        	$params = $this->getRequest()->getParams();
        	$navigation = Zend_Layout::getMvcInstance()->getView()->navigation();
        	$navigation->setAcl($acl)->setRole( strval($role) );
        	$menuId = trim($params['menuId']);
        	$menuSelected = $navigation->findOneById( 'menu-'.$menuId );
        	
        	#$navigation = new Zend_Navigation();
        	#$page = new Zend_Navigation_Page_Mvc();
        	
        	echo '<div class="navbar">
					  <div class="navbar-inner">
					      <div class="container">
					        ';
        	
        	$menu = $navigation->menu();
        	$menu->setUlClass( 'nav' );
        	echo $menu->renderMenu($menuSelected);
        	
        	echo '			</div>
				  </div>
				</div>';
        	
        } catch (Exception $e) {
        	echo $e->getMessage();
        }
        return;
    }


}

