<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package OS\Application\Plugins
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class OS_Plugins_Navigation extends Zend_Controller_Plugin_Abstract {
  /**
   *
   * @access private
   * @var Zend_Db_Table_Row_Abstract
   */
  private $role = null;
  
  /**
   * (non-PHPdoc)
   * 
   * @see Zend_Controller_Plugin_Abstract::preDispatch()
   */
  public function preDispatch(Zend_Controller_Request_Abstract $request) {
    try {
      
      $roleId = 3;
      
      $auth = Zend_Auth::getInstance ();
      $acl = Zend_Registry::get ( 'ZendACL' );
      if ($auth->hasIdentity ()) {
        $identity = $auth->getIdentity ();
        $roleId = intval ( $identity->role_id );
      }
      
      $mdlMenuItem = new menu_Model_Item ();
      $mdlMenu = new menu_Model_Menu ();
      
      $navLinks = array ();
      $nav = new Zend_Navigation ( $navLinks );
      $menus = $mdlMenu->getByStatus ( 1 );
      
      foreach ( $menus as $menu ) {
        // add each menu to zend navigation
        $options = array (
            'id' => 'menu-' . $menu->id,
            'label' => $menu->name,
            'uri' => '' 
        );
        $page = Zend_Navigation_Page::factory ( $options );
        $nav->addPage ( $page );
        // getting menu items 
        $menuItems = $mdlMenuItem->getAllByMenu($menu);
        if ( $menuItems->count() > 0 ) {
          foreach ( $menuItems as $menuItem ) {
            $this->_addPage ( $page, $menuItem );
          }
        }
      }
      
      $this->_addCurrentPageUnregistered ( $nav, $request );
      $page = $nav->findBy ( "id", "mii-" . $request->getParam ( "mid" ) );
      if ($page)
        $page->setActive ( true );
      
      Zend_Registry::set ( 'Zend_Navigation', $nav );
      // Zend_Debug::dump($nav->toArray()); die();
    } catch ( Exception $e ) {
      Zend_Debug::dump ( $e->getMessage () );
      Zend_Debug::dump ( $e->getTraceAsString () );
      die ();
      try {
        $writer = new Zend_Log_Writer_Stream ( APPLICATION_LOG_PATH . 'plugins.log' );
        $logger = new Zend_Log ( $writer );
        $logger->log ( $e->getMessage (), Zend_Log::ERR );
      } catch ( Exception $e ) {
      }
    }
  }
  
  /**
   * 
   * @param Zend_Navigation_Page $pageParent
   * @param unknown $menuItem
   */
  private function _addPage(Zend_Navigation_Page $pageParent, $menuItem) {
    $page = ($menuItem->external == 1) ? $this->_addExternalPage ( $pageParent, $menuItem ) : $this->_addInternalPage ( $pageParent, $menuItem );
    $mdlMenuItem = new menu_Model_Item ();
    $children = $mdlMenuItem->getChildren($menuItem);
    if (count ( $children ) > 0) {
      foreach ( $children as $child ) {
        $this->_addPage ( $page, $child );
      }
    }
  }
  
  /**
   *
   * @param Zend_Navigation_Page $pageParent          
   * @param Zend_Db_Table_Row_Abstract $menuItem          
   * @return Ambigous <Zend_Navigation_Page, Zend_Navigation_Page_Mvc, Zend_Navigation_Page_Uri, unknown>
   */
  private function _addExternalPage(Zend_Navigation_Page $pageParent, $menuItem) {
    $options = array (
        'id' => 'mii-' . $menuItem->id,
        'label' => $menuItem->title,
        'title' => $menuItem->title,
        'uri' => $this->_getParamByKey ( $menuItem, 'linkt' ),
        'target' => $menuItem->wtype,
        'resource' => strtolower ( $menuItem->module . ':' . $menuItem->controller ),
        'privilege' => strtolower ( $menuItem->actioncontroller ),
        'order' => $menuItem->ordering,
        'visible' => true,
        'class' => $menuItem->css_class 
    );
    $page = Zend_Navigation_Page::factory ( $options );
    $pageParent->addPage ( $page );
    return $page;
  }
  
  /**
   *
   * @param Zend_Navigation_Page $pageParent          
   * @param Zend_Db_Table_Row_Abstract $menuItem          
   * @return Ambigous <Zend_Navigation_Page, Zend_Navigation_Page_Mvc, Zend_Navigation_Page_Uri, unknown>
   */
  private function _addInternalPage(Zend_Navigation_Page $pageParent, $menuItem) {
    if ($menuItem->isPublished == 0)
      return;
    $options = array (
        'id' => 'mii-' . $menuItem->id,
        'label' => $menuItem->title,
        'title' => $menuItem->title,
        'target' => $menuItem->wtype,
        'resource' => strtolower ( $menuItem->module . ':' . $menuItem->controller ),
        'privilege' => strtolower ( $menuItem->actioncontroller ),
        'order' => $menuItem->ordering,
        'visible' => true,
        'class' => $menuItem->css_class,
        'module' => $menuItem->module,
        'controller' => $menuItem->controller,
        'action' => $menuItem->actioncontroller 
    );
    
    $params = array ();
    $subItemsParams = Zend_Json::decode ( $menuItem->params );
    if (! is_null ( $subItemsParams ))
      $params = $subItemsParams;
    $page = Zend_Navigation_Page::factory ( $options );
    $page->addParams ( $params );
    $pageParent->addPage ( $page );
    $page->setRoute ( $menuItem->route );
    return $page;
  }
  
  /**
   * Get param by key
   * 
   * @access protected
   * @param Zend_Db_Table_Row_Abstract $menuItem          
   * @param string $key          
   * @return string
   */
  private function _getParamByKey($menuItem, $key) {
    $params = Zend_Json::decode ( $menuItem->params );
    foreach ( $params as $param ) {
      if (isset ( $param [$key] )) {
        return $param [$key];
      } else
        return '';
    }
    return '';
  }
  
  /**
   * 
   * @param Zend_Navigation $nav
   * @param Zend_Controller_Request_Abstract $request
   */
  private function _addCurrentPageUnregistered(Zend_Navigation $nav, Zend_Controller_Request_Abstract $request) {
    if ($nav->findBy ( 'id', 'mii-' . $request->getParam ( 'mid' ) )) {
      return;
    }
    $session = new Zend_Session_Namespace ( 'previousPage' );
    if (strcmp ( strtolower ( $request->getActionName () ), 'logout' ) === 0)
      $session->unsetAll (); // session->previousPage = null;
    $previousPage = $session->previousPage;
    $currentPage = Zend_Controller_Front::getInstance ()->getRequest ()->getRequestUri ();
    $navItem = $nav->findAllBy ( 'active', true );
    if (count ( $navItem ) == 0) {
      $navItem = array (
          'module' => strtolower ( $request->getModuleName () ),
          'controller' => strtolower ( $request->getControllerName () ),
          'action' => strtolower ( $request->getActionName () ),
          'label' => ucfirst ( strtolower ( $request->getActionName () ) ),
          'title' => ucfirst ( strtolower ( $request->getActionName () ) ),
          'resource' => strtolower ( $request->getModuleName () . ':' . $request->getControllerName () ),
          'privilege' => strtolower ( $request->getActionName () ),
          'id' => 0,
          'mid' => 0,
          'visible' => false,
          'active' => true 
      );
      if (is_null ( $previousPage )) {
        $nav->addPage ( $navItem );
      } else {
        $navCurrentItem = $nav->findBy ( 'id', $session->miid );
        $navCurrentItem->addPage ( $navItem );
      }
    } else {
      $session->previousPage = $currentPage;
      $navCurrentItem = $nav->findBy ( 'active', true );
      $session->miid = $navCurrentItem->id;
    }
  }
}