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
dd_get_default_leader()
{
	$tmp_leader_str = dd_copyright_statement("COMMENT");
	$tmp_leader_str .= "<CENTER>\n";
	$tmp_leader_str .= "<TABLE border=\"0\" class=\"leader\" style=\"width:100%\"><TR>\n";
	$tmp_leader_str .= "<TD class=\"leader\">" . dd_make_public_img_tag("speedo1.png") . "</TD>\n";
	$tmp_leader_str .= "<TD class=\"leader\">";
	$tmp_leader_str .= "<P>\n";
	$tmp_leader_str .= "<CENTER>\n";
	$tmp_leader_str .= "<SPAN class=\"dd_bold_me\">";
	$tmp_leader_str .= dd_leader_anchor("Docket Dashboard&#153;", "dashboard");
	$tmp_leader_str .= "</SPAN>\n";
	$tmp_leader_str .= "<BR>\n";
	$tmp_leader_str .= "<BR>\n";
	$tmp_leader_str .= dd_leader_anchor("Home", "home");
	$tmp_leader_str .= " | ";

	$the_dropdowns = array();
	$the_dropdowns["Create Application"] = "create_application";
	$the_dropdowns["Find Application"] = "find_application";
	$tmp_leader_str .= dd_dropdown_button("Application", $the_dropdowns);

//	$tmp_leader_str .= dd_leader_anchor("Create Application", "create_application");
	$tmp_leader_str .= " | ";
	$tmp_leader_str .= dd_leader_anchor("Create Matter", "create_matter");
	$tmp_leader_str .= " | ";
	$tmp_leader_str .= dd_leader_anchor("Create Office Action", "create_office_action");
	$tmp_leader_str .= " | ";

	$the_dropdowns = array();
	$the_dropdowns["New Inventor"] = "new_inventor";
	$the_dropdowns["Find Inventor"] = "find_inventor";
	$tmp_leader_str .= dd_dropdown_button("Inventor", $the_dropdowns);
//	$tmp_leader_str .= dd_leader_anchor("New Inventor", "new_inventor");
	$tmp_leader_str .= " | ";
	$tmp_leader_str .= dd_leader_anchor("Print Dashboard", "print_dashboard");
	$tmp_leader_str .= " | ";
	$tmp_leader_str .= dd_leader_anchor("e1", "e1");
	$tmp_leader_str .= " | ";
	$tmp_leader_str .= dd_leader_anchor("db1", "db1");
	$tmp_leader_str .= "</CENTER>\n";
	$tmp_leader_str .= "\n</P>\n";
	$tmp_leader_str .= "</TD>\n";
	$tmp_leader_str .= "<TD class=\"leader\">" . dd_make_public_img_tag("speedo2.png") . "</TD>\n";
	$tmp_leader_str .= "</TR>\n";
	$tmp_leader_str .= "</TABLE>\n";

	$tmp_leader_str .= "<HR ALIGN=\"CENTER\" SIZE=\"2\">\n";
	$tmp_leader_str .= "</CENTER>\n";
	$tmp_leader_str .= "<P></P>\n";

	return $tmp_leader_str;
} /* dd_get_default_leader */

//**************************************************************************************

function
dd_get_alt_default_leader()
{
	$tmp_leader_str = dd_copyright_statement("COMMENT");
	$tmp_leader_str .= "<CENTER>\n";
	$tmp_leader_str .= "<div class=\"dd_button_dropdown\">\n";
	$tmp_leader_str .= "\t<button class=\"dd_dropbtn\">Dropdown</button>\n";
	$tmp_leader_str .= "\t<div class=\"dd_button_dropdown-content\">\n";
	$tmp_leader_str .= "\t<a href=\"#\">Link 1</a>\n";
	$tmp_leader_str .= "\t<a href=\"#\">Link 2</a>\n";
	$tmp_leader_str .= "\t<a href=\"#\">Link 2</a>\n";
	$tmp_leader_str .= "\t</div>\n";
	$tmp_leader_str .= "</div>\n";

	$tmp_leader_str .= dd_dropdown_button("Another", array(
		"Foo" => "foourl",
		"Foo Doo" => "foodooURL"));

	$tmp_leader_str .= "<HR ALIGN=\"CENTER\" SIZE=\"2\">\n";

	$tmp_leader_str .= "</CENTER>\n";
	return $tmp_leader_str;
} // dd_get_alt_default_leader

/*
 * End of file: default.php
 */

?>
