/**
 *     Scrolling down popup plugin
 *     Copyright (C) 2011 - 2014 www.gopiplus.com
 *     http://www.gopiplus.com/work/2011/07/23/scrolling-down-popup-wordpress-plugin/
 * 
 *     This program is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     (at your option) any later version.
 * 
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 * 
 *     You should have received a copy of the GNU General Public License
 *     along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

function _sdp_submit()
{
	if(document.sdp_form.sdp_text.value == "")
	{
		alert("Please enter popup text.")
		document.sdp_form.sdp_text.focus();
		return false;
	}
	else if((document.sdp_form.sdp_width.value == "") || isNaN(document.sdp_form.sdp_width.value))
	{
		alert("Please enter popup window width, only number.")
		document.sdp_form.sdp_width.focus();
		return false;
	}
	else if((document.sdp_form.sdp_left_space.value == "") || isNaN(document.sdp_form.sdp_left_space.value))
	{
		alert("Please enter position (left space), only number.")
		document.sdp_form.sdp_left_space.focus();
		return false;
	}
	else if((document.sdp_form.sdp_top_space.value == "") || isNaN(document.sdp_form.sdp_top_space.value))
	{
		alert("Please enter position (top space), only number.")
		document.sdp_form.sdp_top_space.focus();
		return false;
	}
	else if((document.sdp_form.sdp_speed.value == "") || isNaN(document.sdp_form.sdp_speed.value))
	{
		alert("Please enter scrolling speed, only number.")
		document.sdp_form.sdp_speed.focus();
		return false;
	}
}

function _sdp_delete(id)
{
	if(confirm("Do you want to delete this record?"))
	{
		document.sdp_display.action="options-general.php?page=scrolling-down-popup-plugin&ac=del&did="+id;
		document.sdp_display.submit();
	}
}	

function _sdp_redirect()
{
	window.location = "options-general.php?page=scrolling-down-popup-plugin";
}

function _sdp_help()
{
	window.open("http://www.gopiplus.com/work/2011/07/23/scrolling-down-popup-wordpress-plugin/");
}