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
class Content_Model_Article extends Zend_Db_Table_Abstract
{

    /**
     * 
     * @var string
     */
    protected $_name = 'content_article';
    /**
     * 
     * @var array
     */
    protected $_referenceMap = array (
    		'Category' => array(
    				'columns'		=> array ( 'category_id' ),
    				'refTableClass'	=> 'Content_Model_Category',
    				'refColumns'	=> array ( 'id' ),
    				'onDelete'		=> self::RESTRICT,
    				'onUpdate'		=> self::RESTRICT
    		),
    );
    
    /**
     * renames the table by adding the prefix defined in the global configuration parameters
     */
    function __construct() {
    	$this->_name = Zend_Registry::get('tablePrefix').$this->_name;
    	parent::__construct();
    }
    
    /**
     * Returns a recordset of articles order by category_id asc and ordering
     * @return Zend_Paginator_Adapter_DbTableSelect
     */
    function getPaginatorAdapterList()
    {
        $select = $this->select()
        	->setIntegrityCheck(false)
        	->from( array('art'=>$this->_name), array('id', 'title', 'published', 'ordering', 'category_id') )
        	->joinInner( array('cat'=>Zend_Registry::get('tablePrefix').'content_category') , 'art.category_id = cat.id', array('title AS category_title'))
        	->order('art.category_id ASC')
        	->order('art.ordering ASC')
        ;
        #echo $select->__toString();
        return new Zend_Paginator_Adapter_DbTableSelect($select);
    }
    
    /**
     * Sets the position to record and save
     * @param Zend_Db_Table_Row_Abstract $article
     * @return Ambigous <mixed, multitype:>
     */
    function save( Zend_Db_Table_Row_Abstract $article )
    {
    	$article->ordering = $this->_getLastPosition($article)+1;
    	return $article->save();
    }
    
    /**
     * returns the last position of a contact list according to the category they belong in ascending order
     * @param Zend_Db_Table_Row_Abstract $article
     */
    private function _getLastPosition( Zend_Db_Table_Row_Abstract $article )
    {
    	if ( $article->category_id > 0 )
    	{
    		$select = $this->select()
	    		->where( 'category_id = ?', $article->category_id, Zend_Db::INT_TYPE )
	    		->order( 'ordering DESC' );
    	} else {
    		$select = $this->select()
	    		->where( 'category_id = ?', 0, Zend_Db::INT_TYPE )
	    		->order( 'ordering DESC' );
    	}
    	$row = $this->fetchRow( $select );
    	if ( $row )
    		return $row->ordering;
    	return 0;
    }
    
    /**
     * Moves the record position one above
     * @param Zend_Db_Table_Row_Abstract $article
     */
    function moveUp( Zend_Db_Table_Row_Abstract $article )
    {
    	$ordering = $article->ordering;
    	if ( $ordering < 1 ) return false;
    	else
    	{
    		$select = $this->select()
	    		->order('ordering DESC')
	    		->where("ordering < ?", $ordering, Zend_Db::INT_TYPE)
	    		->where("category_id = ?", $article->category_id, Zend_Db::INT_TYPE);
    		$previousItem = $this->fetchRow($select);
    		if ( $previousItem )
    		{
    			$previousPosition = $previousItem->ordering;
    			$previousItem->ordering = $ordering;
    			$previousItem->save();
    			$article->ordering = $previousPosition;
    			return $article->save();
    		}
    	}
    }
    
    /**
     * Moves the record position one down
     * @param Zend_Db_Table_Row_Abstract $article
     * @return boolean|Ambigous <mixed, multitype:>
     */
    function moveDown( Zend_Db_Table_Row_Abstract $article )
    {
    	$ordering = $article->ordering;
    	if ( $ordering == $this->_getLastPosition($article) ) return false;
    	else
    	{
    		$select = $this->select()
	    		->order('ordering ASC')
	    		->where("ordering > ?", $ordering, Zend_Db::INT_TYPE)
	    		->where("category_id = ?", $article->category_id, Zend_Db::INT_TYPE);
    		$nextItem = $this->fetchRow($select);
    		if ( $nextItem )
    		{
    			$nextPosition = $nextItem->ordering;
    			$nextItem->ordering = $ordering;
    			$nextItem->save();
    			$article->ordering = $nextPosition;
    			return $article->save();
    		}
    	}
    }

    /**
     * Returns a recordset of articles filter title  
     * @param string $char
     * @return Zend_Db_Table_Rowset_Abstract|multitype:
     */
    function getByChar( $char )
    {
        $select = $this->select();
        if ( strlen($char) > 0 )
        	$select->where("title like '%".$char."%'");
        #echo $select->__toString();
        $rows = $this->fetchAll( $select );
        if ( $rows->count() > 0 )
        	return $rows;
        else return array();
    }

    /**
     * Returns a recordset of published articles filter by category
     * @param Zend_Db_Table_Row_Abstract $category
     * @param string $orderField
     * @param string $orderType
     * @return Zend_Paginator_Adapter_DbTableSelect
     */
    function getByCategory( Zend_Db_Table_Row_Abstract $category, $orderField = 'ordering', $orderType = 'ASC' )
    {
        $select = $this->select()
        	->where ( 'published=?', 1 )
        	->where ( 'category_id=?', $category->id, Zend_Db::INT_TYPE )
        	->order( $orderField.' '.$orderType )
        ;
        return new Zend_Paginator_Adapter_DbTableSelect($select);
    }
    
}

