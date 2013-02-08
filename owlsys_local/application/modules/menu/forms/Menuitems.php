<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package menu
 * @subpackage forms
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Menu_Form_Menuitems extends menu_Form_Item
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
        	case 'external-link':
        	    $this->externalLink();
        	    break;
        	case 'sitemap':
        	    $this->sitemap();
        	    break;
        	case 'separator':
        	    $this->separator();
        	    break;
        	default:
        		$this->defaultParent();
        		break;
        }
        
    }
    
    /**
     * (non-PHPdoc)
     */
    private function defaultParent()
    {
    }
    
    /**
     * Add custom fields to menu item form for render an external link menu item
     */
    private function externalLink()
    {
        $this->getElement('external')->setValue(1);
        $txtLink = $this->createElement("text", "linkt")
	        ->setOrder( $this->order++ )
	        ->setLabel( "LBL_LINK" )
	        ->addFilter('StripTags')
	        ->addValidator( new Zend_Validate_LessThan(1024) )
	        ->setRequired(true)
        ;
        $this->addElement( $txtLink );
    }

    /**
     * Add custom fields to menu item form for render a sitemap menu item 
     */
    private function sitemap()
    {
        
    }
    
    /**
     * Add custom fields to menu item form for render a separator menu item
     */
    private function separator()
    {
        $this->getElement('external')->setValue(1);
    }

}

