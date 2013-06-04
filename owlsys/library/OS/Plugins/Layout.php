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
class OS_Plugins_Layout extends Zend_Controller_Plugin_Abstract
{
	
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Plugin_Abstract::preDispatch()
     */
	public function preDispatch( Zend_Controller_Request_Abstract $request)
	{
		try {
			
			$module 	= strtolower( $this->getRequest()->getModuleName() );
			$controller = strtolower ( $this->_request->getControllerName () );
			$action 	= strtolower ( $this->_request->getActionName () );
			
			$auth = Zend_Auth::getInstance();
			
			$mdlRoleMapper = Acl_Model_RoleMapper::getInstance();
			$layoutMapper = System_Model_LayoutMapper::getInstance();
			$mdlSkinMapper = System_Model_SkinMapper::getInstance();
			$role = new Acl_Model_Role();
			$layout = new System_Model_Layout();
			$skin = new System_Model_Skin();
			
			if ( $auth->hasIdentity() ) {
				$identity = $auth->getIdentity();
				$mdlRoleMapper->find( intval($identity->role_id), $role );
			} else $mdlRoleMapper->find( 3, $role );
			
			$layoutMapper->find($role->getLayout()->getId(), $layout);
			
			$mdlSkinMapper->getSkinSelected($skin);
			$skinName = strtolower($skin->getName());
			
			$layoutPath = Zend_Layout::getMvcInstance()->getLayoutPath();
			
			Zend_Layout::getMvcInstance()->setLayoutPath( APPLICATION_PATH.'/layouts/scripts/'.$skinName );
			Zend_Layout::getMvcInstance()->setLayout( $layout->getName() );
			
		} catch (Exception $e) {
		    
		    //Zend_Debug::dump($e->getTraceAsString());
			$layout = "frontend";
			
			Zend_Layout::getMvcInstance()->setLayoutPath( APPLICATION_PATH.'/layouts/scripts/default' );
			Zend_Layout::getMvcInstance()->setLayout( $layout );
			try {
			    $writer = new Zend_Log_Writer_Stream(APPLICATION_LOG_PATH . 'plugins.log');
			    $logger = new Zend_Log($writer);
			    $logger->log($e->getMessage(), Zend_Log::ERR);
			} catch (Exception $e) {
			}
		}

	} 
}