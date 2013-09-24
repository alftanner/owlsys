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
class Acl_Model_Account extends Zend_Db_Table_Abstract
{
  protected $_name = 'acl_account';
  
  protected $_referenceMap = array(
      'refRole' => array(
          'columns'       => array('role_id'),
          'refTableClass' => 'Acl_Model_Role',
          'refColumns'    => array('id')
      )
  );
  
  function __construct ()
  {
    $this->_name = Zend_Registry::get('tablePrefix') . $this->_name;
    parent::__construct();
  }
  
  /**
   * Login function authentication system
   */
  public function login( $account, $accountToValidate ) {
    // set up the auth adapter
    $db = $this->getAdapter();
    $authAdapter = new Zend_Auth_Adapter_DbTable($db);
    $authAdapter->setTableName( $this->_name )
      ->setIdentityColumn('email')
      ->setCredentialColumn('password')
      ->setCredentialTreatment('isBlocked = 0');
    $authAdapter->setIdentity( $account->email );
    $authAdapter->setCredential( crypt($account->password, $accountToValidate->password) );
    $result = $authAdapter->authenticate();
    Zend_Session::regenerateId();
    if ($result->isValid()) {
      $auth = Zend_Auth::getInstance();
      $storage = $auth->getStorage();
      $storage->write( $authAdapter->getResultRowObject( array('id', 'email', 'registerdate', 'lastvisitdate', 'role_id', 'fullname', 'email_alternative') ) );
      $account = $this->find( $authAdapter->getResultRowObject()->id )->current();
      #$account = $this->createRow( $account->toArray() );
      $account->lastvisitdate = Zend_Date::now()->toString('YYYY-MM-dd HH:mm:ss');
      $account->save();
      return true;
    }
    return false;
  }
  
  /**
   * Return a list of accounts
   * @return Zend_Db_Table_Rowset_Abstract
   */
  public function getList() {
    $prefix = Zend_Registry::get('tablePrefix');
    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from( array('aa' => $this->_name), array('id', 'email', 'registerdate', 'lastvisitdate', 'isBlocked', ) )
      ->joinInner( array('ro' => $prefix.'acl_role'), 'aa.role_id = ro.id', array('name AS role', 'id AS roleId') );
    $rows = $this->fetchAll($select);
    return $rows;
  }
  
  /**
   * Filter a row by email
   * @param string $email
   * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
   */
  public function getByEmail($email)
  {
    /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
    $cache = Zend_Registry::get('cache');
    $cacheId = preg_replace("/[^A-Za-z0-9]/", '_', $email);
    $row = null;
    if ( $cache->test($cacheId) ) {
      $row = $cache->load($cacheId);
    } else {
      $select = $this->select();
      $select->where('email=?', $email);
      $select->limit(1);
      $row = $this->fetchRow($select);
      $cache->save($row, $cacheId);
    }
    return $row;
  }  
}

