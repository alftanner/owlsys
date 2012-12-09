<?php

class System_Model_Skin extends Zend_Db_Table_Abstract
{

	protected $_name = 'skin';
	
	function __construct() {
		$this->_name = Zend_Registry::get('tablePrefix').$this->_name;
		parent::__construct();
	}
	
	/**
	 * Return skin selected
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	function getSkinSelected()
	{
		$select = $this->select()->where('isselected=1')->limit(1);
		return $this->fetchRow($select);
	}
	
	public function getPaginatorAdapterList()
	{
		$select = $this->select();
		return new Zend_Paginator_Adapter_DbTableSelect($select);
	}

	function getSelected()
	{
		$select = $this->select();
		$select->where('isselected=1')->limit(1);
		return $this->fetchRow($select);
	}
}

