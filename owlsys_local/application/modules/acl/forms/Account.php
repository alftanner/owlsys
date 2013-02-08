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
class Acl_Form_Account extends Twitter_Bootstrap_Form_Horizontal
{

    /**
     * (non-PHPdoc)
     *
     * @see Zend_Form::init()
     */
    public function init ()
    {
        $this->setTranslator();
        // this->_addClassNames('well');
        $this->setMethod(Zend_Form::METHOD_POST);
        
        $txtFullname = $this->createElement('text', 'fullname')
            ->setLabel("LBL_FULLNAME")
            ->setRequired(true)
            ->setAttrib('size', 40)
            ->addFilters(
                array(new Zend_Filter_StringToLower(),
                    new Zend_Filter_StringTrim(),new Zend_Filter_Alpha(true)))
            ->addValidators(
                array(new Zend_Validate_Alpha(true),
                    new Zend_Validate_StringLength(
                            array('min' => 5,'max' => 200))));
        $this->addElement($txtFullname);
        
        $txtEmail = $this->createElement('text', 'email')
            ->setLabel("ACL_EMAIL")
            ->setRequired(TRUE)
            ->setAttrib('size', 40)
            ->addFilters(
                array(new Zend_Filter_StringToLower(),
                    new Zend_Filter_StringTrim()))
            ->addValidator(new Zend_Validate_EmailAddress());
        $txtEmail->addValidator(
                new Zend_Validate_Db_NoRecordExists(
                        array('table' => 'os_acl_account','field' => 'email')));
        $this->addElement($txtEmail);
        
        $txtEmailAlternative = $this->createElement('text', 'email_alternative')
            ->setLabel("ACL_EMAIL_ALTERNATIVE")
            ->setRequired(TRUE)
            ->setAttrib('size', 40)
            ->addFilters(
                array(new Zend_Filter_StringToLower(),
                    new Zend_Filter_StringTrim()))
            ->addValidator(new Zend_Validate_EmailAddress());
        $txtEmailAlternative->addValidator(
                new Zend_Validate_Db_NoRecordExists(
                        array('table' => 'os_acl_account',
                            'field' => 'email_alternative')));
        $this->addElement($txtEmailAlternative);
        
        $txtPassword = $this->createElement('password', 'password')
            ->setLabel('ACL_PASSWORD')
            ->setRequired(TRUE)
            ->setAttrib('size', 40)
            ->addValidator(new Zend_Validate_StringLength(array('min' => '6')));
        $this->addElement($txtPassword);
        
        $txtPassword2 = $this->createElement('password', 'password2')
            ->setLabel('ACL_REPEAT_PASSWORD')
            ->setRequired(TRUE)
            ->setAttrib('size', 40)
            ->addValidator('Identical', false, array('password'))
            ->addValidator(new Zend_Validate_StringLength(array('min' => '6')));
        $this->addElement($txtPassword2);
        
        $mdlRole = new Acl_Model_Role();
        $roles = $mdlRole->getRoles();
        $cbRole = $this->createElement("select", "role_id")
            ->setLabel("ACL_ROLE")
            ->setRequired(TRUE);
        // >addMultiOption ( 0, "LABEL_SELECT_ROLE" );
        if ($roles->count() > 0) {
            foreach ($roles as $role) {
                $cbRole->addMultiOption($role->id, $role->name);
            }
        }
        $this->addElement($cbRole);
        
        $id = $this->createElement('hidden', 'id');
        $id->setDecorators(array('ViewHelper'));
        $this->addElement($id);
        
        $token = new Zend_Form_Element_Hash('token');
        $token->setSalt(md5(uniqid(rand(), TRUE)));
        $token->setTimeout(60);
        $token->setDecorators(array('ViewHelper'));
        $this->addElement($token);
        
        $submitOptions = array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Button::BUTTON_LINK,
            'type' => 'submit','buttonType' => 'default');
        $btnSubmit = new Twitter_Bootstrap_Form_Element_Button('submit', 
                $submitOptions);
        $btnSubmit->setLabel('LBL_SAVE');
        $btnSubmit->removeDecorator('Label');
        $btnSubmit->setDecorators(
                array(array('FieldSize'),array('ViewHelper'),array('Addon'),
                    array('ElementErrors'),
                    array('Description',
                        array('tag' => 'p','class' => 'help-block')),
                    array('HtmlTag',array('tag' => 'div','class' => 'controls')),
                    array('Wrapper')));
        $btnSubmit->removeDecorator('Label');
        $this->addElement($btnSubmit);
    }
}

