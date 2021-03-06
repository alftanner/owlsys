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
class Contact_Form_Category extends Twitter_Bootstrap_Form_Horizontal
{

    /**
     * (non-PHPdoc)
     * @see Zend_Form::init()
     */
    public function init()
    {
        $this->_addClassNames('well');
    	$this->setMethod(Zend_Form::METHOD_POST);
        $this->setTranslator();
         
        $this->setMethod('post');
        $id = $this->createElement('hidden', 'id')
	        ->setDecorators(array('ViewHelper'));
        $this->addElement($id);
        
        $txtTitle = $this->createElement('text', 'title')
	        ->setLabel( 'LBL_TITLE' )
	        ->setRequired(TRUE)
	        ->addFilter( new Zend_Filter_StripTags() )
	        ->addFilter( new Zend_Filter_Alpha(true) )
	        ->setAttrib( 'maxlength', 200 );
        $this->addElement($txtTitle);
        
        $txtDescription = $this->createElement('textarea', 'description')
	        ->setAttrib('cols', 40)
	        ->setAttrib('rows', 5)
			->setLabel( 'LBL_DESCRIPTION' )
			->setRequired(FALSE)
			->addValidator( new Zend_Validate_LessThan(1024) );
		$this->addElement($txtDescription);
		
		$image = $this->createElement('file', 'image')
			->setLabel( 'LBL_IMAGE' )
			->setRequired(false)
			->addValidator( 'Count', false, 1) # ensure only 1 file
			->addValidator( 'Size', false, 102400 ) # limit to 100K
			->addValidator( 'Extension', false, 'jpg, jpeg, png, gif' ) # only JPEG, PNG, and GIFs
			->addValidator( 'NotExists', false, DIR_MOD_CONTACT_IMG_UPLOADS )
			->setDestination( DIR_MOD_CONTACT_IMG_UPLOADS )
		;
		$this->addElement($image);
        
		$rbPublished = $this->createElement("radio", "published")
			->setLabel("LBL_PUBLISHED")
			->setValue(1)
			->setMultiOptions( array( "LBL_NO", "LBL_YES") );
		$this->addElement($rbPublished);
        
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

