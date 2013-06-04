<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package system
 * @subpackage forms
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class System_Form_Widget extends Twitter_Bootstrap_Form_Horizontal
{
    /**
     * (non-PHPdoc)
     * @var int
     */
    public $order = 1;
    /**
     *
     * @var Zend_Translate_Adapter
     */
    protected $translator;

    /**
     * Init default fields for widget form
     * @see Zend_Form::init()
     */
    public function init()
    {
        
        $this->_addClassNames('well');
    	$this->setMethod(Zend_Form::METHOD_POST);
    	
    	$this->translator = Zend_Registry::get('Zend_Translate');
    	
        $this->setTranslator();
        
        $hId = $this->createElement("hidden", "id")
        	->setOrder($this->order++);
        ;
        $this->addElement( $hId );
        
        $wId = $this->createElement("hidden", "wid")
        	->setOrder($this->order++);
        ;
        $this->addElement( $wId );

        $txtTitle = $this->createElement("text", "title")
        	->setOrder($this->order++)
        	->setLabel( "Title" )
        	->addFilter('StripTags')
        	->addValidator( new Zend_Validate_LessThan(100) )
        	->setRequired(true)
        ;
        $this->addElement( $txtTitle );
        
        $rbShowTitle = $this->createElement("radio", "showtitle");
        $rbShowTitle->setRequired(TRUE)
        	->setOrder( $this->order++ )
        	->setLabel( "Show title" )
        	->setValue(0)
        	->setMultiOptions( array( "No", "Yes") );
        ;
        $this->addElement($rbShowTitle);
        
        $cbPosition = $this->createElement("select", "position")
	        ->setOrder($this->order++)
	        ->setLabel("Position")
	        ->setRequired(true)
        ;
        $this->addElement( $cbPosition );
        
        $rbPublished = $this->createElement("radio", "published")
        	->setOrder($this->order++)
	        ->setLabel("Published")
	        ->setValue(1)
	        ->setRequired(true)
	        ->setMultiOptions( array( "No", "Yes") );
        $this->addElement($rbPublished);
        
        $rbRenderFor = $this->createElement("radio", "renderfor") # menu item (all | selected only)
	        ->setOrder($this->order++)
	        ->setLabel("Render in")
	        ->setValue(0)
	        ->setRequired(true)
	        ->setMultiOptions( array( $this->translator->translate("All"), $this->translator->translate("Selected only")) );
        $this->addElement($rbRenderFor);
        
        $cbMenuItem = $this->createElement("multiselect", "menuitem")
	        ->setOrder($this->order++)
	        ->setLabel("Menu item")
	        #->setRequired(true)
        ;
        $this->addElement( $cbMenuItem );
        
        $hMod = $this->createElement("hidden", "mod")
        	->setOrder(99998);
        ;
        $this->addElement( $hMod );
        
        $token = new Zend_Form_Element_Hash('token');
        $token->setSalt( md5( uniqid( rand(), TRUE ) ) );
        $token->setTimeout( 300 );
        $token->setRequired(true);
        $token->setDecorators( array('ViewHelper') );
        $this->addElement($token);
        
        $btnSubmit = $this->createElement('submit', 'submit');
        $btnSubmit->setLabel('Submit');
        $btnSubmit->removeDecorator('Label');
        $btnSubmit->setAttrib('class', 'btn btn-info')
            ->setOrder(99999);
        $this->addElement($btnSubmit);
    }


}

