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
        	->setLabel( "LBL_TITLE" )
        	->addFilter('StripTags')
        	->addValidator( new Zend_Validate_LessThan(100) )
        	->setRequired(true)
        ;
        $this->addElement( $txtTitle );
        
        $rbShowTitle = $this->createElement("radio", "showtitle");
        $rbShowTitle->setRequired(TRUE)
        	->setOrder( $this->order++ )
        	->setLabel( "LBL_SHOWTITLE" )
        	->setValue(0)
        	->setMultiOptions( array( "LBL_NO", "LBL_YES") );
        ;
        $this->addElement($rbShowTitle);
        
        $cbPosition = $this->createElement("select", "position")
	        ->setOrder($this->order++)
	        ->setLabel("LBL_POSITION")
	        ->setRequired(true)
        ;
        $this->addElement( $cbPosition );
        
        $rbPublished = $this->createElement("radio", "published")
        	->setOrder($this->order++)
	        ->setLabel("LBL_PUBLISHED")
	        ->setValue(1)
	        ->setRequired(true)
	        ->setMultiOptions( array( "LBL_NO", "LBL_YES") );
        $this->addElement($rbPublished);
        
        $rbRenderFor = $this->createElement("radio", "renderfor") # menu item (all | selected only)
	        ->setOrder($this->order++)
	        ->setLabel("MENU_RENDER_FOR")
	        ->setValue(0)
	        ->setRequired(true)
	        ->setMultiOptions( array( $this->translator->translate("LBL_ALL"), $this->translator->translate("LBL_SELECTED_ONLY")) );
        $this->addElement($rbRenderFor);
        
        $cbMenuItem = $this->createElement("multiselect", "menuitem")
	        ->setOrder($this->order++)
	        ->setLabel("MENU_ITEM")
	        #->setRequired(true)
        ;
        $this->addElement( $cbMenuItem );
        
        $hMod = $this->createElement("hidden", "mod")
        	->setOrder(99998);
        ;
        $this->addElement( $hMod );
        
        $token = new Zend_Form_Element_Hash('token');
        $token->setSalt( md5( uniqid( rand(), TRUE ) ) );
        $token->setTimeout( 60 );
        $token->setDecorators( array('ViewHelper') );
        $this->addElement($token);
        
        $submitOptions = array(
        		'buttonType' => Twitter_Bootstrap_Form_Element_Button::BUTTON_LINK,
        		'type' => 'submit',
        		'buttonType'    => 'default',
        );
        $btnSubmit = new Twitter_Bootstrap_Form_Element_Button('submit', $submitOptions);
        $btnSubmit->setLabel('LBL_SUBMIT');
        $btnSubmit->removeDecorator('Label');
        $btnSubmit->setOrder(99999);
        $btnSubmit->setDecorators(array(
        		array('FieldSize'),
        		array('ViewHelper'),
        		array('Addon'),
        		array('ElementErrors'),
        		array('Description', array('tag' => 'p', 'class' => 'help-block')),
        		array('HtmlTag', array('tag' => 'div', 'class' => 'controls')),
        		array('Wrapper')
        ));
        $btnSubmit->removeDecorator('Label');
        $this->addElement($btnSubmit);
    }


}

