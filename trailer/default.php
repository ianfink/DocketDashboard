<?
/**
 * @author		Ian M. Fink
 *
 * @file		default.php
 *
 * @copyright	Copyright (C) Ian M. Fink.  All rights reserved.
 *				This work is unpublished.
 * 				A commercial license can be made available.
 *
 * Contact:		www.linkedin.com/in/ianfink
 *
 * Tabstop:		4
 *
 */

function
dd_get_default_trailer()
{
	$tmp_trailer_str .= "<P></P>\n";
	$tmp_trailer_str .= "<CENTER>\n";
	$tmp_trailer_str .= "<P>\n";
	$tmp_trailer_str .= "<HR ALIGN=\"CENTER\" SIZE=\"2\">\n";
	$tmp_trailer_str .= dd_anchor("Home", "home");
	$tmp_trailer_str .= " | ";
	$tmp_trailer_str .= dd_anchor("Create Application", "create_application");
	$tmp_trailer_str .= " | ";
	$tmp_trailer_str .= dd_anchor("Create Matter", "create_matter");
	$tmp_trailer_str .= " | ";
	$tmp_trailer_str .= dd_anchor("Find Application", "find_application");
	$tmp_trailer_str .= " | ";
	$tmp_trailer_str .= dd_anchor("e1", "e1");
	$tmp_trailer_str .= " | ";
	$tmp_trailer_str .= dd_anchor("db1", "db1");
	$tmp_trailer_str .= "<BR>\n";
/*
	$the_year = date("Y");
	$copyright_statement = "";
	$copyright_statement .= "Copyright &copy; 2019";
	$copyright_statement .= (strcmp("2019", $the_year) == 0) ? " " : "-" . $the_year . " ";
	$copyright_statement .= "Ian M. Fink.  All Rights Reserved.";
	$tmp_trailer_str .= $copyright_statement;
*/
	$tmp_trailer_str .= dd_copyright_statement("HTML");
	$tmp_trailer_str .= "\n</P>\n";
	$tmp_trailer_str .= "</CENTER>\n";
	$tmp_trailer_str .= "<P></P>\n";
	$tmp_trailer_str .= dd_copyright_statement("COMMENT");
	$tmp_trailer_str .= "\n";

	return $tmp_trailer_str;
} /* dd_get_default_trailer */

/*
 * End of file: default.php
 */

?>
