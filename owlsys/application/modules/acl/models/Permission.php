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
class Acl_Model_Permission extends Zend_Db_Table_Abstract 
{
	/**
	 * 
	 * @var string
	 */
	protected $_name = 'acl_permission';
	/**
	 * 
	 * @var array
	 */
	protected $_referenceMap = array ( 
		'Resource' => array(
			'columns'		=> array ( 'resource_id' ),
			'refTableClass'	=> 'Acl_Model_Resource',
			'refColumns'	=> array ( 'id' ),
			#'onDelete'		=> self::CASCADE,
			#'onUpdate'		=> self::RESTRICT
		),
		'Role' => array(
			'columns'		=> array ( 'role_id' ),
			'refTableClass'	=> 'Acl_Model_Role',
			'refColumns'	=> array ( 'id' ),
			#'onDelete'		=> self::CASCADE,
			#'onUpdate'		=> self::RESTRICT
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
	 * Delete permissions by role
	 * @param Zend_Db_Table_Row_Abstract $role
	 * @return number
	 */
	public function deleteByRole ( Zend_Db_Table_Row_Abstract $role ) 
	{
		$select = $this->select();
		$select->where("role_id = ? ", $role->id, Zend_Db::INT_TYPE);
		$resources = $this->fetchAll( $select );
		if ( count($resources) > 0 ) {
			foreach ($resources as $resource) {
				$rsTemp = $this->find( $resource->id )->current();
				if ( $rsTemp ) {
					return $rsTemp->delete();
				}
			}
		}
	}

	/**
	 * Returns permissions assigned to a specific role
	 * @param Zend_Db_Table_Row_Abstract $resource
	 * @param Zend_Db_Table_Row_Abstract $role
	 * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
	 */
	function getByResource( Zend_Db_Table_Row_Abstract $resource, Zend_Db_Table_Row_Abstract $role )
	{
		#var_dump($role);
		$select = $this->select()
					->setIntegrityCheck(false)
					->from( array('perm'=>$this->_name), array('privilege', 'role_id'))
					->joinInner( array('rop' => Zend_Registry::get('tablePrefix').'acl_role'), 'perm.role_id = rop.id', 'rop.name')
					->where("role_id = ?", $role->id, Zend_Db::INT_TYPE)
					->where("resource_id = ?", $resource->id, Zend_Db::INT_TYPE)
					->limit(1);
		#echo $select->__toString().'<br>'
		$mdlRole = new Acl_Model_Role();
		$select2 = $mdlRole->select()->order('priority DESC')->limit(1);
		
		if ( is_null( $this->fetchRow($select) ) ){
			$childRole = $role->findDependentRowset('Acl_Model_Role', null, $select2)->current();
			if ( !is_null($childRole) ) {
				return $this->getByResource($resource, $childRole);
			}
		}/*else {
			return $this->fetchRow($select);
		}*/
		return $this->fetchRow($select);
	}

	
}

