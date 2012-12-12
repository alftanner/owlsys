<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package content
 * @subpackage models
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Content_Model_Category extends Zend_Db_Table_Abstract
{
    protected $_name = 'content_category';
    protected $_dependentTables = array ( 'Content_Model_Article', 'Content_Model_Category' );
    protected $_referenceMap = array (
    		'Parent' => array(
    				'columns'		=> array ( 'parent_id' ),
    				'refTableClass'	=> 'Content_Model_Category',
    				'refColumns'	=> array ( 'id' ),
    				'onDelete'		=> self::RESTRICT,
    				'onUpdate'		=> self::RESTRICT
    		),
    );
    
    function __construct() {
    	$this->_name = Zend_Registry::get('tablePrefix').$this->_name;
    	parent::__construct();
    }
    
    /**
     * Devuelve un listado de categoria de contenidos usando el adaptador de paginacion
     * @return Zend_Paginator_Adapter_DbTableSelect
     */
    function getPaginatorAdapterList()
    {
    	$select = $this->select()
	    	->setIntegrityCheck(false)
	    	->from( array('cat'=>$this->_name), array('id', 'title', 'published', 'ordering', 'parent_id') )
	    	->joinLeft( array('cp'=> $this->_name ) , 'cat.parent_id = cp.id', array('title AS parent_title'))
	    	->order('cat.ordering ASC')
    	;
    	#echo $select->__toString();
    	return new Zend_Paginator_Adapter_DbTableSelect($select);
    }
    
    /**
     * Devuelve un listado simple de categoria de contenidos
     * @return Zend_Db_Table_Rowset_Abstract|NULL
     */
    function getSimpleList()
    {
        $select = $this->select()
        	->from( $this->_name )
        ;
        $rows = $this->fetchAll( $select );
        if ( $rows ) return $rows;
        return null;
    }
    
    /**
     * Ordena una categoria de articulo y luego la guardar
     * @param Zend_Db_Table_Row_Abstract $category
     * @return Ambigous <mixed, multitype:>
     */
    function save( Zend_Db_Table_Row_Abstract $category )
    {
    	$category->ordering = $this->_getLastPosition($category)+1;
    	return $category->save();
    }
    
    /**
     * Devuelve la ultima posicion de una categoria de contenido respecto a su categoria padre
     * @param Zend_Db_Table_Row_Abstract $category
     * @return number
     */
    private function _getLastPosition( Zend_Db_Table_Row_Abstract $category )
    {
    	if ( $category->parent_id > 0 )
    	{
    		$select = $this->select()
	    		->where( 'parent_id = ?', $category->parent_id, Zend_Db::INT_TYPE )
	    		->order( 'ordering DESC' );
    	} else {
    		$select = $this->select()
	    		->where( 'parent_id = ?', 0, Zend_Db::INT_TYPE )
	    		->order( 'ordering DESC' );
    	}
    	$row = $this->fetchRow( $select );
    	if ( $row )
    		return $row->ordering;
    	return 0;
    }
    
    /**
     * Mueve la posicion de una categoria de contenido una posicion arriba
     * @param Zend_Db_Table_Row_Abstract $category
     * @return boolean|Ambigous <mixed, multitype:>
     */
    function moveUp( Zend_Db_Table_Row_Abstract $category )
    {
    	$ordering = $category->ordering;
    	if ( $ordering < 1 ) return false;
    	else
    	{
    		$select = $this->select()
	    		->order('ordering DESC')
	    		->where("ordering < ?", $ordering, Zend_Db::INT_TYPE)
	    		->where("parent_id = ?", $category->parent_id, Zend_Db::INT_TYPE);
    		$previousItem = $this->fetchRow($select);
    		if ( $previousItem )
    		{
    			$previousPosition = $previousItem->ordering;
    			$previousItem->ordering = $ordering;
    			$previousItem->save();
    			$category->ordering = $previousPosition;
    			return $category->save();
    		}
    	}
    }
    
    /**
     * Mueve la posicion de una categoria de contenido una posicion abajo
     * @param Zend_Db_Table_Row_Abstract $category
     * @return boolean|Ambigous <mixed, multitype:>
     */
    function moveDown( Zend_Db_Table_Row_Abstract $category )
    {
    	$ordering = $category->ordering;
    	if ( $ordering == $this->_getLastPosition($category) ) return false;
    	else
    	{
    		$select = $this->select()
	    		->order('ordering ASC')
	    		->where("ordering > ?", $ordering, Zend_Db::INT_TYPE)
	    		->where("parent_id = ?", $category->parent_id, Zend_Db::INT_TYPE);
    		$nextItem = $this->fetchRow($select);
    		if ( $nextItem )
    		{
    			$nextPosition = $nextItem->ordering;
    			$nextItem->ordering = $ordering;
    			$nextItem->save();
    			$category->ordering = $nextPosition;
    			return $category->save();
    		}
    	}
    }


}

