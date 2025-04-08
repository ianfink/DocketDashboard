<?

/**
 * @author		Ian M. Fink
 *
 * @file		default.php
 *
 * @copyright	Copyright (C) 2025 Ian M. Fink.  All rights reserved.
 * 				A commercial license can be made available.
 *
 * Contact:		www.linkedin.com/in/ianfink
 *
 * Tabstop:		4
 *
 */

function
dd_get_default_style()
{
	$tmp_style_str = "<STYLE><!--\n";

	/* ANCHOR */
	$tmp_style_str .= "a:link {color: #d6b11c;}\n";
	$tmp_style_str .= "a:visited {color: #d6b11c;}\n";
	$tmp_style_str .= "a:hover {color: #FFFF00;}\n";

	/* HR */
	$tmp_style_str .= "hr { background-color: #d6b11c; ";
	$tmp_style_str .= "height: 1px; border: 0; }\n";

	/* BODY */
	$tmp_style_str .= "body {\n";
	$tmp_style_str .= "	background-color: #000000;\n";
	$tmp_style_str .= "	color: #d6b11c;\n";
	$tmp_style_str .= "}\n";

	/* TABLE TH TD */
	$tmp_style_str .= "table, th, td {\n";
	$tmp_style_str .= "	border: 2px solid #606060;\n";
	$tmp_style_str .= "	border-width: 3px;\n";
	$tmp_style_str .= "}\n";

	/* END OF STYLES */
	$tmp_style_str .= "--></STYLE>\n";

	$tmp_style_str = "<STYLE><!--\n";
	$tmp_style_str .= "@import url(/css/s2.css);\n";

	$tmp_style_str .= "--></STYLE>\n";
	return $tmp_style_str;
} /* dd_get_default_style */

/*
 * End of file: default.php
 */

?>
