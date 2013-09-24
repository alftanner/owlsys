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
            $mdlMenu = new menu_Model_Menu();
            
            if ( $this->getRequest()->isPost() )
            {
            	if ( $frmMenu->isValid( $this->getRequest()->getParams() ) )
            	{
            		$menu = $mdlMenu->createRow($frmMenu->getValues());
            		$menu->save();
            		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("New menu added") ) );
            		$this->redirect('menus');
            	}
            }
            $frmMenu->setAction( $this->_request->getBaseUrl() . '/menu-create' );
            $this->view->form = $frmMenu;
        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
            $this->redirect('menus');
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
            
            if ( $this->getRequest()->isPost() )
            {
            	if ( $frmMenu->isValid( $_POST ) )
            	{
            	  $menu->setFromArray($frmMenu->getValues());
            	  $menu->save();
            	  $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("Changes saved") ) );
            	  $this->redirect('menus');
            	}
            }
            
            $frmMenu->populate( $menu->toArray() );
            $frmMenu->setAction( $this->_request->getBaseUrl() . '/menu-update/'.$menu->id );
            $this->view->form = $frmMenu;
        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
            $this->redirect('menus');
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
            $menu = $mdlMenu->find($id)->current();
            $menu->delete();
            $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("The menu was deleted") ) );
            $this->redirect('menus');
        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
            $this->redirect('menus');
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
            $paginator = Zend_Paginator::factory($mdlMenu->getMenus());
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
            
            if ( $menu->isPublished == 1 ) {
            	$menu->isPublished = 0;
            }else {
            	$menu->isPublished = 1;
            }
            $menu->save();
            $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("Changes saved") ) );
            $this->redirect('menus');
        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
            $this->redirect('menus');
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
            $menuId = trim($params['menuId']);
            $menuSelected = $navigation->findOneById( 'menu-'.$menuId );
            $menuItemSelected = $navigation->findBy( 'active', 1 );
            if ( $menuSelected->id == 0 ) $menuItemSelected = $menuItemSelected->_parent;
            $this->view->menuItemSelected = $menuItemSelected;
            $this->view->menuId = $menuId;
            $menu = $navigation->menu();
            
            $css = "navigation menu-".$menuId.' ';
            if ( array_key_exists('css', $params) ) {
                $css .= trim($params['css'])." ";
            } 
            $menu->setUlClass( $css );
            echo $menu->renderMenu($menuSelected);
            
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
        	$roleId = 3;
        	if ( $auth->hasIdentity() ) {
        		$identity = $auth->getIdentity();
                $roleId = $identity->role_id;
			} 
        	$params = $this->getRequest()->getParams();
        	/* @var $navigation Zend_Navigation */
        	$navigation = Zend_Layout::getMvcInstance()->getView()->navigation();
        	$navigation->setAcl($acl)->setRole( strval($roleId) );
        	$menuId = trim($params['menuId']);
        	$menuSelected = $navigation->findOneById( 'menu-'.$menuId );

        	/* @var $menu Zend_View_Helper_Navigation_Menu */
        	$menu = $navigation->menu();
        	
        	$horizontalCss = ( $params['distribution'] == 'horizontal' ) ? ' menu-horizontal-bootstrap ' : '';
        	$menu->setUlClass( 'nav '.$horizontalCss );
        	$menu->setUlId( 'menu-ulh-'.$menuId );
        	$this->view->uid = 'menu-ulh-'.$menuId;
         	echo $menu->renderMenu($menuSelected);
        	
        } catch (Exception $e) {
        	echo $e->getMessage();
        }
        return;
    }


}


