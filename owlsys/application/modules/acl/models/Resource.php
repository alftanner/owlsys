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
class Acl_Model_Resource extends Zend_Db_Table_Abstract  
{

    /**
     * 
     * @var string
     */
	protected $_name = 'acl_resource';

	/**
	 * 
	 * @var array
	 */
	protected $_dependentTables = array ( 'Acl_Model_Permission', 'menu_Model_Item', 'System_Model_Widget' );

	/**
	 * renames the table by adding the prefix defined in the global configuration parameters
	 */
	function __construct() {
		$this->_name = Zend_Registry::get('tablePrefix').$this->_name;
		parent::__construct();
	}
	
	/**
	 * Returns a resource list available for a specific role including all childs roles
	 * @param Zend_Db_Table_Row_Abstract $role
	 * @return Zend_Db_Table_Rowset_Abstract instance of | array
	 */
	public function getResourcesByRole( Zend_Db_Table_Row_Abstract $role ) 
	{
		$select = $this->select()
					->setIntegrityCheck(false)
					->from( array('sr'=> $this->_name), array('id', 'module', 'controller', 'actioncontroller') )
					->distinct()
					->joinInner( array('arrd' => Zend_Registry::get('tablePrefix').'acl_role_resource'), 'arrd.resource_id = sr.id', '' )
					->joinInner( array('arp' => Zend_Registry::get('tablePrefix').'acl_role_parents'), sprintf('arrd.role_id = arp.role_id and ( arp.role_id = %u OR arp.parent_id = %u)', $role->id, $role->id), '' );
		$rows = $this->fetchAll( $select );
		#echo $select->__toString();
		if ( $rows->count() > 0 ) return $rows;
		return null;
	}
	
	/**
	 * Returns all resources registered order by module / controller asc
	 * @return Zend_Db_Table_Rowset_Abstract instance of | array
	 */
	public function getRegisteredList( ) 
	{
		$select = $this->select();
		$rows = $this->fetchAll( $select, array('module'=>'asc', 'controller'=>'asc') );
		if ( $rows->count() > 0 ) return $rows;
		return array();
	}
	
	/**
	 * 
	 * @param Zend_Db_Table_Row_Abstract $module
	 * @return Zend_Db_Table_Rowset_Abstract|multitype:
	 */
	public function getByModule( Zend_Db_Table_Row_Abstract $module )
	{
	    $select = $this->select()->where('module=?', $module->module);
	    $rows = $this->fetchAll( $select );
	    return $rows;
	}
	
	# para rol_resource_detail
	/*
	 * 
	 * 	insert into owlcms_acl_role_resource_detail ( role_id, resource_id )
		select 1, rs.id from owlcms_sys_resource rs where id not in ( 3, 12, 20, 26, 59, 60 )
		
		insert into owlcms_acl_role_resource_detail ( role_id, resource_id )
		select 3, rs.id from owlcms_sys_resource rs where id in ( 3, 12, 20, 26, 59, 60 )
	*/
	
	/**
	 * Returns all resources registered  
	 * @return Zend_Paginator_Adapter_DbTableSelect instance of
	 */
	public function getList() 
	{
		$select = $this->select();
		return $this->fetchAll($select);
	}
	
	/**
	 * Return all registered module 
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	function getModules()
	{
		$rows = array();
		$frontendOptions = array('lifetime'=>60*60*24, 'automatic_serialization'=>true);
		$backendOptions = array('cache_dir'=> APPLICATION_CACHE_PATH );
		$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
		
		if ( $cache->test('acl_getModules') ) {
			$rows = $cache->load('acl_getModules');
		} else {
			$select = $this->select( )
				->distinct()
				->from( array('sr'=> $this->_name), 'module' );
			$rows = $this->fetchAll($select);
			$cache->save($rows, 'acl_getModules');
		}
	    return $rows;
	} 

	/**
	 * Return a resource by module, controller and action
	 * @param string $module
	 * @param string $controller
	 * @param string $action
	 * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
	 */
	function getIdByDetail( $module, $controller, $action )
	{
		$select = $this->select()
			->where( 'module=?', $module)
			->where( 'controller=?', $controller)
			->where( 'actioncontroller=?', $action)
			->limit(1);
		;
		#echo $select->__toString();
		$row = $this->fetchRow($select);
		#if ( !$row ) return null;
		#return $row;
		return $row;
	}

}


