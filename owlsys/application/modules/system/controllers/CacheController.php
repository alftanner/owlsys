<?php

class System_CacheController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    /**
     * List all registered tags in cache
     */
    public function indexAction()
    {
      try {
        /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
        $cache = Zend_Registry::get('cache');
        $this->view->tags = $cache->getTags();
      } catch (Exception $e) {
      }
    }

    public function cleantagAction()
    {
      $request = $this->getRequest();
      $response = $this->getResponse();
      $translate = Zend_Registry::get('Zend_Translate');
      try {
        $tag = $request->getParam('tag');
        /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
        $cache = Zend_Registry::get('cache');
        $cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($tag));
        
        $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("LBL_CHANGES_SAVED") ) );
        $this->redirect('cache-manager');
      } catch (Exception $e) {
        $response->appendBody("<div class='span12'>");
        $response->appendBody($e->getMessage());
        $response->appendBody("</div>");
        $response->setHttpResponseCode(404);
        $this->_helper->viewRenderer->setNoRender(true);
      }
    }

    public function cleanallAction()
    {
      $request = $this->getRequest();
      $response = $this->getResponse();
      $translate = Zend_Registry::get('Zend_Translate');
      try {
        /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
        $cache = Zend_Registry::get('cache');
        $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
        
        $this->_helper->flashMessenger->addMessage( array('type'=>'info', 'message' => $translate->translate("LBL_CHANGES_SAVED") ) );
        $this->redirect('cache-manager');
        
      } catch (Exception $e) {
        $response->appendBody("<div class='span12'>");
        $response->appendBody($e->getMessage());
        $response->appendBody("</div>");
        $response->setHttpResponseCode(404);
        $this->_helper->viewRenderer->setNoRender(true);
      }
    }


}





