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
class Menu_Form_Widgets extends System_Form_Widget
{
    
    /**
     * (non-PHPdoc)
     * @see System_Form_Widget::init()
     */
    public function init()
    {
        parent::init();
        switch ($this->getAttrib('widgetType')) {
        	case 'menu':
        		$this->menu();
        		break;
      		case 'breadcrumb':
        		$this->breadcrumb();
        		break;
        	case 'menubootstrap':
        		$this->menubootstrap();
        		break;
        	default:
        		$this->defaultWidget();
        		break;
        }
    }
    
    /**
     * (non-PHPdoc)
     */
    private function defaultWidget()
    {
    }
    
    /**
     * Add custom fields to menu item form for render a menu option menu item
     */
    private function menu()
    {
        $cbMenu = $this->createElement("select", "menuId")
	        ->setOrder( $this->order++ )
	        ->setLabel("MENU")
	        ->setRequired(true)
        ;
        
        $mdlMenu = new menu_Model_Menu();
        $menuList = $mdlMenu->getMenus();
        foreach ( $menuList as $menu )
        {
        	$cbMenu->addMultiOption( $menu->id, $menu->name );
        }
        $this->addElement( $cbMenu );
        
        $cbDistribution = $this->createElement("select", "distribution")
	        ->setOrder( $this->order++ )
	        ->setLabel("LBL_DISTRIBUTION")
	        ->setRequired(true)
	        ->addMultioption( 'horizontal', "LBL_HORIZONTAL" )
	        ->addMultioption( 'vertical', "LBL_VERTICAL" )
        ;
        $this->addElement( $cbDistribution );
        
        $txtCSS = $this->createElement("text", "css")
	        ->setOrder( $this->order++ )
	        ->setLabel( "LBL_CSS_CLASS_ALTERNATIVE" )
	        ->addFilter('StripTags')
	        ->addValidator( new Zend_Validate_LessThan(30) )
	        ->setRequired(FALSE)
        ;
        $this->addElement( $txtCSS );
        
        $rbDropdownMultilevel = $this->createElement("radio", "dropdownmultilevel")
	        ->setOrder($this->order++)
	        ->setLabel("MENU_ENABLE_DROPDOWN_MULTILEVEL")
			->setValue(0)
			->setRequired(true)
			->setMultiOptions( array( "LBL_NO", "LBL_YES") )
        ;
		$this->addElement($rbDropdownMultilevel);
        
    }
    
    /**
     * Add custom fields to menu item form for render a breadcrumb menu item
     */
    private function breadcrumb()
    {
        $txtDepth = $this->createElement("text", "depth")
	        ->setOrder( $this->order++ )
	        ->setLabel( "MENU_DEPTH" )
	        ->addFilter('StripTags')
	        ->setValue(0)
	        ->addValidator( new Zend_Validate_LessThan(30) )
	        ->setRequired(TRUE)
        ;
        $this->addElement( $txtDepth );
        
        $rbLastLink = $this->createElement("radio", "lastlink")
	        ->setOrder( $this->order++ )
	        ->setLabel("MENU_SET_LAST_LINK")
	        ->setValue(1)
	        ->setRequired(true)
	        ->setMultiOptions( array( "LBL_NO", "LBL_YES") );
        $this->addElement($rbLastLink);
        
        $txtSeparator = $this->createElement("text", "separator")
	        ->setOrder( $this->order++ )
	        ->setValue( '/' )
	        ->setLabel( "MENU_SEPARATOR" )
	        ->setRequired(TRUE)
        ;
        $this->addElement( $txtSeparator );
    }

    private function menubootstrap()
    {
    	$cbMenu = $this->createElement("select", "menuId")
	    	->setOrder( $this->order++ )
	    	->setLabel("MENU")
	    	->setRequired(true)
    	;
    
    	$mdlMenu = new menu_Model_Menu();
    	$menuList = $mdlMenu->getMenus();
    	foreach ( $menuList as $menu )
    	{
    		$cbMenu->addMultiOption( $menu->id, $menu->name );
    	}
    	$this->addElement( $cbMenu );
    
    	$cbDistribution = $this->createElement("select", "distribution")
	    	->setOrder( $this->order++ )
	    	->setLabel("LBL_DISTRIBUTION")
	    	->setRequired(true)
	    	->addMultioption( 'horizontal', "LBL_HORIZONTAL" )
	    	->addMultioption( 'vertical', "LBL_VERTICAL" )
    	;
    	$this->addElement( $cbDistribution );
    
    }
}

