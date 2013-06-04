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
class Acl_Model_Resource extends OS_Entity  
{

	protected $_module;
	protected $_controller;
	protected $_actioncontroller;
	/**
     * @return the $_module
     */
    public function getModule ()
    {
        return $this->_module;
    }

	/**
     * @return the $_controller
     */
    public function getController ()
    {
        return $this->_controller;
    }

	/**
     * @return the $_actioncontroller
     */
    public function getActioncontroller ()
    {
        return $this->_actioncontroller;
    }

	/**
     * @param field_type $module
     */
    public function setModule ($module)
    {
        $this->_module = $module;
        return $this;
    }

	/**
     * @param field_type $controller
     */
    public function setController ($controller)
    {
        $this->_controller = $controller;
        return $this;
    }

	/**
     * @param field_type $actioncontroller
     */
    public function setActioncontroller ($actioncontroller)
    {
        $this->_actioncontroller = $actioncontroller;
        return $this;
    }

	
	

}


