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
class Acl_Form_ManageResources extends Twitter_Bootstrap_Form_Horizontal
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
    	/*$mdlResource = new Acl_Model_Resource();
        $resources = $mdlResource->getRegisteredList();
        $cbResource = $this->createElement("multiselect", "resources_id");
        $cbResource->setLabel("LABEL_RESOURCES")
        				->setRequired( FALSE );
        if ( $resources->count() > 0 ) {
	        foreach ( $resources as $resource ) {
	        	$lblResource = $resource->module.' > '.$resource->controller.' > '.$resource->actioncontroller; 
	        	$cbResource->addMultiOption( $resource->id, $lblResource );
	        }
        }
        $this->addElement( $cbResource );*/
        
        $id = $this->createElement('hidden', 'id');
        $id->setDecorators( array('ViewHelper') );
        $this->addElement($id);
        
        $hRs = $this->createElement('hidden', 'hrs');
	    $this->addElement($hRs);
        
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
        
        #$this->clearDecorators();
        #$this->addDecorator('HtmlTag', array('tag'=>'ul'));
        #$this->addDecorator('form');
    }


}

