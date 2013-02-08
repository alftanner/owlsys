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
class Content_ArticleController extends Zend_Controller_Action
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
     * Index action for article controller
     */
    public function indexAction()
    {
        // action body
    }

    /**
     * listregistered action for article controller
     */
    public function listregisteredAction()
    {
        // action body
        try {
            $mdlArticle = new Content_Model_Article();
            $adapter = $mdlArticle->getList();
            $paginator = Zend_Paginator::factory($adapter);
            $paginator->setItemCountPerPage(10);
            $pageNumber = $this->getRequest()->getParam('page',1);
            $paginator->setCurrentPageNumber($pageNumber);
            $this->view->articles = $paginator;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return;
    }

    /**
     * Add action for article controller
     * @throws Exception
     */
    public function addAction()
    {
        // action body
        try {
            $translate = Zend_Registry::get('Zend_Translate');
            $frmArticle = new Content_Form_Article();
            $frmArticle->setAction( $this->_request->getBaseUrl() . "/content/article/add" );
            $this->view->frmArticle = $frmArticle;
            
            $mdlCategory = new Content_Model_Category();
            $categories = $mdlCategory->getSimpleList();
            $cbParent = $frmArticle->getElement('category_id');
            if ( !$categories ) throw new Exception($translate->translate("CONTENT_CATEGORIES_EMPTY"));
            foreach ( $categories as $category )
            {
            	$cbParent->addMultiOption( $category->id, $category->title );
            }
            
            if ( $this->getRequest()->isPost() )
            {
                if ( $frmArticle->isValid( $this->getRequest()->getParams() ) )
                {
                    $mdlArticle = new Content_Model_Article();
                    $article = $mdlArticle->createRow( $frmArticle->getValues() );
                    $article->introcontent = htmlentities( $frmArticle->getElement('introcontent')->getValue() );
                    $article->content = htmlentities( $frmArticle->getElement('content')->getValue() );
                    $mdlArticle->save($article);
                    $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_CREATED_SUCCESSFULLY") ) );
                    $this->_helper->redirector( "listregistered", "article", "content" );
                }
            } else {
            	#$fields = array();
            	#foreach ( $frmArticle->getElements() as $element ) $fields[] = $element->getName();
            	#$frmArticle->addDisplayGroup( $fields, 'form', array( 'legend' => $translate->translate("CONTENT_ADD_ARTICLE"), ) );
            }
            
        } catch (Exception $e) {
            $this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
            $this->_helper->redirector( "listregistered", "article", "content" );
        }
        return;
    }

    /**
     * edit action for article controller
     * @throws Exception
     */
    public function editAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$id = $this->getRequest()->getParam( "id" );
        	$mdlArticle = new Content_Model_Article();
        	$article = $mdlArticle->find( intval($id) )->current();
        	if ( !$article ) throw new Exception($translate->translate("LBL_ROW_NOT_FOUND"));
			$frmArticle = new Content_Form_Article();
            $frmArticle->setAction( $this->_request->getBaseUrl() . "/content/article/edit" );
            $this->view->frmArticle = $frmArticle;
            
            $mdlCategory = new Content_Model_Category();
            $categories = $mdlCategory->getSimpleList();
            $cbParent = $frmArticle->getElement('category_id');
            foreach ( $categories as $category ) {
            	$cbParent->addMultiOption( $category->id, $category->title );
            }
            
            $article->introcontent = html_entity_decode( $article->introcontent );
            $article->content = html_entity_decode( $article->content );
            $frmArticle->populate( $article->toArray() );
            
            if ( $this->getRequest()->isPost() )
            {
                if ( $frmArticle->isValid( $this->getRequest()->getParams() ) )
                {
                    $article->title = $frmArticle->getElement('title')->getValue();
                    $article->introcontent = htmlentities( $frmArticle->getElement('introcontent')->getValue() );
                    $article->content = htmlentities( $frmArticle->getElement('content')->getValue() );
                    $article->category_id = $frmArticle->getElement('category_id')->getValue();
                    $article->published = $frmArticle->getElement('published')->getValue();
                    $article->save();
                    $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_UPDATED_SUCCESSFULLY") ) );
                    $this->_helper->redirector( "listregistered", "article", "content" );
                }
            } else {
            	/*$fields = array();
            	foreach ( $frmArticle->getElements() as $element ) $fields[] = $element->getName();
            	$frmArticle->addDisplayGroup( $fields, 'form', array( 'legend' => "CONTENT_EDIT_ARTICLE", ) );*/
            }
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "listregistered", "article", "content" );
        }
        return;
    }

    /**
     * move action for article controller
     * @throws Exception
     */
    public function moveAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$id = $this->getRequest()->getParam( "id" );
        	$direction = $this->_request->getParam('direction');
        	$mdlArticle = new Content_Model_Article();
        	$article = $mdlArticle->find( intval($id) )->current();
        	if ( !$article ) throw new Exception($translate->translate("LBL_ROW_NOT_FOUND"));
        	if ( !in_array($direction, array('down', 'up')) ) {
        		throw new Exception($translate->translate("LBL_UP_DOWN_NOT_SPECIFIED"));
        	}
        	if ( $direction == "up" )
        	{
        		$mdlArticle->moveUp( $article );
        		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_MOVED_UP_SUCCESSFULLY") ) );
        	} elseif ( $direction == "down" )
        	{
        		$mdlArticle->moveDown( $article );
        		$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_MOVED_DOWN_SUCCESSFULLY") ) );
        	}
        	$this->_helper->redirector( "listregistered", "article", "content" );
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "listregistered", "article", "content" );
        }
        return;
    }

    /**
     * publish action for article controller
     * @throws Exception
     */
    public function publishAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$id = $this->getRequest()->getParam( "id" );
        	$mdlArticle = new Content_Model_Article();
        	$article = $mdlArticle->find( intval($id) )->current();
        	if ( !$article ) throw new Exception($translate->translate("LBL_ROW_NOT_FOUND"));
        	if ( $article->published == 0 )
        	{
        	    $article->published = 1;
        	    $article->save();
        	    $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_PUBLISHED_SUCCESSFULLY") ) );
        	} else {
        	    $article->published = 0;
        	    $article->save();
        	    $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_UNPUBLISHED_SUCCESSFULLY") ) );
        	}
        	$this->_helper->redirector( "listregistered", "article", "content" );
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "listregistered", "article", "content" );
        }
        return;
    }

    /**
     * delete action for article controller
     * @throws Exception
     */
    public function deleteAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$mdlArticle = new Content_Model_Article();
        	$id = $this->getRequest()->getParam('id', 0);
        	$article = $mdlArticle->find( intval($id) )->current();
        	if ( !$article ) throw new Exception($translate->translate("LBL_ROW_NOT_FOUND"));
        	$article->delete();
        	$this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_ITEM_DELETED_SUCCESSFULLY") ) );
        	$this->_helper->redirector( "listregistered", "article", "content" );
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "listregistered", "article", "content" );
        }
        return;
    }

    /**
     * view action for article controller
     * @throws Exception
     */
    public function viewAction()
    {
        // action body
        try {
        	$translate = Zend_Registry::get('Zend_Translate');
        	$mdlArticle = new Content_Model_Article();
        	$id = $this->getRequest()->getParam('aid', 0);
        	$article = $mdlArticle->find( intval($id) )->current();
        	if ( !$article ) throw new Exception($translate->translate("LBL_ROW_NOT_FOUND"));
        	$this->view->article = $article;
        } catch (Exception $e) {
        	$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "error", "error", "default" );
        }
        return;
    }

    /**
     * getbychar action for article controller
     */
    public function getbycharAction()
    {
        // action body
        try {
            $term = $this->getRequest()->getParam("term");
            $mdlArticle = new Content_Model_Article();
            $articleList = $mdlArticle->getByChar($term);
            $data = array();
            foreach ( $articleList as $article )
            {
            	$data[] = array( "id" => $article->id, "value" => $article->title);
            }
            echo Zend_Json::encode($data);
            $this->_helper->layout()->disableLayout();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }


}

