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
class menu_Form_Item extends Twitter_Bootstrap_Form_Horizontal
{
    /**
     * 
     * @var int
     */
    public $order = 11;
    /**
     * 
     * @var array
     */
    public $defaultFormFields = array( 
    	'id', 'menu', 'title', 'description', 'route', 
    	'parent', 'wtype', 'published', 'external', 'mid', 
    	'resource', 'mod', 'token', 'isvisible', 'css_class'
    );

    /**
     * Init default fields for menu item form
     * @see Zend_Form::init()
     */
    public function init()
    {
    	$this->_addClassNames('well');
    	$this->setMethod(Zend_Form::METHOD_POST);
    	$this->setTranslator();
    	
    	$this->setMethod('post');
		$id = $this->createElement('hidden', 'id')
			->setOrder(1)
			->setRequired(true)
			->setDecorators(array('ViewHelper'));
		$this->addElement($id);
		
		$menuId = $this->createElement('hidden', 'menu')
			->setOrder(2)
			->setRequired(TRUE)
			->setDecorators(array('ViewHelper'));
		$this->addElement($menuId);
		
		$txtTitle = $this->createElement('text', 'title')
			->setOrder(3)
			->setLabel( 'Title' )
			->setRequired(TRUE)
			->addFilter('StripTags')
			->setAttrib('size',40)
			->setAttrib('maxlength',50);
		$this->addElement($txtTitle);
		
		$txtDescription = $this->createElement('text', 'description')
			->setOrder(4)
			->setLabel( 'Description' )
			->addFilter('StripTags')
			->setAttrib('size',40)
			->setAttrib('maxlength',150)
			->addValidator( new Zend_Validate_LessThan(150) );
		$this->addElement($txtDescription);
		
		$txtAlias = $this->createElement('text', 'route')
			->setOrder(5)
			->setLabel( 'Route' )
			->setRequired(TRUE)
			->addFilter('StripTags')
			->setAttrib('size',40)
			->addValidator( new Zend_Validate_LessThan(50) );
			#->addValidator( new Zend_Validate_Alnum() );
		$this->addElement($txtAlias);
		
		$cbParent = $this->createElement('select', 'parent')
			->setOrder(6)
			->setLabel( "Parent" )
			->setRequired(true);
		$this->addElement($cbParent);
		
		$cbWType = $this->createElement('select', 'wtype')
			->setOrder(7)
			->setLabel( 'MENU_ITEM_WINDOW_TYPE' )
			->setRequired(true)
			->addMultiOption('_self', '_self')
			->addMultiOption('_parent', '_parent')
			->addMultiOption('_blank', '_blank');
		$this->addElement($cbWType);
		
		$rbPublished = $this->createElement("radio", "published")
			->setOrder(8)
        	->setLabel("Published")
        	->setValue(1)
        	->setRequired(true)
        	->setMultiOptions( array( "No", "Yes") );
        $this->addElement($rbPublished);
        
        $rbVisible = $this->createElement("radio", "isvisible")
	        ->setOrder(9)
	        ->setLabel("Visible")
	        ->setValue(1)
	        ->setRequired(true)
	        ->setMultiOptions( array( "No", "Yes") );
        $this->addElement($rbVisible);
        
        $txtCssClass = $this->createElement('text', 'css_class')
            ->setOrder(10)
            ->setLabel( 'Class' )
    		->setAttrib('maxlength',50)
    		->addValidator( new Zend_Validate_LessThan(50) );
        $this->addElement($txtCssClass);
        
        $hExternal = $this->createElement("hidden", "external")
        	->setValue(0)
        	->setRequired(true)
        	->setOrder(99995)
        	->setDecorators( array('ViewHelper') )
        ;
        $this->addElement( $hExternal );
        
        $mId = $this->createElement("hidden", "mid")
        	->setOrder(99996)
        	->setRequired(true)
        	->setDecorators( array('ViewHelper') )
        ;
        $this->addElement( $mId );
        
        $hResource = $this->createElement("hidden", "resource")
        	->setOrder(99997)
        	->setRequired(true)
        	->setDecorators( array('ViewHelper') )
        ;
        $this->addElement( $hResource );
        
        $hMod = $this->createElement("hidden", "mod")
        	->setDecorators( array('ViewHelper') )
        	->setOrder(99998)
        	->setRequired(true)
        ;
        $this->addElement( $hMod );
        
        $token = new Zend_Form_Element_Hash('token');
        $token->setSalt( md5( uniqid( rand(), TRUE ) ) );
        $token->setTimeout( 300 );
        $token->setRequired(true);
        $token->setDecorators( array('ViewHelper') );
        $this->addElement($token);
        
        $btnSubmit = $this->createElement('submit', 'submit');
        $btnSubmit->setOrder(99999);
        $btnSubmit->setLabel('Submit');
        $btnSubmit->removeDecorator('Label');
        $btnSubmit->setAttrib('class', 'btn btn-info');
        $this->addElement($btnSubmit);

    }


}

