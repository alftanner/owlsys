<?php

class Menu_WidgetController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function simpleAction()
    {
        try {
            $auth = Zend_Auth::getInstance();
            $acl = Zend_Registry::get('ZendACL');
            if ( $auth->hasIdentity() ) {
            	$identity = $auth->getIdentity();
            	$role = intval( $identity->role_id );
            }else{
            	$role=3;
            }
            
            $params = $this->getRequest()->getParams();
            $navigation = Zend_Layout::getMvcInstance()->getView()->navigation();
            $navigation->setAcl($acl)->setRole( strval($role) );
            $menuId = trim($params['menuId']);
            $menuSelected = $navigation->findOneById( 'menu-'.$menuId );
            $menuItemSelected = $navigation->findBy( 'active', 1 );
            if ( $menuSelected->id == 0 ) $menuItemSelected = $menuItemSelected->_parent;
            #Zend_Debug::dump($menuItemSelected);
            #die();
            $this->view->menuItemSelected = $menuItemSelected;
            $this->view->menuId = $menuId;
            $menu = $navigation->menu();
            $css = "menu-".$menuId.' ';
            if ( array_key_exists('css', $params) ) {
                $css .= trim($params['css'])." ";
            } 
            $menu->setUlClass( $css );
            echo $menu->renderMenu($menuSelected);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return null;
    }


}



