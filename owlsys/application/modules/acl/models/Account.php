<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package acl
 * @subpackage models
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Acl_Model_Account extends OS_Entity {
	
    protected $_email;
    protected $_password;
    protected $_registerDate;
    protected $_lastVisitDate;
    protected $_isBlocked;
    protected $_role;
    protected $_fullname;
    protected $_emailAlternative;
    protected $_recoverpwdtoken;

	/**
     * @return the $_email
     */
    public function getEmail ()
    {
        return $this->_email;
    }

	/**
     * @return the $_password
     */
    public function getPassword ()
    {
        return $this->_password;
    }

	/**
     * @return the $_registerDate
     */
    public function getRegisterDate ()
    {
        return $this->_registerDate;
    }

	/**
     * @return the $_lastVisitDate
     */
    public function getLastVisitDate ()
    {
        return $this->_lastVisitDate;
    }

	/**
     * @return the $_isBlocked
     */
    public function getIsBlocked ()
    {
        return $this->_isBlocked;
    }

	/**
     * @return Acl_Model_Role $_role
     */
    public function getRole ()
    {
        return $this->_role;
    }

	/**
     * @return the $_fullname
     */
    public function getFullname ()
    {
        return $this->_fullname;
    }

	/**
     * @return the $_emailAlternative
     */
    public function getEmailAlternative ()
    {
        return $this->_emailAlternative;
    }

	/**
     * @return the $_recoverpwdtoken
     */
    public function getRecoverpwdtoken ()
    {
        return $this->_recoverpwdtoken;
    }


	/**
     * @param field_type $email
     */
    public function setEmail ($email)
    {
        $this->_email = $email;
        return $this;
    }

	/**
     * @param field_type $password
     */
    public function setPassword ($password)
    {
        $this->_password = $password;
        return $this;
    }

	/**
     * @param field_type $registerDate
     */
    public function setRegisterDate ($registerDate)
    {
        $this->_registerDate = $registerDate;
        return $this;
    }

	/**
     * @param field_type $lastVisitDate
     */
    public function setLastVisitDate ($lastVisitDate)
    {
        $this->_lastVisitDate = $lastVisitDate;
        return $this;
    }

	/**
     * @param field_type $isBlocked
     */
    public function setIsBlocked ($isBlocked)
    {
        $this->_isBlocked = $isBlocked;
        return $this;
    }

	/**
     * @param Acl_Model_Role $role
     */
    public function setRole ($role)
    {
        $this->_role = $role;
        return $this;
    }

	/**
     * @param field_type $fullname
     */
    public function setFullname ($fullname)
    {
        $this->_fullname = $fullname;
        return $this;
    }

	/**
     * @param field_type $emailAlternative
     */
    public function setEmailAlternative ($emailAlternative)
    {
        $this->_emailAlternative = $emailAlternative;
        return $this;
    }

	/**
     * @param field_type $recoverpwdtoken
     */
    public function setRecoverpwdtoken ($recoverpwdtoken)
    {
        $this->_recoverpwdtoken = $recoverpwdtoken;
        return $this;
    }

}

