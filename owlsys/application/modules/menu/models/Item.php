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
class menu_Model_Item extends OS_Entity
{

    protected $_route;
    /**
     * @var menu_Model_Menu
     */
    protected $_menu;
    /**
     * @var Acl_Model_Resource
     */
    protected $_resource;
    /**
     * @var menu_Model_Item
     */
    protected $_parent;
    protected $_ordering;
    protected $_icon;
    protected $_wtype;
    protected $_params;
    protected $_isPublished;
    protected $_title;
    protected $_description;
    protected $_external;
    /**
     * id found for each menu item specified in the xml module file menus.xml
     * @var number
     */
    protected $_mid; 
    protected $_isVisible;
    protected $_cssClass;
    protected $_depth;
    /**
     * Menu item children
     * @var menu_Model_Item[]
     */
    protected $_menuItems = array();
    
	/**
     * @return the $_route
     */
    public function getRoute ()
    {
        return $this->_route;
    }

	/**
     * @return menu_Model_Menu $_menu
     */
    public function getMenu ()
    {
        return $this->_menu;
    }

	/**
     * @return Acl_Model_Resource $_resource
     */
    public function getResource ()
    {
        return $this->_resource;
    }

	/**
     * @return menu_Model_Item $_parent
     */
    public function getParent ()
    {
        return $this->_parent;
    }

	/**
     * @return the $_ordering
     */
    public function getOrdering ()
    {
        return $this->_ordering;
    }

	/**
     * @return the $_icon
     */
    public function getIcon ()
    {
        return $this->_icon;
    }

	/**
     * @return the $_wtype
     */
    public function getWtype ()
    {
        return $this->_wtype;
    }

	/**
     * @return the $_params
     */
    public function getParams ()
    {
        return $this->_params;
    }

	/**
     * @return the $_isPublished
     */
    public function getIsPublished ()
    {
        return $this->_isPublished;
    }

	/**
     * @return the $_title
     */
    public function getTitle ()
    {
        return $this->_title;
    }

	/**
     * @return the $_description
     */
    public function getDescription ()
    {
        return $this->_description;
    }

	/**
     * @return the $_external
     */
    public function getExternal ()
    {
        return $this->_external;
    }

	/**
     * @return the $_mid
     */
    public function getMid ()
    {
        return $this->_mid;
    }

	/**
     * @return the $_isVisible
     */
    public function getIsVisible ()
    {
        return $this->_isVisible;
    }

	/**
     * @return the $_cssClass
     */
    public function getCssClass ()
    {
        return $this->_cssClass;
    }

	/**
     * @return the $_depth
     */
    public function getDepth ()
    {
        return $this->_depth;
    }

	/**
     * @param field_type $route
     */
    public function setRoute ($route)
    {
        $this->_route = $route;
        return $this;
    }

	/**
     * @param menu_Model_Menu $menu
     */
    public function setMenu ($menu)
    {
        $this->_menu = $menu;
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
     * @param menu_Model_Item $parent
     */
    public function setParent ($parent)
    {
        $this->_parent = $parent;
        return $this;
    }

	/**
     * @param field_type $ordering
     */
    public function setOrdering ($ordering)
    {
        $this->_ordering = $ordering;
        return $this;
    }

	/**
     * @param field_type $icon
     */
    public function setIcon ($icon)
    {
        $this->_icon = $icon;
        return $this;
    }

	/**
     * @param field_type $wtype
     */
    public function setWtype ($wtype)
    {
        $this->_wtype = $wtype;
        return $this;
    }

	/**
     * @param field_type $params
     */
    public function setParams ($params)
    {
        $this->_params = $params;
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
     * @param field_type $title
     */
    public function setTitle ($title)
    {
        $this->_title = $title;
        return $this;
    }

	/**
     * @param field_type $description
     */
    public function setDescription ($description)
    {
        $this->_description = $description;
        return $this;
    }

	/**
     * @param field_type $external
     */
    public function setExternal ($external)
    {
        $this->_external = $external;
        return $this;
    }

	/**
     * @param field_type $mid
     */
    public function setMid ($mid)
    {
        $this->_mid = $mid;
        return $this;
    }

	/**
     * @param field_type $isVisible
     */
    public function setIsVisible ($isVisible)
    {
        $this->_isVisible = $isVisible;
        return $this;
    }

	/**
     * @param field_type $cssClass
     */
    public function setCssClass ($cssClass)
    {
        $this->_cssClass = $cssClass;
        return $this;
    }

	/**
     * @param field_type $depth
     */
    public function setDepth ($depth)
    {
        $this->_depth = $depth;
        return $this;
    }
	
	/**
     * @return the $_menuItems
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
     * @return menu_Model_Item
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

