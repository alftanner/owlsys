<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package menu
 * @subpackage models
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class menu_Model_Menu extends OS_Entity
{

    protected $_name;
    protected $_isPublished;
    /**
     * @var menu_Model_Item[]
     */
    protected $_menuItems;
    
	/**
     * @return the $_name
     */
    public function getName ()
    {
        return $this->_name;
    }

	/**
     * @return the $_isPublished
     */
    public function getIsPublished ()
    {
        return $this->_isPublished;
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
     * @param field_type $isPublished
     */
    public function setIsPublished ($isPublished)
    {
        $this->_isPublished = $isPublished;
        return $this;
    }

    /**
     * @return menu_Model_Item[] $_menuItems
     */
    public function getChildren ()
    {
        return $this->_menuItems;
    }
    
    /**
     * @param multitype:menu_Model_Item $children
     */
    public function setChildren ($children)
    {
        $this->_menuItems = $children;
        return $this;
    }

    /**
     * 
     * @param menu_Model_Item $menuItem
     * @return menu_Model_Menu
     */
    public function addChild(menu_Model_Item $menuItem)
    {
        if ( count($this->_menuItems) == 0 ) {
            $this->_menuItems[] = $menuItem;
            return $this;
        }
        $bolExists = false;
        foreach ( $this->_menuItems as $child )
        {
            if ( $child->getId() == $menuItem->getId() ) {
                $bolExists = true;
                break;
            }
        }
        if ( $bolExists == false ) {
            $this->_menuItems[] = $menuItem;
        }
        return $this;
    }
    
    
}


