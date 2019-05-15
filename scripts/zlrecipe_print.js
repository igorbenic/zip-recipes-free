/*
 License: GPLv3 or later

 Copyright 2014 Gezim Hoxha
 This code is derived from the 2.6 version build of ZipList Recipe Plugin released by ZipList Inc.:
 http://get.ziplist.com/partner-with-ziplist/wordpress-recipe-plugin/ and licensed under GPLv3 or later
 */

/*
 This file is part of Zip Recipes Plugin.

 Zip Recipes Plugin is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 Zip Recipes Plugin is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Zip Recipes Plugin. If not, see <http://www.gnu.org/licenses/>.
 */

var win=null;
function zlrPrint(id, plugin_path)
{
	var content = document.getElementById(id).innerHTML;
	win = window.open();
	self.focus();
	win.document.open();
	win.document.write('<html><head>');
	win.document.write("<link charset='utf-8' href='" + plugin_path + "styles/zlrecipe-print.css' rel='stylesheet' type='text/css' />");
	win.document.write('</head><body onload="print();">');
	win.document.write('<div id=\'zlrecipe-print-container\' >');
	win.document.write(content);
	win.document.write('</div>');
	win.document.write('</body></html>');
	win.document.close();
}
