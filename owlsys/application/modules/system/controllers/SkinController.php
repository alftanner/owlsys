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
        	$mdlSkin = System_Model_SkinMapper::getInstance();
        	$adapter = $mdlSkin->getList();
        	$paginator = Zend_Paginator::factory($adapter);
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
    		 
    		 $mdlSkin = System_Model_SkinMapper::getInstance();
    		 $skin = new System_Model_Skin();
    		 $skinSelected = new System_Model_Skin();
    		 $mdlSkin->find($id, $skin);
    		 
    		 $adapter = $mdlSkin->getAdapter();
    		 $adapter->beginTransaction();
    		 $mdlSkin->getSkinSelected($skinSelected);
    		 $skinSelected->setIsSelected(0);
    		 $mdlSkin->save($skinSelected);
    		 
    		 $skin->setIsSelected(0);
    		 $mdlSkin->save($skin);
    		 $adapter->commit();
    		 $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("LBL_CHANGES_SAVED") ) );
    		 $this->redirect('skins-list');
    		 
    	} catch (Exception $e) {
    		$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('skins');
    	}
    	return;
    }


}





