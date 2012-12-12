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
class Menu_Model_Menu extends Zend_Db_Table_Abstract 
{

    /**
     * 
     * @var string
     */
	protected $_name = 'menu';
	/**
	 * 
	 * @var array
	 */
	protected $_dependentTables = array ( 'menu_Model_Item' );
	
	/**
	 * renames the table by adding the prefix defined in the global configuration parameters
	 */
	function __construct() {
		$this->_name = Zend_Registry::get('tablePrefix').$this->_name;
		parent::__construct();
	}
	
	/**
	 * Return a recordset of menus
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getMenus()
	{
		$select = $this->select();
		$select->order('id');
		return $this->fetchAll($select);
	}
	
	/**
	 * Return a recordset of menus
	 * @return Zend_Paginator_Adapter_DbTableSelect
	 */
	public function getPaginatorAdapterList()
	{
		$select = $this->select();
		$select->order('id');
		return new Zend_Paginator_Adapter_DbTableSelect($select);
	}

	/**
	 * 
	 * @param int $status
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getByStatus( $status )
	{
		$select = $this->select();
		$select->order('id');
		$select->where('published=?', $status, Zend_Db::INT_TYPE);
		return $this->fetchAll($select);
	}
}

