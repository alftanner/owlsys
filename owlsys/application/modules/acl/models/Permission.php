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
class Acl_Model_Permission extends OS_Entity 
{

    /**
     * 
     * @var Acl_Model_Role
     */
    protected $_role;
    /**
     * 
     * @var Acl_Model_Resource
     */
    protected $_resource;
    protected $_isAllowed;
	/**
     * @return Acl_Model_Role $_role
     */
    public function getRole ()
    {
        return $this->_role;
    }

	/**
     * @return Acl_Model_Resource $_resource
     */
    public function getResource ()
    {
        return $this->_resource;
    }

	/**
     * @return the $_isAllowed
     */
    public function getIsAllowed ()
    {
        return $this->_isAllowed;
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
     * @param Acl_Model_Resource $resource
     */
    public function setResource ($resource)
    {
        $this->_resource = $resource;
        return $this;
    }

	/**
     * @param field_type $isAllowed
     */
    public function setIsAllowed ($isAllowed)
    {
        $this->_isAllowed = $isAllowed;
        return $this;
    }
    
    
}

