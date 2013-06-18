<?php

class Acl_WidgetController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    /**
     * Display a profile information login
     */
    public function statusAction()
    {
        try {
            $auth = Zend_Auth::getInstance();
            $identity = $auth->getIdentity();
            $this->view->identity = $identity;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }


}



