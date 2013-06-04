<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package system
 * @subpackage models
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class System_Model_Widgetdetail extends OS_Entity 
{

    /**
     * 
     * @var System_Model_Widget
     */
    protected $_widget;
    /**
     * 
     * @var menu_Model_Item
     */
    protected $_menuItem;
	/**
     * @return System_Model_Widget $_widget
     */
    public function getWidget ()
    {
        return $this->_widget;
    }

	/**
     * @return menu_Model_Item $_menuItem
     */
    public function getMenuItem ()
    {
        return $this->_menuItem;
    }

	/**
     * @param System_Model_Widget $_widget
     */
    public function setWidget ($_widget)
    {
        $this->_widget = $_widget;
        return $this;
    }

	/**
     * @param menu_Model_Item $_menuItem
     */
    public function setMenuItem ($_menuItem)
    {
        $this->_menuItem = $_menuItem;
        return $this;
    }

    
    
    
    
}