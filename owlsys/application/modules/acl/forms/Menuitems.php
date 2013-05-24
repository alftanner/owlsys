<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package acl
 * @subpackage forms
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Acl_Form_Menuitems extends menu_Form_Item
{

    /**
     * (non-PHPdoc)
     * @see menu_Form_Item::init()
     */
    public function init()
    {
        parent::init();
        
        switch ($this->getAttrib('menuType')) {
        	case 'login':
        	    $this->login();
        	    break;
        	default:
        		$this->defaultParent();
        		break;
        }
        
    }

    /**
     * 
     */
    private function defaultParent()
    {
    }
    
    /**
     * Add custom fields to menu item form for render a login menu item 
     */
    private function login()
    {
        /*$txtHeaderTitle = $this->createElement("text", "headertitle")
	        ->setOrder( $this->order++ )
	        ->setLabel( "ACL_HEADER_TITLE" )
	        ->addFilter('StripTags')
	        ->addValidator( new Zend_Validate_LessThan(250) )
        ;
        $this->addElement( $txtHeaderTitle );
        
        $txtFooterMessage = $this->createElement("text", "footermessage")
	        ->setOrder( $this->order++ )
	        ->setLabel( "ACL_FOOTER_MESSAGE" )
	        ->addFilter('StripTags')
	        ->addValidator( new Zend_Validate_LessThan(250) )
        ;
        $this->addElement( $txtFooterMessage );*/
        $this->getElement('id_alias')->setValue('login');
    }

}

