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
class Contact_Form_Contact extends Twitter_Bootstrap_Form_Horizontal
{
    
    /**
     * (non-PHPdoc)
     * @see Zend_Form::init()
     */
    public function init()
    {
        switch ($this->getAttrib('type')) {
        	case 'public':
        		$this->publicDisplay();
        		break;
        	default:
        		$this->backendManager();
        		break;
        }
    }

    /**
     * Render a back-end contact form
     */
    public function backendManager()
    {
        $this->_addClassNames('well');
    	$this->setMethod(Zend_Form::METHOD_POST);
        $this->setTranslator();
         
        $this->setMethod('post');
        $id = $this->createElement('hidden', 'id')
        	->setDecorators(array('ViewHelper'));
        $this->addElement($id);
        
        $cbAccount = $this->createElement('select', 'account_id')
	        ->setLabel( "ACL_ACCOUNT" )
	        ->setRequired(true);
        $this->addElement($cbAccount);
        
        $cbCategory = $this->createElement('select', 'category_id')
	        ->setLabel( "LBL_CATEGORY" )
	        ->setRequired(true);
        $this->addElement($cbCategory);
        
        $txtMobile = $this->createElement('text', 'mobile')
	        ->setLabel( 'LBL_MOBILE' )
	        ->setRequired(TRUE)
	        ->addFilter( new Zend_Filter_Digits() )
	        ->addValidator( new Zend_Validate_Digits() )
	        ->setAttrib('maxlength', 255);
        $this->addElement($txtMobile);
        
        $txtWebpage = $this->createElement('text', 'webpage')
	        ->setLabel( 'LBL_WEBPAGE' )
	        ->setRequired(FALSE)
	        ->addValidator( new OS_Application_Validators_Url() )
	        ->setAttrib('maxlength', 200);
        $this->addElement($txtWebpage);
        
        $txtEmailTo = $this->createElement('text', 'email_to')
	        ->setLabel( 'CONTACT_EMAIL_TO' )
	        ->setRequired(false)
	        ->addValidator( new Zend_Validate_EmailAddress() )
	        ->setAttrib('maxlength', 200);
        $this->addElement($txtEmailTo);
        
        $txtMisc = $this->createElement('textarea', 'misc')
	        ->setAttrib('cols', 40)
	        ->setAttrib('rows', 3)
	        ->setLabel( 'LBL_MISC' )
	        ->setRequired(FALSE)
	        ->addValidator( new Zend_Validate_LessThan(1024) );
        $this->addElement($txtMisc);
        
        $txtFax = $this->createElement('text', 'fax')
	        ->setLabel( 'LBL_FAX' )
	        ->setRequired(false)
	        ->addFilter( new Zend_Filter_Alnum() )
	        ->addValidator( new Zend_Validate_Alnum() )
	        ->setAttrib('maxlength', 255);
        $this->addElement($txtFax);
 
        $txtTelephone = $this->createElement('text', 'telephone')
	        ->setLabel( 'LBL_TELEPHONE' )
	        ->setRequired(false)
	        ->addFilter( new Zend_Filter_Digits() )
	        ->addValidator( new Zend_Validate_Digits() )
	        ->setAttrib('maxlength', 255);
        $this->addElement($txtTelephone);
        
        $txtPostcode = $this->createElement('text', 'postcode')
	        ->setLabel( 'LBL_POSTCODE' )
	        ->setRequired(false)
	        ->addFilter( new Zend_Filter_Alnum() )
	        ->addValidator( new Zend_Validate_Alnum() )
	        ->setAttrib('maxlength', 100);
        $this->addElement($txtPostcode);
        
        $txtCountry = $this->createElement('text', 'country')
	        ->setLabel( 'LBL_COUNTRY' )
	        ->setRequired(false)
	        ->addFilter( new Zend_Filter_StripTags() )
	        ->addFilter( new Zend_Filter_Alpha(true) )
	        ->addValidator( new Zend_Validate_Alpha(true) )
	        ->setAttrib( 'maxlength', 100 );
        $this->addElement($txtCountry);
        
        $txtCity = $this->createElement('text', 'city')
	        ->setLabel( 'LBL_CITY' )
	        ->setRequired(false)
	        ->addFilter( new Zend_Filter_StripTags() )
	        ->addFilter( new Zend_Filter_Alpha(true) )
	        ->addValidator( new Zend_Validate_Alpha(true) )
	        ->setAttrib( 'maxlength', 100 );
        $this->addElement($txtCity);
        
        $txtConPosition = $this->createElement('text', 'con_position')
	        ->setLabel( 'CONTACT_CON_POSITION' )
	        ->setRequired(false)
	        ->addFilter( new Zend_Filter_StripTags() )
	        ->addFilter( new Zend_Filter_Alpha(true) )
	        ->addValidator( new Zend_Validate_Alpha(true) )
	        ->setAttrib( 'maxlength', 255 );
        $this->addElement($txtConPosition);

		$txtAddress = $this->createElement('textarea', 'address')
	        ->setAttrib('cols', 40)
	        ->setAttrib('rows', 3)
	        ->setLabel( 'LBL_ADDRESS' )
	        ->setRequired(FALSE)
	        ->addFilter( new Zend_Filter_Alnum(true) )
	        ->addValidator( new Zend_Validate_Alnum(true) )
	        ->addValidator( new Zend_Validate_LessThan(1024) );
        $this->addElement($txtAddress);
        
        $image = $this->createElement('file', 'image');
        #$image = new Zend_Form_Element_File('image');
        #$image = new Twitter_Bootstrap_Form_Element_File('image');
        $image->setLabel( 'LBL_IMAGE' )
	        ->setRequired(false)
	        ->addValidator( 'Count', false, 1) /* ensure only 1 file */
	        ->addValidator( 'Size', false, 102400 ) /* limit to 100K */
	        ->addValidator( 'Extension', false, 'jpg, jpeg, png, gif' ) /* only JPEG, PNG, and GIFs */
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

    /**
     * Render a front-end contact form
     */
    public function publicDisplay()
    {
    	$this->_addClassNames('well');
    	$this->setMethod(Zend_Form::METHOD_POST);
        $this->setTranslator();
        
        $txtFullName = $this->createElement('text', 'fullname')
	        ->setLabel( 'LBL_FULLNAME' )
	        ->setRequired(true)
	        ->addFilter( new Zend_Filter_Alpha(true) )
	        ->addValidator( new Zend_Validate_Alpha(true) )
	        ->addFilter( new Zend_Filter_StripTags() )
	        ->setAttrib('maxlength', 255);
        $this->addElement($txtFullName);
        
        $txtTelephone = $this->createElement('text', 'telephone')
	        ->setLabel( 'LBL_TELEPHONE' )
	        ->setRequired(true)
	        ->addFilter( new Zend_Filter_Digits() )
	        ->addValidator( new Zend_Validate_Digits() )
	        ->setAttrib('maxlength', 255);
        $this->addElement($txtTelephone);
        
        $txtEmail = $this->createElement('text', 'email')
	        ->setLabel( 'LBL_EMAIL' )
	        ->setRequired(true)
	        ->addValidator( new Zend_Validate_EmailAddress() )
	        ->setAttrib('maxlength', 200);
        $this->addElement($txtEmail);
        
        $txtWebpage = $this->createElement('text', 'webpage')
	        ->setLabel( 'LBL_WEBPAGE' )
	        ->setRequired(FALSE)
	        ->addValidator( new OS_Application_Validators_Url() )
	        ->setAttrib('maxlength', 200);
        $this->addElement($txtWebpage);
        
        $txtCountry = $this->createElement('text', 'country')
	        ->setLabel( 'LBL_COUNTRY' )
	        ->setRequired(false)
	        ->addFilter( new Zend_Filter_StripTags() )
	        ->addFilter( new Zend_Filter_Alpha(true) )
	        ->addValidator( new Zend_Validate_Alpha(true) )
	        ->setAttrib( 'maxlength', 100 );
        $this->addElement($txtCountry);
        
        $txtCity = $this->createElement('text', 'city')
	        ->setLabel( 'LBL_CITY' )
	        ->setRequired(false)
	        ->addFilter( new Zend_Filter_StripTags() )
	        ->addFilter( new Zend_Filter_Alpha(true) )
	        ->addValidator( new Zend_Validate_Alpha(true) )
	        ->setAttrib( 'maxlength', 100 );
        $this->addElement($txtCity);
        
        $txtMessage = $this->createElement('textarea', 'message')
	        ->setAttrib('cols', 40)
	        ->setAttrib('rows', 3)
	        ->setLabel( 'LBL_MESSAGE' )
	        ->setRequired(TRUE)
	        ->addFilter( new Zend_Filter_Alnum(true) )
	        ->addValidator( new Zend_Validate_Alnum(true) )
	        ->addValidator( new Zend_Validate_LessThan(1024) );
        $this->addElement($txtMessage);
        
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

