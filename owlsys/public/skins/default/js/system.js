/**
 * Copyright 2012 Roger Castañeda
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version. 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU General Public License for more details. 
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 */
$(document).ready(function(){
	//$('#menu-0').remove();
	$("#collapse4").on('shown', function () {
		$(this).css('overflow', 'visible');
	});
	$("#collapse4").on('hidden', function () {
		$(this).css('overflow', 'hidden');
	});
	$("#collapse6").on('shown', function () {
		$(this).css('overflow', 'visible');
	});
	$("#collapse6").on('hidden', function () {
		$(this).css('overflow', 'hidden');
	});
	$("#collapse7").on('shown', function () {
		$(this).css('overflow', 'visible');
	});
	$("#collapse7").on('hidden', function () {
		$(this).css('overflow', 'hidden');
	});
	$("#collapse8").on('shown', function () {
		$(this).css('overflow', 'visible');
	});
	$("#collapse8").on('hidden', function () {
		$(this).css('overflow', 'hidden');
	});
	
	$('.menu-horizontal-bootstrap').children().each( function(){
		if ( $(this).find('ul').length > 0 ) {
			$(this).addClass('dropdown');
			$(this).children('a').attr('data-toggle', 'dropdown');
			$(this).children('a').addClass('dropdown-toggle');
			$(this).children('a').append(' <b class=\"caret\"></b>');
			$(this).find('ul').addClass('dropdown-menu');
			$(this).find('li').has('ul.dropdown-menu').each( function(){
				$(this).addClass('dropdown-submenu');
			});
		}
	});
	
	$('.chosen').chosen();
});

