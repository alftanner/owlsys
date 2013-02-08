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
 * @package content
 * @subpackage controllers
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Content_CategoryController extends Zend_Controller_Action
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
     * index action for category controller
     */
    public function indexAction()
    {
        // action body
    }

    /**
     * add action for category controller
     */
    public function addAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$frmCategory = new Content_Form_Category();
        	$frmCategory->setAction( $this->_request->getBaseUrl() . "/content/category/add" );
        	$this->view->frmCategory = $frmCategory;
        	
        	$mdlCategory = new Content_Model_Category();
        	$categories = $mdlCategory->getSimpleList();
        	$cbParent = $frmCategory->getElement('parent_id');
        	$cbParent->addMultiOption( 0, $translate->translate("LBL_NOT_PARENT") );
        	foreach ( $categories as $category )
        	{
        	    $cbParent->addMultiOption( $category->id, $category->title );
        	}
        	
        	if ( $this->getRequest()->isPost() )
        	{
        		if ( $frmCategory->isValid( $this->getRequest()->getParams() ) )
        		{
        		    $category = $mdlCategory->createRow( $frmCategory->getValues() );
        		    $category->description = htmlentities( $frmCategory->getElement('description')->getValue() );
        		    $mdlCategory->save($category);
        		    $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_CREATED_SUCCESSFULLY") ) );
        		    $this->_helper->redirector( "listregistered", "category", "content" );
        		}
        	} else {
        		/*$fields = array();
        		foreach ( $frmCategory->getElements() as $element ) $fields[] = $element->getName();
        		$frmCategory->addDisplayGroup( $fields, 'form', array( 'legend' => "CONTENT_ADD_CATEGORY", ) );*/
        	}
        	
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "listregistered", "category", "content" );
        }
        return;
    }

    /**
     * edit action for category controller
     * @throws Zend_Exception
     */
    public function editAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$id = $this->getRequest()->getParam("id");
        	$mdlCategory = new Content_Model_Category();
        	$category = $mdlCategory->find( intval($id) )->current();
        	if ( !$category ) throw new Zend_Exception($translate->translate("LBL_ROW_NOT_FOUND")); 
        	$frmCategory = new Content_Form_Category();
        	$frmCategory->setAction( $this->_request->getBaseUrl() . "/content/category/edit" );
        	$this->view->frmCategory = $frmCategory;
        	
        	$categories = $mdlCategory->getSimpleList();
        	$cbParent = $frmCategory->getElement('parent_id');
        	$cbParent->addMultiOption( 0, $translate->translate("LBL_NOT_PARENT") );
        	foreach ( $categories as $categoryTemp )
        	{
        		$cbParent->addMultiOption( $categoryTemp->id, $categoryTemp->title );
        	}
        	
        	$category->description = html_entity_decode( $category->description );
        	$frmCategory->populate( $category->toArray() );
        	 
        	if ( $this->getRequest()->isPost() )
        	{
        		if ( $frmCategory->isValid( $this->getRequest()->getParams() ) )
        		{
        			$category->title = $frmCategory->getElement('title')->getValue();
        			$category->description = htmlentities( $frmCategory->getElement('description')->getValue() );
        			$category->parent_id = $frmCategory->getElement('parent_id')->getValue();
        			$category->published = $frmCategory->getElement('published')->getValue();
        			$category->save();
        			$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_UPDATED_SUCCESSFULLY") ) );
        			$this->_helper->redirector( "listregistered", "category", "content" );
        		}
        	} else {
        		/*$fields = array();
        		foreach ( $frmCategory->getElements() as $element ) $fields[] = $element->getName();
        		$frmCategory->addDisplayGroup( $fields, 'form', array( 'legend' => "CONTENT_EDIT_CATEGORY", ) );*/
        	}
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "listregistered", "category", "content" );
        }
        return;
    }

    /**
     * move action for category controller
     * @throws Zend_Exception
     */
    public function moveAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$id = $this->getRequest()->getParam("id");
        	$direction = $this->_request->getParam('direction');
        	$mdlCategory = new Content_Model_Category();
        	$category = $mdlCategory->find( intval($id) )->current();
        	if ( !$category ) throw new Zend_Exception($translate->translate("LBL_ROW_NOT_FOUND"));
        	if ( !in_array($direction, array('down', 'up')) ) {
        		throw new Zend_Exception($translate->translate("LBL_UP_DOWN_NOT_SPECIFIED"));
        	}
        	if ( $direction == "up" )
        	{
        		$mdlCategory->moveUp( $category );
        		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_MOVED_UP_SUCCESSFULLY") ) );
        	} elseif ( $direction == "down" )
        	{
        		$mdlCategory->moveDown( $category );
        		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_MOVED_DOWN_SUCCESSFULLY") ) );
        	}
        	$this->_helper->redirector( "listregistered", "category", "content" );
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "listregistered", "category", "content" );
        }
        return;
    }

    /**
     * publish action for category controller
     * @throws Zend_Exception
     */
    public function publishAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$id = $this->getRequest()->getParam( "id" );
        	$mdlCategory = new Content_Model_Category();
        	$category = $mdlCategory->find( intval($id) )->current();
        	if ( !$category ) throw new Zend_Exception($translate->translate("LBL_ROW_NOT_FOUND"));
        	if ( $category->published == 0 )
        	{
        		$category->published = 1;
        		$category->save();
        		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_PUBLISHED_SUCCESSFULLY") ) );
        	} else {
        		$category->published = 0;
        		$category->save();
        		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_UNPUBLISHED_SUCCESSFULLY") ) );
        	}
        	$this->_helper->redirector( "listregistered", "category", "content" );
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "listregistered", "category", "content" );
        }
        return;
    }

    /**
     * delete action for category controller
     * @throws Zend_Exception
     */
    public function deleteAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$mdlCategory = new Content_Model_Category();
        	$id = $this->getRequest()->getParam('id', 0);
        	$category = $mdlCategory->find( intval($id) )->current();
        	if ( !$category ) throw new Zend_Exception($translate->translate("LBL_ROW_NOT_FOUND"));
        	$category->delete();
        	$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_DELETED_SUCCESSFULLY") ) );
        	$this->_helper->redirector( "listregistered", "category", "content" );
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "listregistered", "category", "content" );
        }
        return;
    }

    /**
     * listregistered action for category controller
     */
    public function listregisteredAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$mdlCategory = new Content_Model_Category();
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
     * viewbloglayout action for category controller
     * @throws Zend_Exception
     */
    public function viewbloglayoutAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	
        	$mdlCategory = new Content_Model_Category();
        	$cid = $this->getRequest()->getParam('catid', 0);
        	$category = $mdlCategory->find( intval($cid) )->current();
        	if ( !$category ) throw new Zend_Exception($translate->translate("LBL_ROW_NOT_FOUND"));
        	
        	$orderField = $this->getRequest()->getParam('of', 'ordering');
        	$orderType = $this->getRequest()->getParam('ot', 'asc');
        	
        	$mdlArticle = new Content_Model_Article();
        	$adapter = $mdlArticle->getByCategory($category, $orderField, $orderType);
        	$paginator = new Zend_Paginator($adapter);
        	$paginator->setItemCountPerPage(10);
        	$pageNumber = $this->getRequest()->getParam('page',1);
        	$paginator->setCurrentPageNumber($pageNumber);
        	$this->view->articles = $paginator;
        	
        } catch (Exception $e) {
            #trigger_error( $e->__toString() );
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "error", "error", "default" );
        }
        return;
    }


}

