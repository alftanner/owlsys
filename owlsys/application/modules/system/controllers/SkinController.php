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
    	$mdlSkin = new System_Model_Skin();
    	$adapter = $mdlSkin->getAdapter();
    	try {
          $id = $this->getRequest()->getParam('id', 0);

          $skinSelected = $mdlSkin->createRow();
          $skin = $mdlSkin->find($id)->current();
          
          $adapter->beginTransaction();
          $skinSelected = $mdlSkin->getSkinSelected();
          $skinSelected->isSelected = 0;
          $skinSelected->save();
          
          $skin->isSelected = 1;
          $skin->save();
          $adapter->commit();
          $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("LBL_CHANGES_SAVED") ) );
          $this->redirect('skins-list');
    		 
    	} catch (Exception $e) {
    	  $adapter->rollBack();
    		$this->_helper->flashMessenger->addMessage( array('type'=>'error', 'message' => $e->getMessage() ) );
        	$this->redirect('skins');
    	}
    	return;
    }


}





