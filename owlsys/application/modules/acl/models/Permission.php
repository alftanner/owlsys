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

  protected $_name = 'acl_permission';
  
  protected $_referenceMap = array (
      'Resource' => array(
          'columns'		=> array ( 'resource_id' ),
          'refTableClass'	=> 'Acl_Model_DbTable_Resource',
          'refColumns'	=> array ( 'id' ),
      ),
      'Role' => array(
          'columns'		=> array ( 'role_id' ),
          'refTableClass'	=> 'Acl_Model_DbTable_Role',
          'refColumns'	=> array ( 'id' ),
      ),
  );
  
  function __construct ()
  {
    $this->_name = Zend_Registry::get('tablePrefix') . $this->_name;
    parent::__construct();
  }
  
  /**
   * Returns permissions (resources) assigned to a specific role
   * @param Zend_Db_Table_Row_Abstract $role
   * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
   */
  public function getAllowedByRole( $role )
  {
    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from( array('p'=>$this->_name), array('resource_id') )
      ->where("role_id = ?", $role->id, Zend_Db::INT_TYPE)
      ->where("isAllowed = 1")
    ;
    $rows = $this->fetchAll($select);
//     var_dump($select->__toString());
//     die();
    return $rows;
  }
  
  /**
   *
   * @param Acl_Model_Role $role
   * @return Zend_Db_Table_Rowset_Abstract
   */
  public function getResourcesByRole($role)
  {
    $rows = array();
    $prefix = Zend_Registry::get('tablePrefix');
    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from( array('r'=>$prefix.'acl_resource'), array('id AS resource_id','module','controller','actioncontroller') )
      ->joinLeft(array('p'=>$this->_name), 'r.id=p.resource_id AND p.role_id='.$role->id, array('id','isAllowed'))
    ;
    $rows = $this->fetchAll($select);
    return $rows;
  }
  
  /**
   *
   * @param Acl_Model_Resource $resource
   * @return number
   */
  public function countByResource( $resource )
  {
    $count = 0;
    $select = $this->select()
      ->from( $this->_name, new Zend_Db_Expr('COUNT(id) AS total') )
      ->where('resource_id=?', $resource->id, Zend_Db::INT_TYPE)
      ->limit(1)
    ;
    $count = $this->fetchRow($select)->total;
    return $count;
  }

  public function deleteByRole($role)
  {
    $where = $this->getAdapter()->quoteInto('role_id = ?', $role->id);
    $this->delete($where);
  }

}