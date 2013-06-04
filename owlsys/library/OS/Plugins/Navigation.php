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
class OS_Plugins_Navigation extends Zend_Controller_Plugin_Abstract
{
    /**
     * @access private
     * @var Zend_Db_Table_Row_Abstract
     */
    private $role = null;
    
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Plugin_Abstract::preDispatch()
     */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		try {
			
			$roleId = 3;
			
			$auth = Zend_Auth::getInstance();
			$acl = Zend_Registry::get('ZendACL');
			if ( $auth->hasIdentity() ) {
				$identity = $auth->getIdentity();
				$roleId = intval($identity->role_id);
			}
			
			$mdlMenuItemMapper = menu_Model_ItemMapper::getInstance();
			$mdlMenuMapper = menu_Model_MenuMapper::getInstance();
			
			$navLinks = array();
			$nav = new Zend_Navigation($navLinks);
			$menus = $mdlMenuMapper->getByStatus(1);
			foreach ( $menus as $menu ) {
			    $mdlMenuItemMapper->getByMenu($menu);
			    if ( $menu->getChildren() > 0 ) {
    			    foreach ( $menu->getChildren() as $menuItem ) {
    			        /* @var $menuItem menu_Model_Item */
    			        $mdlMenuItemMapper->getMenuItemsRecursively($menuItem);
    			    }
			    }
			}
			
			foreach ( $menus as $menu ) {
			    $options = array(
			            'id' => 'menu-'.$menu->getId(),
			            'label' => $menu->getName(),
			            'uri'   => '',
			    );
			    $page = Zend_Navigation_Page::factory( $options );
			    $nav->addPage( $page );
			    if ( $menu->getChildren() > 0 ) {
    			    foreach ( $menu->getChildren() as $menuItem ) {
    			        $this->_addPage($page, $menuItem);
    			    }
			    }
			}
			
			$this->_addCurrentPageUnregistered($nav, $request);
			$page = $nav->findBy("id", "mii-".$request->getParam("mid"));
			if ( $page ) $page->setActive(true);
			
		    Zend_Registry::set('Zend_Navigation', $nav);
// 		    Zend_Debug::dump($nav->toArray()); die();
		    
		} catch (Exception $e) {
		    Zend_Debug::dump($e->getMessage());
		    Zend_Debug::dump($e->getTraceAsString());
		    die();
		    try {
		        $writer = new Zend_Log_Writer_Stream(APPLICATION_LOG_PATH . 'plugins.log');
		        $logger = new Zend_Log($writer);
		        $logger->log($e->getMessage(), Zend_Log::ERR);
		    } catch (Exception $e) {
		    }
		} 
	}
	
	private function _addPage(Zend_Navigation_Page $pageParent, menu_Model_Item $menuItem) 
	{
        $page = ( $menuItem->getExternal() == 1 ) ? $this->_addExternalPage($pageParent, $menuItem) : $this->_addInternalPage($pageParent, $menuItem);
        if ( count($menuItem->getChildren()) > 0 ) {
            foreach ( $menuItem->getChildren() as $child ) {
                $this->_addPage($page, $child);
            }
        }
	}
	
	/**
	 * 
	 * @param Zend_Navigation_Page $pageParent
	 * @param menu_Model_Item $menuItem
	 * @return Ambigous <Zend_Navigation_Page, Zend_Navigation_Page_Mvc, Zend_Navigation_Page_Uri, unknown>
	 */
	private function _addExternalPage( Zend_Navigation_Page $pageParent, menu_Model_Item $menuItem )
	{
		$options = array(
			'id' => 'mii-'.$menuItem->getId(),
			'label' => $menuItem->getTitle(),
			'title' => $menuItem->getTitle(),
			'uri' 	=> $this->_getParamByKey( $menuItem, 'linkt'),
			'target' => $menuItem->getWtype(),
			'resource' => strtolower( $menuItem->getResource()->getModule().':'.$menuItem->getResource()->getController() ),
			'privilege' => strtolower( $menuItem->getResource()->getActioncontroller() ),
			'order' => $menuItem->getOrdering(),
			'visible' => true,
			'class' => $menuItem->getCssClass()
		);
		$page = Zend_Navigation_Page::factory($options);
		$pageParent->addPage($page);
		return $page;
	}
	
	/**
	 * 
	 * @param Zend_Navigation_Page $pageParent
	 * @param menu_Model_Item $menuItem
	 * @return Ambigous <Zend_Navigation_Page, Zend_Navigation_Page_Mvc, Zend_Navigation_Page_Uri, unknown>
	 */
	private function _addInternalPage( Zend_Navigation_Page $pageParent, menu_Model_Item $menuItem )
	{
		if ( $menuItem->getIsPublished() == 0 ) return;
		$options = array(
			'id' => 'mii-'.$menuItem->getId(),
			'label' => $menuItem->getTitle(),
			'title' => $menuItem->getTitle(),
			'target' => $menuItem->getWtype(),
			'resource' => strtolower( $menuItem->getResource()->getModule().':'.$menuItem->getResource()->getController() ),
			'privilege' => strtolower( $menuItem->getResource()->getActioncontroller() ),
			'order' => $menuItem->getOrdering(),
			'visible' => true,
			'class' => $menuItem->getCssClass(),
			'module' => $menuItem->getResource()->getModule(),
			'controller' => $menuItem->getResource()->getController(),
			'action' => $menuItem->getResource()->getActioncontroller(),
		);
		
		$params = array();
		$subItemsParams = Zend_Json::decode($menuItem->getParams());
		if ( !is_null($subItemsParams) ) $params = $subItemsParams;
		$page = Zend_Navigation_Page::factory($options);
		$page->addParams($params);
	    $pageParent->addPage($page);
	    $page->setRoute( $menuItem->getRoute() );
		return $page;
	}
	
	/**
	 * Get param by key
	 * @access protected
	 * @param Zend_Db_Table_Row_Abstract $menuItem
	 * @param string $key
	 * @return string
	 */
	private function _getParamByKey( menu_Model_Item $menuItem, $key )
	{
	    $params = Zend_Json::decode($menuItem->getParams());
	    foreach ($params as $param) {
	        if ( isset($param[$key]) )
	        {
	            return $param[$key];
	        } else return '';
	    }
	    return '';
	}
	
	private function _addCurrentPageUnregistered( Zend_Navigation $nav, Zend_Controller_Request_Abstract $request )
	{
	    if ( $nav->findBy('id', 'mii-'.$request->getParam('mid')) ){
	        return;
	    } 
		$session = new Zend_Session_Namespace('previousPage');
		if ( strcmp( strtolower($request->getActionName()), 'logout') === 0 ) $session->unsetAll(); #$session->previousPage = null;
		$previousPage = $session->previousPage;
		$currentPage = Zend_Controller_Front::getInstance()->getRequest()->getRequestUri();
		$navItem = $nav->findAllBy('active', true);
		if ( count($navItem) == 0 ) {
			$navItem = array (
				'module' => strtolower( $request->getModuleName() ),
				'controller' => strtolower( $request->getControllerName() ),
				'action' => strtolower( $request->getActionName() ),
				'label' => ucfirst( strtolower( $request->getActionName() ) ) ,
				'title' => ucfirst( strtolower( $request->getActionName() ) ) ,
				'resource' => strtolower( $request->getModuleName().':'.$request->getControllerName() ),
				'privilege' => strtolower( $request->getActionName() ),
				'id' => 0,
				'mid' => 0,
				'visible' => false,
				'active' => true,
			);
			if ( is_null($previousPage) ) {
				$nav->addPage($navItem);
			} else {
				$navCurrentItem = $nav->findBy('id', $session->miid);
				$navCurrentItem->addPage( $navItem );
			}
		} else {
			$session->previousPage = $currentPage;
			$navCurrentItem = $nav->findBy('active', true);
			$session->miid = $navCurrentItem->id;
		}
	}
}