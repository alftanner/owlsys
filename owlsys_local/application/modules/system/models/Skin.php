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
	    
		$frontendOptions = array('lifetime'=>60*60*24*30, 'automatic_serialization'=>true);
	    $backendOptions = array('cache_dir'=> APPLICATION_CACHE_PATH );
	    $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
	    
	    $row = null;
	    if ( $cache->load('skinSelected') ) {
	        $row = $cache->load('skinSelected');
	    } else {
	        $select = $this->select();
	        $select->where('isselected=1')->limit(1);
	        $row = $this->fetchRow($select);
	        $cache->save($row, 'skinSelected');
	    }
		
	    return $row;
	}
	
	public function getList()
	{
		$select = $this->select();
		return $this->fetchAll($select);
	}

}

