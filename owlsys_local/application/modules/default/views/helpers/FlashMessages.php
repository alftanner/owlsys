<?php
/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 * @package default
 * @subpackage helpers
 * @author roger castañeda <rogercastanedag@gmail.com>
 * @version 1
 */
class Zend_View_Helper_FlashMessages extends Zend_View_Helper_Abstract
{
	
    /**
     * Return a message
     * @return string
     */
	public function flashMessages(  )
	{
		
		$messages = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
		$output = '';
		if ( count($messages) > 0 ) {
			foreach ($messages as $message) {
				$alertType = 'alert-block';
				switch ( strtolower($message['type']) ) {
					default: case 'warning': $alertType = 'alert-block'; break;
					case 'error': $alertType = 'alert-error'; break;
					case 'success': $alertType = 'alert-success'; break;
					case 'info': $alertType = 'alert-info'; break;
				}
				$output .= '<div class="alert '.$alertType.'">';
				$output .= '<button type="button" class="close" data-dismiss="alert">×</button>';
				$output .= ( isset($message['header']) && strlen($message['header']) > 0 ) ? '<h4>'.$message['header'].'</h4>' : '';
				$output .= $message['message'];
				$output .= '</div>';
			}
		}
		return $output;
	}
	
}
