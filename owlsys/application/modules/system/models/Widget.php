<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package system
 * @subpackage models
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class System_Model_Widget extends Zend_Db_Table_Abstract
{

    /**
     * 
     * @var string
     */
    protected $_name = 'widget';
    /**
     * 
     * @var array
     */
    protected $_dependentTables = array ( 'System_Model_Widgetdetail' );
    /**
     * 
     * @var array
     */
    protected $_referenceMap = array (
    		'WidgetResource' => array(
    				'columns'			=> array ( 'resource_id' ),
    				'refTableClass'	=> 'Acl_Model_Resource',
    				'refColumns'		=> array ( 'id' ),
    				'onDelete'		=> self::CASCADE,
    				'onUpdate'		=> self::RESTRICT
    		)
    );

    /**
     * renames the table by adding the prefix defined in the global configuration parameters
     */
    function __construct() {
    	$this->_name = Zend_Registry::get('tablePrefix').$this->_name;
    	parent::__construct();
    }
    
    /**
     * returns the last position of a contact list according to the category they belong in ascending order
     * @param Zend_Db_Table_Row_Abstract $widget
     * @return number
     */
    function getLastPosition( Zend_Db_Table_Row_Abstract $widget)
    {
        $select = $this->select()
        	->where('position=?', $widget->position)
        	->order("ordering DESC")
        	->limit(1);
        ;
        $row = $this->fetchRow($select);
        if ( !$row ) return 0;
        return $row->ordering;
    }

    /**
     * Returns a recordseet of widgets
     * @return Zend_Paginator_Adapter_DbTableSelect
     */
    public function getPaginatorAdapterList() 
    {
		$select = $this->select()
					->setIntegrityCheck(false)
					->from( array('wgt' => $this->_name), array('id', 'position', 'title', 'published', 'ordering') )
					->joinInner( array('rs' => Zend_Registry::get('tablePrefix').'acl_resource'), 'rs.id = wgt.resource_id', array('module', 'controller', 'actioncontroller') )
					->order('wgt.position')
					->order('wgt.ordering')
		;
		return new Zend_Paginator_Adapter_DbTableSelect($select);
    }
    
    /**
     * Moves the record position one above
     * @param Zend_Db_Table_Row_Abstract $widget
     * @return boolean|Ambigous <mixed, multitype:>
     */
    function moveUp( Zend_Db_Table_Row_Abstract $widget )
    {
    	$ordering = $widget->ordering;
    	if ( $ordering < 1 ) return false;
    	else
    	{
    		$select = $this->select()
	    		->order('ordering DESC')
	    		->where("ordering < ?", $ordering, Zend_Db::INT_TYPE)
	    		->where("position = ?", $widget->position);
    		$previousItem = $this->fetchRow($select);
    		if ( $previousItem )
    		{
    			$previousPosition = $previousItem->ordering;
    			$previousItem->ordering = $ordering;
    			$previousItem->save();
    			$widget->ordering = $previousPosition;
    			return $widget->save();
    		}
    	}
    	return false;
    }
    
    /**
     * Moves the record position one down
     * @param Zend_Db_Table_Row_Abstract $widget
     * @return boolean|Ambigous <mixed, multitype:>
     */
    function moveDown( Zend_Db_Table_Row_Abstract $widget )
    {
    	$ordering = $widget->ordering;
    	if ( $ordering == $this->getLastPosition($widget) ) return false;
    	else
    	{
    		$select = $this->select()
	    		->order('ordering ASC')
	    		->where("ordering > ?", $ordering, Zend_Db::INT_TYPE)
	    		->where("position = ?", $widget->position);
    		$nextItem = $this->fetchRow($select);
    		if ( $nextItem )
    		{
    			$nextPosition = $nextItem->ordering;
    			$nextItem->ordering = $ordering;
    			$nextItem->save();
    			$widget->ordering = $nextPosition;
    			return $widget->save();
    		}
    	}
    	return false;
    }

}

