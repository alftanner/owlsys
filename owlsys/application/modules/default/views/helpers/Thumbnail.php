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
class Zend_View_Helper_Thumbnail extends Zend_View_Helper_Abstract
{
    /** 
     * Return a url path where thumbnail was generated  
     * @param   String  $imagePath  Le chemin vers l'image
     * @param   int     $width      La largeur en pixel de la vignette
     * @param   int     $height     La largeur en pixel de la vignette
     * @param   String  $destPath   Le chemin de destination de la vignette
     * @param   String  $urlPath    L'url pour l'affichage de la vignette
    */
    public function thumbnail($imagePath, $width, $height, $destPath, $urlPath) {
        require_once APPLICATION_PATH . '/../library/PhpThumb/ThumbLib.inc.php';
    	$thumb = PhpThumbFactory::create($imagePath);
    	$thumb->resize($width, $height);
    	$file = basename($thumb->getFileName(), '.'.strtolower($thumb->getFormat())).'_thumb.'.strtolower($thumb->getFormat());
    	$destPath = rtrim($destPath, '/') . '/' . $file;
    	if (!file_exists($destPath)) {
    		$thumb->save($destPath);
    	}
    	$urlPath = rtrim($urlPath) . '/' . $file;
    
    	return $urlPath;
    }
    
}