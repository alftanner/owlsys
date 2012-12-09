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
 * @package contact
 * @subpackage controllers
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Contact_CategoryController extends Zend_Controller_Action
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
     * Index action for category controller
     */
    public function indexAction()
    {
        // action body
    }

    /**
     * List registered action for category controller
     */
    public function listregisteredAction()
    {
        // action body
        try {
            $translate = Zend_Registry::get('Zend_Translate');
            $mdlCategory = new Contact_Model_Category();
            $adapter = $mdlCategory->getPaginatorAdapterList();
            $paginator = new Zend_Paginator($adapter);
            $paginator->setItemCountPerPage(10);
            $pageNumber = $this->getRequest()->getParam('page',1);
            $paginator->setCurrentPageNumber($pageNumber);
            $this->view->categories = $paginator;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return;
    }

    /**
     * Add action for category controller
     */
    public function addAction()
    {
        // action body
        try {
            $translate = Zend_Registry::get('Zend_Translate');
            $frmCategory = new Contact_Form_Category();
            $frmCategory->setAction( $this->_request->getBaseUrl() . "/contact/category/add" );

            $this->view->frmCategory = $frmCategory;
            if ( $this->getRequest()->isPost() )
            {
            	if ( $frmCategory->isValid( $_POST ) )
            	{
            	    $mdlCategory = new Contact_Model_Category();
            	    $fileName = '';
            	    if ( $frmCategory->image->isUploaded() )
            	    {
            	        $ext = end(explode('.', $frmCategory->image->getFileName()));
            	        $frmCategory->image->addFilter( 'Rename', implode('_', array('cc', date('YmdHis'))).'.'.$ext );
            	        $frmCategory->image->receive();
            	        $fileName = $frmCategory->image->getFileName(null, false);
            	        chmod(DIR_MOD_CONTACT_IMG_UPLOADS.'/'.$fileName, 0755);
            	        $thumb = Zend_Layout::getMvcInstance()->getView()->thumbnail(
            	    	    DIR_MOD_CONTACT_IMG_UPLOADS.'/'.$fileName,
	            	        70, 70,
	            	        DIR_MOD_CONTACT_THUMB_UPLOADS.'/',
            	        	DIR_MOD_CONTACT_THUMB_UPLOADS 
            	        );
            	        chmod($thumb, 0755);
            	        #chmod(DIR_MOD_CONTACT_IMG_UPLOADS.'/'.$fileName, 0755);
            	    }
            	    $category = $mdlCategory->createRow( $frmCategory->getValues() );
            	    $category->image = $fileName;
            	    $category->save();
            	    $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_CREATED_SUCCESSFULLY") ) );
            	    $this->_helper->redirector( "listregistered", "category", "contact" );
            	}
            	
            } else {
            	$fields = array();
            	foreach ( $frmCategory->getElements() as $element ) $fields[] = $element->getName();
            	$frmCategory->addDisplayGroup( $fields, 'form', array( 'legend' => "CONTACT_ADD_CATEGORY", ) );
            }
        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
            $this->_helper->redirector( "listregistered", "category", "contact" );
        }
        return;
    }

    /**
     * Edit action for category controller
     * @throws Exception
     */
    public function editAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$frmCategory = new Contact_Form_Category();
        	$frmCategory->setAction( $this->_request->getBaseUrl() . "/contact/category/edit" );
        	$mdlCategory = new Contact_Model_Category();
        	$id = $this->getRequest()->getParam('id', 0);
        	$category = $mdlCategory->find( $id )->current();
        	if ( !$category ) {
        		throw new Exception($translate->translate("LBL_ROW_NOT_FOUND"));
        	}
        	if ( $this->getRequest()->isPost() )
        	{
        		if ( $frmCategory->isValid( $_POST ) )
        		{
        		    $fileName = $category->image;
        			if ( $frmCategory->image->isUploaded() )
        			{
        				$ext = end(explode('.', $frmCategory->image->getFileName()));
        				$frmCategory->image->addFilter( 'Rename', implode('_', array('cc', date('YmdHis'))).'.'.$ext );
        				$frmCategory->image->receive();
        				$fileName = $frmCategory->image->getFileName(null, false);
        				chmod(DIR_MOD_CONTACT_IMG_UPLOADS.'/'.$fileName, 0755);
        				$thumb = Zend_Layout::getMvcInstance()->getView()->thumbnail(
        						DIR_MOD_CONTACT_IMG_UPLOADS.'/'.$fileName,
        						70, 70,
        						DIR_MOD_CONTACT_THUMB_UPLOADS.'/',
        						DIR_MOD_CONTACT_THUMB_UPLOADS
        				);
        				chmod($thumb, 0755);
        				# eliminando original
        				unlink( DIR_MOD_CONTACT_IMG_UPLOADS.'/'.$category->image );
        				$imgParts = explode('.',$category->image);
        				$nameImg = current($imgParts);
        				$ext = end( $imgParts );
        				# eliminando thumb original
        				unlink( DIR_MOD_CONTACT_THUMB_UPLOADS.'/'.$nameImg.'_thumb.'.$ext );
        				#chmod(DIR_MOD_CONTACT_IMG_UPLOADS.'/'.$fileName, 0755);
        			}
        			$category->setFromArray( $frmCategory->getValues() );
        			$category->image = $fileName;
        			$category->save();
        			$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_UPDATED_SUCCESSFULLY") ) );
        			$this->_helper->redirector( "listregistered", "category", "contact" );
        		}
        		 
        	} else {
        	    $frmCategory->populate( $category->toArray() );
        	    
        	    $fields = array();
        	    foreach ( $frmCategory->getElements() as $element ) $fields[] = $element->getName();
        	    $frmCategory->addDisplayGroup( $fields, 'form', array( 'legend' => "CONTACT_EDIT_CATEGORY", ) );
        	}
        	$this->view->frmCategory = $frmCategory;
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "listregistered", "category", "contact" );
        }
        return;
    }

    /**
     * Publish action category controller
     * @throws Exception
     */
    public function publishAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$mdlCategory = new Contact_Model_Category();
        	$id = $this->getRequest()->getParam('id', 0);
        	$category = $mdlCategory->find( $id )->current();
        	if ( !$category ) {
        		throw new Exception($translate->translate("LBL_ROW_NOT_FOUND"));
        	}
        	if ( $category->published == 0 ) {
        		$category->published = 1;
        		$category->save();
        		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_PUBLISHED_SUCCESSFULLY") ) );
        	} else {
        		$category->published = 0;
        		$category->save();
        		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_UNPUBLISHED_SUCCESSFULLY") ) );
        	}
        	$this->_helper->redirector( "listregistered", "category", "contact" );
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "listregistered", "category", "contact" );
        }
        return;
    }

    /**
     * Remove action for category controller
     * @throws Exception
     */
    public function removeAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$id = $this->getRequest()->getParam("id");
        	$mdlCategory = new Contact_Model_Category();
        	$category = $mdlCategory->find( $id )->current();
        	if ( !$category )  throw new Exception($translate->translate("LBL_ROW_NOT_FOUND"));
        	$category->delete();
        	$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_DELETED_SUCCESSFULLY") ) );
        	$this->_helper->redirector( "listregistered", "category", "contact" );
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "listregistered", "category", "contact" );
        }
        return;
    }

    /**
     * View action for category controller
     * @throws Exception
     */
    public function viewAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	
        	$params = $this->getRequest()->getParams();
        	$mdlCategory = new Contact_Model_Category();
        	$mdlContact = new Contact_Model_Contact();
        	$category = $mdlCategory->find( (int) $params['category'] )->current();
        	
        	if ( !$category ) {
        		throw new Exception($translate->translate("CONTACT_ROW_NOT_FOUND"));
        	}
        	
        	$contactList = $mdlContact->getByCategory($category);
        	
        	$this->view->contacts = $contactList;
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "index", "contact", "contact" );
        }
        return;
    }


}

