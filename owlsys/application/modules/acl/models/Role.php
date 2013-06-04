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
class Acl_Model_Role extends OS_Entity  
{
	protected $_name;
	protected $_layout;
	
	/**
     * @return the $_name
     */
    public function getName ()
    {
        return $this->_name;
    }

	/**
     * @return System_Model_Layout $_layout
     */
    public function getLayout ()
    {
        return $this->_layout;
    }

	/**
     * @param field_type $name
     */
    public function setName ($name)
    {
        $this->_name = $name;
        return $this;
    }

	/**
     * @param System_Model_Layout $layout
     */
    public function setLayout ($layout)
    {
        $this->_layout = $layout;
        return $this;
    }

	
	

	
}