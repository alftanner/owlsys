<?php

class Acl_Model_DbTable_Resource extends Zend_Db_Table_Abstract
{

    protected $_name = 'acl_resource';
    
    protected $_dependentTables = array ( 'Acl_Model_DbTable_Permission', 'menu_Model_DbTable_Item', 'System_Model_DbTable_Widget' );
    
    function __construct() {
        $this->_name = Zend_Registry::get('tablePrefix').$this->_name;
        parent::__construct();
    }
    
    /**
     * Returns all resources registered order by module / controller asc
     * @return Zend_Db_Table_Rowset_Abstract instance of | array
     */
    public function getAll( )
    {
        $select = $this->select();
        $rows = $this->fetchAll( $select, array('module'=>'asc', 'controller'=>'asc') );
        return $rows;
    }
    
    /**
     * @param Acl_Model_Resource $resource
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getByModule( Acl_Model_Resource $resource )
    {
        $select = $this->select()->where('module=?', $resource->getModule());
        $rows = $this->fetchAll( $select );
        return $rows;
    }
    
    /**
     * Return all registered module
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getModules()
    {
        $rows = array();
        /* @var $cache Zend_Cache_Core|Zend_Cache_Frontend */
        $cache = Zend_Registry::get('cache');
        $cacheId = 'acl_getModules';
        if ( $cache->test($cacheId) ) {
            $rows = $cache->load($cacheId);
        } else {
            $select = $this->select( )
                ->distinct()
                ->from( array('sr'=> $this->_name), 'module' );
            $rows = $this->fetchAll($select);
            $cache->save($rows, $cacheId);
        }
        return $rows;
    }
    
    /**
     * Return a resource by module, controller and action
     * @param Acl_Model_Resource $resource
     * @return Zend_Db_Table_Row_Abstract|null The row results per the Zend_Db_Adapter fetch mode, or null if no row found.
     */
    public function getIdByDetail( Acl_Model_Resource $resource )
    {
        $select = $this->select()
            ->where( 'module=?', $resource->getModule())
            ->where( 'controller=?', $resource->getController())
            ->where( 'actioncontroller=?', $resource->getActioncontroller())
            ->limit(1);
        $row = $this->fetchRow($select);
		return $row;
	}

	public function remove(Acl_Model_Resource $resource)
	{
	    $select = $this->select()
	      ->where('id=?',$resource->getId(), Zend_Db::INT_TYPE)
	      ->limit(1)
	    ;
	    $row = $this->fetchRow($select);
	    $row->delete();
	}

}

