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
class Acl_Form_Role extends Twitter_Bootstrap_Form_Horizontal
{

    /**
     * (non-PHPdoc)
     * @see Zend_Form::init()
     */
    public function init()
    {
    	#$this->_addClassNames('well');
    	$this->setMethod(Zend_Form::METHOD_POST);
    	
        $this->setTranslator();
		
		$txtName = $this->createElement('text', 'name');
		$txtName->setLabel( 'ACL_ROLE' )
					->setRequired(TRUE)
					->setAttrib('size', '40')
					->addValidator( new Zend_Validate_NotEmpty() );
		$this->addElement($txtName);

		$mdlRole = new Acl_Model_Role();
        $roles = $mdlRole->getRoles();
        $cbRole = $this->createElement("select", "parent_id");
        $cbRole->setLabel("ACL_ROLE_PARENT")
        				->setRequired( FALSE );
        if ( $roles->count() > 0 ) {
	        foreach ( $roles as $role ) {
	        	$cbRole->addMultiOption( $role->id, $role->name );
	        }
        }
        $this->addElement( $cbRole );
        
        $mdlSkin = new System_Model_Skin();
        $skin = $mdlSkin->getSkinSelected();
        $skinName = is_null($skin) ? 'default' : strtolower($skin->name);
        $layouts = new Zend_Config_Xml( APPLICATION_PATH.'/layouts/scripts/'.$skinName.'/layouts.xml');
        $layouts = $layouts->files->layout->toArray();
        
        $cbDesktopLayout = $this->createElement("select", "desktop_layout");
        $cbDesktopLayout->setLabel('LBL_DESKTOP_LAYOUT');
        $cbDesktopLayout->setRequired(true);
        
        $cbMobileLayout = $this->createElement("select", "mobile_layout");
        $cbMobileLayout->setLabel('LBL_MOBILE_LAYOUT');
        $cbMobileLayout->setRequired(true);
        
        foreach ( $layouts as $layout ) {
        	$cbDesktopLayout->addMultiOption( $layout, $layout );
        	$cbMobileLayout->addMultiOption( $layout, $layout );
        }
        
        $this->addElement( $cbDesktopLayout );
        $this->addElement( $cbMobileLayout );
        
        $txtPriority = $this->createElement('text', 'priority');
		$txtPriority->setLabel( 'ACL_PRIORITY' )
					->setRequired(TRUE)
					->setAttrib('size', 10)
					->setAttrib('maxlength', 2) 
					->addValidator( new Zend_Validate_NotEmpty() );
		$this->addElement($txtPriority);
        
        $id = $this->createElement('hidden', 'id');
        $id->setDecorators( array('ViewHelper') );
        $this->addElement($id);
        
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
        $btnSubmit->setLabel('LBL_SAVE');
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

