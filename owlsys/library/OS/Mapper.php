<?php
abstract class OS_Mapper implements OS_IMapper
{
    protected $_dbTable;
    
    protected function setDbTable ($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (! $dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }

    /**
     *
     * @return Zend_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
        return $this->getDbTable()->getAdapter();
    }
    /*protected function __construct() {}
    protected function __clone(){}*/
}