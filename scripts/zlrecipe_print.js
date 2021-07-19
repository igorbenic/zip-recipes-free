var win=null;
function zlrPrint(id, plugin_path)
{
	var content = document.getElementById(id).innerHTML;
	var title = document.getElementsByClassName("zrdn-element_recipe_title")[0].innerHTML;
	win = window.open();
	self.focus();
	win.document.open();
	win.document.write('<html><title>');
	win.document.write(title);
	win.document.write('</title>');
	win.document.write('<head>');
	win.document.write('<link charset="utf-8" href="'+zrdn_print_styles.grid_style+'" rel="stylesheet" type="text/css" />');
	win.document.write('<link charset="utf-8" href="'+zrdn_print_styles.stylesheet_url+'" rel="stylesheet" type="text/css" />');
	win.document.write('<link charset="utf-8" href="'+zrdn_print_styles.print_css+'" rel="stylesheet" type="text/css" />');
	win.document.write('</head><body onload="print();">');
	win.document.write('<div id=\'zrdn-recipe-container\' >');
	win.document.write(content);
	win.document.write('</div>');
	win.document.write('</body></html>');
	win.document.close();
}
