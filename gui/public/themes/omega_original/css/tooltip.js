/**
* ispCP ω (OMEGA) a Virtual Hosting Control System
*
* @copyright	2006-2008 by ispCP | http://isp-control.net
* @link http://isp-control.net
* @author ispCP Team (2007)
* @license
*    This program is free software; you can redistribute it and/or
*    modify it under the terms of the GPL General Public License
*    as published by the Free Software Foundation; either version 2.0
*    of the License, or (at your option) any later version.
*
*    This program is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GPL General Public License for more details.
*
*    You may have received a copy of the GPL General Public License
*    along with this program.
*
*    An on-line copy of the GPL General Public License can be found
*    http://www.fsf.org/licensing/licenses/gpl.txt
*/
function showTip(id, e) {
	x = (document.all) ? window.event.x + document.body.scrollLeft : e.pageX;
	y = (document.all) ? window.event.y + document.body.scrollTop  : e.pageY;

	tip = document.getElementById(id);

	tip.style.left = (x + 10) + "px";
	tip.style.top  = (y + 10) + "px";
	tip.style.display = "block";
}

function hideTip(id) {
	document.getElementById(id).style.display = "none";
}