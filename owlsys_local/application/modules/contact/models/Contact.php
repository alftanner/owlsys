<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package contact
 * @subpackage models
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Contact_Model_Contact extends Zend_Db_Table_Abstract
{
    /**
     * 
     * @var string
     */
    protected $_name = 'contact';
    /**
     * 
     * @var array
     */
    protected $_referenceMap = array (
    	'Category' => array(
	    	'columns'		=> array ( 'category_id' ),
	    	'refTableClass'	=> 'Contact_Model_Category',
	    	'refColumns'	=> array ( 'id' ),
	    	'onDelete'		=> self::CASCADE,
	    	'onUpdate'		=> self::RESTRICT,
    	),
	    'Account' => array(
    		'columns'		=> array ( 'account_id' ),
    		'refTableClass'	=> 'Acl_Model_Account',
    		'refColumns'	=> array ( 'id' ),
    		'onDelete'		=> self::CASCADE,
    		'onUpdate'		=> self::RESTRICT,
	    ),
    );
    
    /**
     * renames the table by adding the prefix defined in the global configuration parameters
     */
    function __construct()
    {
        $this->_name = Zend_Registry::get('tablePrefix').$this->_name;
        parent::__construct();
    }
    
    /**
     * Returns a recordset of contacts registered
     * @return NULL|Zend_Db_Table_Rowset_Abstract
     */
    function getPublishedList()
    {
        $select = $this->select()
        	->setIntegrityCheck(false)
        	->from( array('co'=>$this->_name), 
        		array('id', 'con_position', 'address', 'city', 'country', 'postcode', 'telephone', 'fax', 
        				'misc', 'image', 'email_to', 'ordering', 'account_id', 'category_id', 'mobile', 'webpage') )
        	->joinInner( array('ac'=>Zend_Registry::get('tablePrefix').'acl_account') , 'ac.id = co.account_id', array('first_name', 'last_name'))
        	->where('published=?', 1)
        ;
        $rows = $this->fetchAll($select);
        if ( !$rows ) return null;
        return $rows;
    }
    
    /**
     * Returns a recordset of contacts registered
     * @return Zend_Paginator_Adapter_DbTableSelect
     */
    function getPaginatorAdapterList()
    {
        $tblPrefix = Zend_Registry::get('tablePrefix');
    	$select = $this->select()
    		->setIntegrityCheck(false)
    		->from( array('co'=>$this->_name), 
    			array('webpage', 'mobile', 'category_id', 'account_id', 'ordering', 'published', 'email_to', 'image', 'misc', 'fax', 'telephone',
    				'postcode', 'country', 'city', 'address', 'con_position', 'id') 
    		)
    		->joinInner( array('cat'=>$tblPrefix.'contact_category') , 'co.category_id = cat.id', array('title AS category_title'))
    		->joinInner( array('acc'=>$tblPrefix.'acl_account') , 'co.account_id = acc.id', array())
    		->order('id ASC')
    	;
    	#echo $select->__toString();
    	return new Zend_Paginator_Adapter_DbTableSelect($select);
    }
    
    /**
     * returns the last position of a contact list according to the category they belong in ascending order  
     * @access private
     * @param Zend_Db_Table_Row_Abstract $contact
     */
    private function _getLastPosition( Zend_Db_Table_Row_Abstract $contact )
    {
    	$select = $this->select()
    		->where( 'category_id = ?', $contact->category_id, Zend_Db::INT_TYPE )
    		->order( 'ordering DESC' );
    	$row = $this->fetchRow( $select );
    	if ( $row )
    		return $row->ordering;
    	return 0;
    }
    
    /**
     * Moves the record position one above
     * @param Zend_Db_Table_Row_Abstract $contact
     * @return boolean|Ambigous <mixed, multitype:>
     */
    function moveUp( Zend_Db_Table_Row_Abstract $contact )
    {
    	$ordering = $contact->ordering;
    	if ( $ordering < 1 ) return false;
    	else
    	{
    		$select = $this->select()
	    		->order('ordering DESC')
	    		->where("ordering < ?", $ordering, Zend_Db::INT_TYPE)
	    		->where("category_id = ?", $contact->category_id, Zend_Db::INT_TYPE);
    		$previousItem = $this->fetchRow($select);
    		if ( $previousItem )
    		{
    			$previousPosition = $previousItem->ordering;
    			$previousItem->ordering = $ordering;
    			$previousItem->save();
    			$contact->ordering = $previousPosition;
    			return $contact->save();
    		}
    	}
    }
    
    /**
     * Moves the record position one down
     * @param Zend_Db_Table_Row_Abstract $contact
     * @return boolean|Ambigous <mixed, multitype:>
     */
    function moveDown( Zend_Db_Table_Row_Abstract $contact )
    {
    	$ordering = $contact->ordering;
    	if ( $ordering == $this->_getLastPosition($contact) ) return false;
    	else
    	{
    		$select = $this->select()
	    		->order('ordering ASC')
	    		->where("ordering > ?", $ordering, Zend_Db::INT_TYPE)
	    		->where("category_id = ?", $contact->category_id, Zend_Db::INT_TYPE);
    		$nextItem = $this->fetchRow($select);
    		if ( $nextItem )
    		{
    			$nextPosition = $nextItem->ordering;
    			$nextItem->ordering = $ordering;
    			$nextItem->save();
    			$contact->ordering = $nextPosition;
    			return $contact->save();
    		}
    	}
    }
    
    /**
     * Sets the position to record and save
     * @param Zend_Db_Table_Row_Abstract $contact
     * @return Ambigous <mixed, multitype:>
     */
    function save( Zend_Db_Table_Row_Abstract $contact )
    {
    	$contact->ordering = $this->_getLastPosition($contact)+1;
    	return $contact->save();
    }

    /**
     * Returns a recordset of contacts filter by category
     * @param Zend_Db_Table_Row_Abstract $category
     * @return Zend_Db_Table_Rowset_Abstract
     */
    function getByCategory( Zend_Db_Table_Row_Abstract $category )
    {
        $tblPrefix = Zend_Registry::get('tablePrefix');
    	$select = $this->select()
    		->setIntegrityCheck(false)
    		->from( array('co'=>$this->_name), 
    			array('webpage', 'mobile', 'category_id', 'account_id', 'ordering', 'published', 'email_to', 'image', 'misc', 'fax', 'telephone',
    				'postcode', 'country', 'city', 'address', 'con_position', 'id') 
    		)
    		->joinInner( array('cat'=>$tblPrefix.'contact_category') , 'co.category_id = cat.id', array('title AS category_title'))
    		->joinInner( array('acc'=>$tblPrefix.'acl_account') , 'co.account_id = acc.id', array('first_name', 'last_name'))
    		->where( 'cat.id = ?', (int) $category->id, Zend_Db::INT_TYPE )
    		->order('id ASC')
    	; 
    	#echo $select->__toString();
    	return $this->fetchAll( $select );
    }
}

