<?php
/**
 * Copyright 2012 Steve 
 * @see http://www.zfforums.com/zend-framework-components-13/core-infrastructure-19/how-do-i-validate-url-1269.html
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package OS\Application\Plugins
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class OS_Application_Validators_Url extends Zend_Validate_Abstract
{
    /**
     * @access public INVALID_URL
     * @var string
     */
    const INVALID_URL = 'invalidUrl';
    /**
     * @access protected
     * @var array $_messageTemplates
     */
    protected $_messageTemplates = array(
		self::INVALID_URL => "'%value%' is not a valid URL.",
    );
    
	/**
	 * (non-PHPdoc)
	 * @see Zend_Validate_Interface::isValid()
	 */
 	public function isValid($value) {
 	    $valueString = (string) $value;
 	    $this->_setValue($valueString);
 	    
 	    if (!Zend_Uri::check($value)) {
 	    	$this->_error(self::INVALID_URL);
 	    	return false;
 	    }
 	    return true;
	}
    
}