<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package contact
 * @subpackage forms
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Contact_Form_Menuitems extends menu_Form_Item
{

    /**
     * (non-PHPdoc)
     * @see menu_Form_Item::init()
     */
    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        parent::init();
        switch ($this->getAttrib('menuType')) {
        	case 'contact-layout':
        		$this->simpleContactLayout();
        		break;
        	case 'category-layout':
        		$this->simpleCategoryLayout();
        		break;
        	case 'contacts-list-registered':
        	case 'contact-add':
        	case 'category-list-registered':
        	case 'category-add':
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
     * Add custom fields to menu item form for render a simple contact layout menu item
     */
    private function simpleContactLayout()
    {
        # dropdown for contacts
    	$txtContact = $this->createElement("select", "contact")
	    	->setOrder( $this->order++ )
	    	->setLabel( "CONTACT_SELECT" )
	    	->setRequired(true)
    	;
    	$mdlContact = new Contact_Model_Contact();
    	$contactList = $mdlContact->getPublishedList();
    	foreach ( $contactList as $contact ) {
    	    $title = $contact->first_name.' '.$contact->last_name;
    	    $title .= ( strlen($contact->con_position) > 0 ) ? ' ['.$contact->con_position.']' : '';
    	    $txtContact->addMultiOption( $contact->id , $title );
    	}
    	$this->addElement( $txtContact );
    	
    	# we can add more conditions like set require for some fields or even show them
    }
    
    /**
     * Add custom fields to menu item form for render a simple category layout menu item
     */
    private function simpleCategoryLayout()
    {
    	$txtCategory = $this->createElement("select", "category")
	    	->setOrder( $this->order++ )
	    	->setLabel( "CONTACT_CATEGORY_SELECT" )
	    	->setRequired(true)
    	;
    	$mdlCategory = new Contact_Model_Category();
    	$categoryList = $mdlCategory->getSimpleList();
    	foreach ( $categoryList as $category ) {
    	    $txtCategory->addMultiOption( $category->id , $category->title );
    	}
    	$this->addElement( $txtCategory );
    }

}

