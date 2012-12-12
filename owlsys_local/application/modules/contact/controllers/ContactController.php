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
class Contact_ContactController extends Zend_Controller_Action
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
     * Index action for contact controller
     */
    public function indexAction()
    {
        // action body
    }

    /**
     * List registered for contact controller
     */
    public function listregisteredAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$mdlContact = new Contact_Model_Contact();
        	$adapter = $mdlContact->getPaginatorAdapterList();
        	$paginator = new Zend_Paginator($adapter);
        	$paginator->setItemCountPerPage(10);
        	$pageNumber = $this->getRequest()->getParam('page',1);
        	$paginator->setCurrentPageNumber($pageNumber);
        	$this->view->contacts = $paginator;
        } catch (Exception $e) {
        	echo $e->getMessage();
        }
        return;
    }

    /**
     * Add action for contact controller
     */
    public function addAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$frmContact = new Contact_Form_Contact( );
        	$frmContact->setAction( $this->_request->getBaseUrl() . "/contact/contact/add" );
        	
        	$mdlAccount = new Acl_Model_Account();
        	$accountList = $mdlAccount->getSimpleList();
        	$cbAccount = $frmContact->getElement('account_id');
        	foreach ( $accountList as $account )
        	{
        		$cbAccount->addMultiOption( $account->id, $account->email );
        	}
        	$mdlCategory = new Contact_Model_Category();
        	$categoryList = $mdlCategory->getSimpleList();
        	$cbCategory = $frmContact->getElement('category_id');
        	foreach ( $categoryList as $category )
        	{
        		$cbCategory->addMultiOption( $category->id, $category->title );
        	}
        
        	if ( $this->getRequest()->isPost() )
        	{
        		if ( $frmContact->isValid( $_POST ) )
        		{
        			$mdlContact = new Contact_Model_Contact();
        			$fileName = '';
        			if ( $frmContact->image->isUploaded() )
        			{
        				$ext = end(explode('.', $frmContact->image->getFileName()));
        				$frmContact->image->addFilter( 'Rename', implode('_', array('cc', date('YmdHis'))).'.'.$ext );
        				$frmContact->image->receive();
        				$fileName = $frmContact->image->getFileName(null, false);
        				chmod(DIR_MOD_CONTACT_IMG_UPLOADS.'/'.$fileName, 0755);
        				$thumb = Zend_Layout::getMvcInstance()->getView()->thumbnail(
        						DIR_MOD_CONTACT_IMG_UPLOADS.'/'.$fileName,
        						70, 70,
        						DIR_MOD_CONTACT_THUMB_UPLOADS.'/',
        						DIR_MOD_CONTACT_THUMB_UPLOADS
        				);
        				chmod($thumb, 0755);
        			}
        			$contact = $mdlContact->createRow( $frmContact->getValues() );
        			$contact->image = $fileName;
        			$mdlContact->save($contact);
        			$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_CREATED_SUCCESSFULLY") ) );
        			$this->_helper->redirector( "listregistered", "contact", "contact" );
        		}
        	} else {
        		$fields = array();
        		foreach ( $frmContact->getElements() as $element ) $fields[] = $element->getName();
        		$frmContact->addDisplayGroup( $fields, 'form', array( 'legend' => "CONTACT_ADD", ) );
        	}
        	
        	$this->view->frmContact = $frmContact;
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "listregistered", "contact", "contact" );
        }
        return;
    }

    /**
     * Edit action for contact controller
     * @throws Exception
     */
    public function editAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$frmContact = new Contact_Form_Contact();
        	$frmContact->setAction( $this->_request->getBaseUrl() . "/contact/contact/edit" );
        	$id = $this->getRequest()->getParam('id', 0);
        	$mdlContact = new Contact_Model_Contact();
        	$contact = $mdlContact->find( $id )->current();
        	if ( !$contact ) {
        		throw new Exception($translate->translate("LBL_ROW_NOT_FOUND"));
        	}
        	
        	$mdlAccount = new Acl_Model_Account();
        	$accountList = $mdlAccount->getSimpleList();
        	$cbAccount = $frmContact->getElement('account_id');
        	foreach ( $accountList as $account )
        	{
        		$cbAccount->addMultiOption( $account->id, $account->email );
        	}
        	$mdlCategory = new Contact_Model_Category();
        	$categoryList = $mdlCategory->getSimpleList();
        	$cbCategory = $frmContact->getElement('category_id');
        	foreach ( $categoryList as $category )
        	{
        		$cbCategory->addMultiOption( $category->id, $category->title );
        	}
        
        	if ( $this->getRequest()->isPost() )
        	{
        		if ( $frmContact->isValid( $_POST ) )
        		{
        			$fileName = $contact->image;
        			if ( $frmContact->image->isUploaded() )
        			{
        				$ext = end(explode('.', $frmContact->image->getFileName()));
        				$frmContact->image->addFilter( 'Rename', implode('_', array('cc', date('YmdHis'))).'.'.$ext );
        				$frmContact->image->receive();
        				$fileName = $frmContact->image->getFileName(null, false);
        				chmod(DIR_MOD_CONTACT_IMG_UPLOADS.'/'.$fileName, 0755);
        				$thumb = Zend_Layout::getMvcInstance()->getView()->thumbnail(
        						DIR_MOD_CONTACT_IMG_UPLOADS.'/'.$fileName,
        						70, 70,
        						DIR_MOD_CONTACT_THUMB_UPLOADS.'/',
        						DIR_MOD_CONTACT_THUMB_UPLOADS
        				);
        				chmod($thumb, 0755);
        				# eliminando original
        				unlink( DIR_MOD_CONTACT_IMG_UPLOADS.'/'.$contact->image );
        				$imgParts = explode('.',$contact->image);
        				$nameImg = current($imgParts);
        				$ext = end( $imgParts );
        				# eliminando thumb original
        				unlink( DIR_MOD_CONTACT_THUMB_UPLOADS.'/'.$nameImg.'_thumb.'.$ext );
        			}
        			$contact->setFromArray( $frmContact->getValues() );
        			$contact->image = $fileName;
        			$contact->save();
        			$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_UPDATED_SUCCESSFULLY") ) );
        			$this->_helper->redirector( "listregistered", "contact", "contact" );
        		}
        	} else {
        	    $frmContact->populate( $contact->toArray() );
        	    $fields = array();
        	    foreach ( $frmContact->getElements() as $element ) $fields[] = $element->getName();
        	    $frmContact->addDisplayGroup( $fields, 'form', array( 'legend' => "CONTACT_EDIT", ) );
        	}
        	 
        	$this->view->frmContact = $frmContact;
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "listregistered", "contact", "contact" );
        }
        return;
    }

    /**
     * Publish action for contact controller
     * @throws Exception
     */
    public function publishAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$mdlContact = new Contact_Model_Contact();
        	$id = $this->getRequest()->getParam('id', 0);
        	$contact = $mdlContact->find( $id )->current();
        	if ( !$contact ) {
        		throw new Exception($translate->translate("LBL_ROW_NOT_FOUND"));
        	}
        	if ( $contact->published == 0 ) {
        		$contact->published = 1;
        		$contact->save();
        		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_PUBLISHED_SUCCESSFULLY") ) );
        	} else {
        		$contact->published = 0;
        		$contact->save();
        		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_UNPUBLISHED_SUCCESSFULLY") ) );
        	}
        	$this->_helper->redirector( "listregistered", "contact", "contact" );
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "listregistered", "contact", "contact" );
        }
        return;
    }

    /**
     * Remove action for contact controller
     * @throws Exception
     */
    public function removeAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$id = $this->getRequest()->getParam("id");
        	$mdlContact = new Contact_Model_Contact();
        	$contact = $mdlContact->find( $id )->current();
        	if ( !$contact )  throw new Exception($translate->translate("LBL_ROW_NOT_FOUND"));
        	$contact->delete();
        	$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_DELETED_SUCCESSFULLY") ) );
        	$this->_helper->redirector( "listregistered", "contact", "contact" );
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "listregistered", "contact", "contact" );
        }
        return;
    }

    /**
     * Move action for contact controller
     * @throws Exception
     */
    public function moveAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$id = $this->_request->getParam( 'id' );
        	$direction = $this->_request->getParam('direction');
        	$mdlContact = new Contact_Model_Contact();
        	$contact = $mdlContact->find( $id )->current();
        	if ( !$contact ) {
        		throw new Exception($translate->translate("MENU_ITEM_NOT_FOUND"));
        	}
        	if ( !in_array($direction, array('down', 'up')) ) {
        		throw new Exception($translate->translate("LBL_UP_DOWN_NOT_SPECIFIED"));
        	}
        	if ( $direction == "up" )
        	{
        		$mdlContact->moveUp($contact);
        		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_MOVED_UP_SUCCESSFULLY") ) );
        	} elseif ( $direction == "down" )
        	{
        		$mdlContact->moveDown($contact);
        		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_MOVED_DOWN_SUCCESSFULLY") ) );
        	}
        	$this->_helper->redirector( "listregistered", "contact", "contact" );
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "listregistered", "contact", "contact" );
        }
        return;
    }

    /**
     * View action for contact controller
     * @throws Exception
     */
    public function viewAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$mdlContact = new Contact_Model_Contact();
        	$frmContact = new Contact_Form_Contact( array('type'=>'public') );
        	
        	$params = $this->getRequest()->getParams();
        	$contact = $mdlContact->find( (int)$params['contact'] )->current();
        	if ( !$contact )
        	{
        	    throw new Exception($translate->translate("CONTACT_ROW_NOT_FOUND"));
        	}
        	
        	if( $this->getRequest()->isPost() )
        	{
        	    if ( $frmContact->isValid( $_POST ) )
        	    {
        	        
        	        $mdlAccount = new Acl_Model_Account();
        	        $account = $mdlAccount->find( (int) $contact->account_id )->current();
        	        $emailTo = ( strlen($contact->email_to) > 1 ) ? $contact->email_to : $account->email;
        	        
        	        $mail = new Zend_Mail();
        	        $mail->setBodyText( $frmContact->getElement('message')->getValue() )
	        	        ->setFrom(
	        	        	$frmContact->getElement('email')->getValue(), 
	        	        	$frmContact->getElement('fullname')->getValue())
	        	        ->addTo(
	        	        	$emailTo, 
	        	        	$account->first_name.' '.$account->last_name)
	        	        ->setSubject( $translate->translate('CONTACT_DEFAULT_SUBJECT') )
	        	        ->send();
        	       	$frmContact->reset();
        	    }
        	} else {
        		$fields = array();
        		foreach ( $frmContact->getElements() as $element ) $fields[] = $element->getName();
        		$frmContact->addDisplayGroup( $fields, 'form', array( 'legend' => "CONTACT", ) );
        	}
        	
        	$frmContact->setAction( $this->_request->getBaseUrl() . "/contact/contact/view" );
        	$this->view->frmContact = $frmContact;
        } catch (Exception $e) {
        	#$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	#$this->_helper->redirector( "index", "contact", "contact" );
        	echo $e->getMessage();
        }
        return;
    }


}

