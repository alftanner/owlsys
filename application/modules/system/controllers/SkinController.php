<?php

class System_SkinController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function listAction()
    {
    	$translate = Zend_Registry::get('Zend_Translate');
        try {
        	$mdlSkin = new System_Model_Skin();
        	$adapter = $mdlSkin->getPaginatorAdapterList();
        	$paginator = new Zend_Paginator($adapter);
        	$paginator->setItemCountPerPage(10);
        	$pageNumber = $this->getRequest()->getParam('page',1);
        	$paginator->setCurrentPageNumber($pageNumber);
        	$this->view->skins = $paginator;
        } catch (Exception $e) {
        	echo $e->getMessage();
        }
        return;
    }

    public function selectAction()
    {
    	$translate = Zend_Registry::get('Zend_Translate');
    	try {
    		 $id = $this->getRequest()->getParam('id', 0);
    		 
    		 $mdlSkin = new System_Model_Skin();
    		 $skin = $mdlSkin->find( $id )->current();
    		 if ( !$skin ) throw new Exception($translate->translate("SYSTEM_SKIN_NOT_FOUND"));
    		 
    		 $skinSelected = $mdlSkin->getSelected();
    		 $skinSelected->isselected = 0;
    		 $skinSelected->save();
    		 
    		 $skin->isselected = 1;
    		 $skin->save();
    		 
    		 $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'header'=>'', 'message' => $translate->translate("LBL_CHANGES_SAVED") ) );
    		 return $this->_helper->redirector( "list", "skin", "system" );
    		 
    	} catch (Exception $e) {
    		$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'header'=>'', 'message' => $e->getMessage() ) );
        	$this->_helper->redirector( "list", "skin", "system" );
    	}
    	return;
    }


}





