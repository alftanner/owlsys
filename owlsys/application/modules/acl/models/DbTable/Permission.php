<?php

class Acl_Model_DbTable_Permission extends Zend_Db_Table_Abstract
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
     * Returns permissions assigned to a specific role
     * @param Zend_Db_Table_Row_Abstract $role
     * @return Ambigous <Zend_Db_Table_Row_Abstract, NULL, unknown>
     */
    public function getAllowedByRole( Acl_Model_Role $role )
    {
        $select = $this->select()
            ->from( $this->_name)
			->where("role_id = ?", $role->getId(), Zend_Db::INT_TYPE)
            ->where("isAllowed = 1")
        ;
        //Zend_Debug::dump($select->__toString());
    	$rows = $this->fetchAll($select);
    	return $rows;
	}
	
	/**
	 * 
	 * @param Acl_Model_Role $role
	 * @return Zend_Db_Table_Rowset_Abstract
	 */
	public function getResourcesByRole(Acl_Model_Role $role) 
	{
	    $rows = array();
	    $prefix = Zend_Registry::get('tablePrefix');
	    $select = $this->select()
    	    ->setIntegrityCheck(false)
    	    ->from( array('r'=>$prefix.'acl_resource'), array('id AS resource_id','module','controller','actioncontroller') )
    	    ->joinLeft(array('p'=>$this->_name), 'r.id=p.resource_id AND p.role_id='.$role->getId(), array('id','isAllowed'))
	    ;
// 	    Zend_Debug::dump($select->__toString());
	    $rows = $this->fetchAll($select);
	    return $rows;
	}

	/**
	 * 
	 * @param Acl_Model_Resource $resource
	 * @return number
	 */
	public function countByResource(Acl_Model_Resource $resource)
	{
	    $count = 0;
	    $select = $this->select()
	       ->from( $this->_name, new Zend_Db_Expr('COUNT(id) AS total') )
	       ->where('resource_id=?', $resource->getId(), Zend_Db::INT_TYPE)
	       ->limit(1)
	    ;
	    $count = $this->fetchRow($select)->total;
	    return $count;
	}
}

