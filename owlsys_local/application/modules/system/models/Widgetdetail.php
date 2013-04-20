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
class System_Model_Widgetdetail extends Zend_Db_Table_Abstract
{

    /**
     * @var string
     */
    protected $_name = 'widget_detail';
    /**
     * @var array
     */
    #protected $_primary = array('widget_id', 'menuitem_id');
    /**
     * @var array
     */
    protected $_referenceMap = array (
		'Widget' => array(
			'columns'			=> array ( 'widget_id' ),
			'refTableClass'	=> 'System_Model_Widget',
			'refColumns'		=> array ( 'id' ),
    	),
	    'MenuItem' => array(
			'columns'			=> array ( 'menuitem_id' ),
			'refTableClass'	=> 'Menu_Model_Item',
			'refColumns'		=> array ( 'id' ),
	    )
    );
    
    /**
     * renames the table by adding the prefix defined in the global configuration parameters
     * @author recg [rogercastanedag@gmail.com]
     */
    function __construct() {
    	$this->_name = Zend_Registry::get('tablePrefix').$this->_name;
    	parent::__construct();
    }
    
    /**
     * Check if a widget is renderable in a menu item
     * @param Zend_Db_Table_Row_Abstract $widget
     * @return boolean
     */
    public function isRenderForAll( Zend_Db_Table_Row_Abstract $widget )
    {
        $select = $this->select()
        	->where( 'widget_id=?', $widget->id )
        	->where( 'IFNULL(menuitem_id,0)=0' )
        	->limit(1)
        ;
        #echo $select->__toString();
        $rowCount = $this->fetchAll ( $select );
        #echo $rowCount->count();
        if ( $rowCount->count() == 1 ) return true;
        return false;
    }

    /**
     * Returns widgets filter by hook and menu item id
     * @param int $itemId
     * @param string $hook
     * @return Zend_Db_Table_Rowset_Abstract|NULL
     */
    function getWidgetsByHookAndItemId($itemId, $hook)
    {
        $select = $this->select()
	        ->setIntegrityCheck(false)
	        ->from( array('wgt' => Zend_Registry::get('tablePrefix').'widget'), array('id', 'position', 'title', 'published', 'ordering', 'params', 'resource_id', 'widget_id', 'showtitle') )
	        ->joinInner( array('wd'=> $this->_name), 'wgt.id = wd.widget_id', array() ) 
	        #->from( array('wd'=> $this->_name), array() ) 
	        #->joinInner( array('wgt' => Zend_Registry::get('tablePrefix').'widget'), 'wgt.id = wd.widget_id', array('id', 'position', 'title', 'published', 'ordering', 'params', 'resource_id', 'widget_id') ) # widget
			->joinInner( array('rs' => Zend_Registry::get('tablePrefix').'acl_resource'), 'rs.id = wgt.resource_id', array('module', 'controller', 'actioncontroller') )
		;
		/*if ( $itemId == 0 ) $select->where( 'IFNULL(wd.menuitem_id,0)=0' );
		else {
		    $select->where( 'wd.menuitem_id IN (?)', $itemId );
		    
		}*/
        $select->where( 'IFNULL(wd.menuitem_id,0)=?',$itemId );
		$select->where( 'wgt.position=?', strval($hook) )
	        ->where( 'wgt.published=?', 1 )
	        ->order( 'wgt.ordering ASC' )
        ;
		#echo "<p>&nbsp;</p><p>&nbsp;</p>";
        #Zend_Debug::dump($select->__toString());
        $rows = $this->fetchAll($select);
        if ( $rows ) return $rows;
        return null;
    }
    
}

