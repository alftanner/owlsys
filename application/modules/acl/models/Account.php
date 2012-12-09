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
class Acl_Model_Account extends Zend_Db_Table_Abstract {
	
    /**
     * 
     * @var string
     */
	protected $_name = 'acl_account';
	/**
	 * (non-PHPdoc)
	 * @var array
	 */
	protected $_dependentTables = array ( 'Contact_Model_Contact' );
	/**
	 * 
	 * @var array
	 */
	protected $_referenceMap = array ( 
		'Role' => array(
			'columns'		=> array ( 'role_id' ),
			'refTableClass'	=> 'Acl_Model_Role',
			'refColumns'	=> array ( 'id' ),
			'onDelete'		=> self::RESTRICT,
			'onUpdate'		=> self::RESTRICT
		),
	);
	
	/**
	 * Rename the name of the table adding prefix defined in global configuration params
	 */
	function __construct() {
		$this->_name = Zend_Registry::get('tablePrefix').$this->_name;
		parent::__construct();
	}
	
	/**
	 * Login function authentication system 
	 * @param Zend_Db_Table_Row $account
	 * @return boolean
	 */
	function Login( Zend_Db_Table_Row $account ) {
		// set up the auth adapter
		$db = Acl_Model_Account::getDefaultAdapter();
		$authAdapter = new Zend_Auth_Adapter_DbTable($db);
		$authAdapter->setTableName( $this->_name )
					->setIdentityColumn('email')
					->setCredentialColumn('password')
					->setCredentialTreatment('MD5(CONCAT(salt,?)) and block = 0');
					#->setCredentialTreatment('MD5(?) and block = 0');
		$authAdapter->setIdentity( $account->email );
		$authAdapter->setCredential( $account->password );
		$result = $authAdapter->authenticate();
		Zend_Session::regenerateId();
		if ($result->isValid()) {
			$auth = Zend_Auth::getInstance();
			$storage = $auth->getStorage();
			$storage->write( $authAdapter->getResultRowObject( array('id', 'email', 'registerdate', 'lastvisitdate', 'role_id') ) );
			
			$account = $this->find( $authAdapter->getResultRowObject()->id )->current();
			#$account = $this->createRow( $account->toArray() );
			$account->lastvisitdate = Zend_Date::now()->toString('YYYY-MM-dd HH:mm:ss');
			$account->save();
			return true;
		}
		return false;
	}

	/**
	 * Returns a recordset
	 * @return Zend_Paginator_Adapter_DbTableSelect
	 */
	public function getPaginatorAdapterList() {
		$select = $this->select()
					->setIntegrityCheck(false)
					->from( array('aa' => $this->_name), array('id', 'email', 'registerdate', 'lastvisitdate', 'block', ) )
					->joinInner( array('ro' => Zend_Registry::get('tablePrefix').'acl_role'), 'aa.role_id = ro.id', array('name as role') );
		return new Zend_Paginator_Adapter_DbTableSelect($select);
	}

	/**
	 * Returns a recordsset
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getSimpleList()
	{
	    $select = $this->select()
	    	->order('id ASC')
	    ;
	    $records = $this->fetchAll( $select );
	    return $records;
	}
	
}

