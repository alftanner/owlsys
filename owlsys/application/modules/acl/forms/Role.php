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

        $mdlLayout = System_Model_LayoutMapper::getInstance();
        $layouts = $mdlLayout->getAll();
        $cbLayout = $this->createElement("select", "layout");
        $cbLayout->setLabel('Layout');
        $cbLayout->setRequired(true);
        foreach ( $layouts as $layout ) {
        	$cbLayout->addMultiOption( $layout->getId(), $layout->getName() );
        }
        $this->addElement( $cbLayout );
        
        $id = $this->createElement('hidden', 'id');
        $id->setDecorators( array('ViewHelper') );
        $this->addElement($id);
        
        $token = new Zend_Form_Element_Hash('token');
        $token->setSalt( md5( uniqid( rand(), TRUE ) ) );
        $token->setTimeout( 300 );
        $token->setDecorators( array('ViewHelper') );
        $this->addElement($token);
        
        $btnSubmit = $this->createElement('submit', 'submit');
        $btnSubmit->setLabel('LBL_SUBMIT');
        $btnSubmit->removeDecorator('Label');
        $btnSubmit->setAttrib('class', 'btn btn-info');
        $this->addElement($btnSubmit);
        
    }


}

