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
class System_Model_Widget extends OS_Entity
{

    protected $_position;
    protected $_title;
    protected $_isPublished;
    protected $_ordering;
    protected $_params;
    /**
     * @var Acl_Model_Resource
     */
    protected $_resource;
    /**
     * Id from xml widget file 
     * @var number
     */
    protected $_wid;
    protected $_showtitle;
	/**
     * @return the $_position
     */
    public function getPosition ()
    {
        return $this->_position;
    }

	/**
     * @return the $_title
     */
    public function getTitle ()
    {
        return $this->_title;
    }

	/**
     * @return the $_isPublished
     */
    public function getIsPublished ()
    {
        return $this->_isPublished;
    }

	/**
     * @return the $_ordering
     */
    public function getOrdering ()
    {
        return $this->_ordering;
    }

	/**
     * @return the $_params
     */
    public function getParams ()
    {
        return $this->_params;
    }

	/**
     * @return Acl_Model_Resource $_resource
     */
    public function getResource ()
    {
        return $this->_resource;
    }

	/**
     * @return the $_wid
     */
    public function getWid ()
    {
        return $this->_wid;
    }

	/**
     * @return the $_showtitle
     */
    public function getShowtitle ()
    {
        return $this->_showtitle;
    }

	/**
     * @param field_type $_position
     */
    public function setPosition ($_position)
    {
        $this->_position = $_position;
        return $this;
    }

	/**
     * @param field_type $_title
     */
    public function setTitle ($_title)
    {
        $this->_title = $_title;
        return $this;
    }

	/**
     * @param field_type $_isPublished
     */
    public function setIsPublished ($_isPublished)
    {
        $this->_isPublished = $_isPublished;
        return $this;
    }

	/**
     * @param field_type $_ordering
     */
    public function setOrdering ($_ordering)
    {
        $this->_ordering = $_ordering;
        return $this;
    }

	/**
     * @param field_type $_params
     */
    public function setParams ($_params)
    {
        $this->_params = $_params;
        return $this;
    }

	/**
     * @param Acl_Model_Resource $_resource
     */
    public function setResource ($_resource)
    {
        $this->_resource = $_resource;
        return $this;
    }

	/**
     * @param number $_wid
     */
    public function setWid ($_wid)
    {
        $this->_wid = $_wid;
        return $this;
    }

	/**
     * @param field_type $_showtitle
     */
    public function setShowtitle ($_showtitle)
    {
        $this->_showtitle = $_showtitle;
        return $this;
    }

    
    

}

