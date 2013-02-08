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
class Acl_Model_Role extends Zend_Db_Table_Abstract  
{
	/**
	 * 
	 * @var string
	 */
	protected $_name = 'acl_role';
	/**
	 * 
	 * @var array
	 */
	protected $_dependentTables = array ( 'Acl_Model_Role', 'Acl_Model_Permission', 'Acl_Model_Account' );
	/**
	 * 
	 * @var array
	 */
	protected $_referenceMap = array ( 
		'Parent' => array(
			'columns'			=> array ( 'parent_id' ),
			'refTableClass'	=> 'Acl_Model_Role',
			'refColumns'		=> array ( 'id' ),
			'onDelete'		=> self::CASCADE,
			'onUpdate'		=> self::RESTRICT
		),
		
	);
	
	/**
	 * renames the table by adding the prefix defined in the global configuration parameters
	 */
	function __construct() {
		$this->_name = Zend_Registry::get('tablePrefix').$this->_name;
		parent::__construct();
	}
	
	/**
	 * Returns a recordet order by parent, role and priority
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getList() {
		$select = $this->select()
						->setIntegrityCheck(false)
						->from( array('ro' => $this->_name), array('id', 'name', 'priority') )
						->joinInner( array('rop' => $this->_name), 'ro.parent_id = rop.id', array('name AS parent_name') )
						->order('rop.id ASC')
						->order('ro.priority ASC')
						#->order('rop.name DESC')
						;
		return $this->fetchAll($select);
	}

	/**
	 * Returns a recordset of roles
	 * @param int $menuId
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getRoles () 
	{
		$select = $this->select();
		$items = $this->fetchAll($select);
		if ( $items->count() > 0 )
			return $items;
		else return null;
	}

}