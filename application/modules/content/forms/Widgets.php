<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package content
 * @subpackage forms
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Content_Form_Widgets extends System_Form_Widget
{

    /**
     * (non-PHPdoc)
     * @see Zend_Form::init()
     */
    public function init()
    {
        parent::init();
        switch ($this->getAttrib('widgetType')) {
        	default:
        		$this->defaultParent();
        	break;
        }
    }
    
    private function defaultParent()
    {
    	return;
    }


}

