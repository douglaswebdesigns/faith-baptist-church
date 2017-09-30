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


if (parseInt(scrolling_down_popup_plugin_cookies)!=NaN)
var random_num=Math.floor(Math.random()*scrolling_down_popup_plugin_cookies)
var ie=document.all
var dom=document.getElementById

function initboxv2(){
if (!dom&&!ie)
return
crossboxcover=(dom)?document.getElementById("scrolling-down-popup-plugin-top") : document.all.scrolling-down-popup-plugin-top
crossbox=(dom)?document.getElementById("scrolling-down-popup-plugin"): document.all.scrolling-down-popup-plugin
scroll_top=(ie)? truebody().scrollTop : window.pageYOffset
crossbox.height=crossbox.offsetHeight
crossboxcover.style.height=parseInt(crossbox.height)+"px"
crossbox.style.top=crossbox.height*(-1)+"px"
crossboxcover.style.left=scrolling_down_popup_plugin_left_space+"px"
crossboxcover.style.top=scrolling_down_popup_plugin_top_space+"px"
crossboxcover.style.visibility=(dom||ie)? "visible" : "show"
dropstart=setInterval("dropinv2()",50)
}

function dropinv2(){
scroll_top=(ie)? truebody().scrollTop : window.pageYOffset
if (parseInt(crossbox.style.top)<0){
crossboxcover.style.top=scroll_top+scrolling_down_popup_plugin_top_space+"px"
crossbox.style.top=parseInt(crossbox.style.top)+scrolling_down_popup_plugin_speed+"px"
}
else{
clearInterval(dropstart)
crossbox.style.top=0
}
}

function dismissboxv2(){
if (window.dropstart) clearInterval(dropstart)
crossboxcover.style.visibility="hidden"
}

function truebody(){
return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}

function get_cookie(Name) {
var search = Name + "="
var returnvalue = ""
if (document.cookie.length > 0) {
offset = document.cookie.indexOf(search)
if (offset != -1) {
offset += search.length
end = document.cookie.indexOf(";", offset)
if (end == -1)
end = document.cookie.length;
returnvalue=unescape(document.cookie.substring(offset, end))
}
}
return returnvalue;
}

if (scrolling_down_popup_plugin_cookies=="oncepersession" && get_cookie("popupcookie")=="" || scrolling_down_popup_plugin_cookies=="showalways" || parseInt(scrolling_down_popup_plugin_cookies)!=NaN && random_num==0){
if (window.addEventListener)
window.addEventListener("load", initboxv2, false)
else if (window.attachEvent)
window.attachEvent("onload", initboxv2)
else if (document.getElementById || document.all)
window.onload=initboxv2
if (scrolling_down_popup_plugin_cookies=="oncepersession")
document.cookie="popupcookie=yes"
}