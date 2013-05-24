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
class OS_Application_Plugins_Navigation extends Zend_Controller_Plugin_Abstract
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
		    #Zend_Debug::dump($request->getParams());
		    #die();
			
			$mdlRole = new Acl_Model_Role();
			
			$auth = Zend_Auth::getInstance();
			$acl = Zend_Registry::get('ZendACL');
			if ( $auth->hasIdentity() ) {
				$identity = $auth->getIdentity();
				$this->role = $mdlRole->find( $identity->role_id );
			}else{
				$this->role = $mdlRole->find( 3 );
			}
			
		    $mdlMenuItem = new menu_Model_Item();
		    $mdlMenu = new menu_Model_Menu();

		    $navLinks = array();
		    $nav = new Zend_Navigation($navLinks);
		    
		    $menuList = $mdlMenu->getByStatus(1);
		    foreach ($menuList as $menu) {
		    	
		    	$options = array(
	    			'id' => 'menu-'.$menu->id,
	    			'label' => $menu->name,
	    			'uri'   => '',
		    	);
		    	$page = Zend_Navigation_Page::factory( $options );
		    	$nav->addPage( $page );
		    	
		    	$menuitemList = $mdlMenuItem->getItemsForNavigationByMenu($menu);
		    	foreach ( $menuitemList as $menuItem )
		    	{
		    		if ( $menuItem->parent_id == 0 ) {
		    			if ( $menuItem->external == 1 ) {
		    				$this->addExternalPage($page, $menuItem);
		    			} else {
		    				$this->addInternalPage($page, $menuItem);
		    			}
		    		} else {
		    			$parent = $nav->findBy('id', 'mii-'.$menuItem->parent_id);
		    			if ( $menuItem->external == 1 ) {
		    				$this->addExternalPage($parent, $menuItem);
		    			} else {
		    				$this->addInternalPage($parent, $menuItem);
		    			}
		    		}
		    	}
		    }
		    
		    $this->addCurrentPageUnregistered($nav, $request);
		    
		    $page = $nav->findBy("id", "mii-".$request->getParam("mid"));
		    if ( $page ) $page->setActive(true);
		    
		    Zend_Registry::set('Zend_Navigation', $nav);
		    #Zend_Debug::dump($nav->toArray());
		    #die();
		    
		} catch (Exception $e) {
		    try {
		        $writer = new Zend_Log_Writer_Stream(APPLICATION_LOG_PATH . 'plugins.log');
		        $logger = new Zend_Log($writer);
		        $logger->log($e->getMessage(), Zend_Log::ERR);
		    } catch (Exception $e) {
		    }
		} 
	}
	
	/**
	 * 
	 * @param Zend_Navigation_Page $pageParent
	 * @param Zend_Db_Table_Row_Abstract $menuItem
	 * @return Ambigous <Zend_Navigation_Page, Zend_Navigation_Page_Mvc, Zend_Navigation_Page_Uri, unknown>
	 */
	private function addExternalPage( Zend_Navigation_Page $pageParent, Zend_Db_Table_Row_Abstract $menuItem )
	{
		$options = array(
			'id' => 'mii-'.$menuItem->id,
			'label' => $menuItem->title,
			'title' => $menuItem->title,
			'uri' 	=> $this->getParamByKey( $menuItem, 'linkt'),
			'target' => $menuItem->wtype,
			'resource' => strtolower( $menuItem->module.':'.$menuItem->controller ),
			'privilege' => strtolower( $menuItem->actioncontroller ),
			'order' => $menuItem->ordering,
			'visible' => $menuItem->isvisible,
			'class' => $menuItem->css_class
		);
		$page = Zend_Navigation_Page::factory($options);
		$pageParent->addPage($page);
		return $page;
	}
	
	/**
	 *
	 * @param Zend_Navigation_Page $pageParent
	 * @param Zend_Db_Table_Row_Abstract $menuItem
	 * @return Ambigous <Zend_Navigation_Page, Zend_Navigation_Page_Mvc, Zend_Navigation_Page_Uri, unknown>
	 */
	private function addInternalPage( Zend_Navigation_Page $pageParent, Zend_Db_Table_Row_Abstract $menuItem )
	{
		
		$options = array(
			'id' => 'mii-'.$menuItem->id,
			'label' => $menuItem->title,
			'title' => $menuItem->title,
			'target' => $menuItem->wtype,
			'resource' => strtolower( $menuItem->module.':'.$menuItem->controller ),
			'privilege' => strtolower( $menuItem->actioncontroller ),
			'order' => $menuItem->ordering,
			'visible' => $menuItem->isvisible,
			'class' => $menuItem->css_class,
			'module' => $menuItem->module,
			'controller' => $menuItem->controller,
			'action' => $menuItem->actioncontroller,
		);
		
		$params = array();
		$subItemsParams = Zend_Json::decode($menuItem->params);
		if ( !is_null($subItemsParams) ) $params = $subItemsParams;
		$page = Zend_Navigation_Page::factory($options);
		$page->addParams($params);
	    $pageParent->addPage($page);
	    
	    if ( strlen($menuItem->id_alias) > 0 ) {
	        $page->setRoute( $menuItem->id_alias );
	    } else $page->setRoute( strtolower($menuItem->module.'-'.$menuItem->controller.'-'.$menuItem->actioncontroller) );
	    
		return $page;
	}
	
	/**
	 * Get param by key
	 * @access protected
	 * @param Zend_Db_Table_Row_Abstract $item
	 * @param string $key
	 * @return string
	 */
	protected function getParamByKey( Zend_Db_Table_Row_Abstract $item, $key )
	{
	    $params = Zend_Json::decode($item->params);
	    foreach ($params as $param) {
	        if ( isset($param[$key]) )
	        {
	            return $param[$key];
	        } else return '';
	    }
	    return '';
	}
	
	private function addCurrentPageUnregistered( Zend_Navigation $nav, Zend_Controller_Request_Abstract $request )
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
				'miid' => 0,
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