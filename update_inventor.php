<?
/**
 * @author		Ian M. Fink
 *
 * @file		update_inventor.php
 *
 * @copyright	Copyright (C) 2025 Ian M. Fink.  All rights reserved.
 * 				A commercial license can be made available.
 *
 * Contact:		www.linkedin.com/in/ianfink
 *
 * Tabstop:		4
 * 
 */

$dd_php_files = $_ENV['PHP_INCLUDE_PATH'];
$html_string = "";

require_once $dd_php_files . "/dd_funcs.php";
require_once dd_get_body_file();
require_once dd_get_leader_file();
require_once dd_get_style_file();
require_once dd_get_trailer_file();

if (!dd_init()) {
	exit(0);
}

$web_page = new web_page_class("Update Inventor", $dd_php_files);

$html_string = "";

$web_page->add_style(dd_get_style());
$web_page->set_body_tag(dd_get_body_tag());
$web_page->add_leader(dd_get_leader());
$web_page->add_trailer(dd_get_trailer());

if (empty($_REQUEST['form_id'])) {
	$html_string .= "Try: try again.\n";
} else {
	$db_object = new form_table_object($dd_db_handle, "inventor");
	$html_string .= $db_object->prepare_update_string_on_id();
	$db_object->add_int_column("id");
	$db_object->add_int_column("zipcode_seven");
	$db_object->add_int_column("zipcode_four");
	$update_result = $db_object->update_table();

	if ($update_result) {
		$html_string .= "<BR>\nUPDATE SUCCESS\n";
	} else {
		$html_string .= "<BR>\nUPDATE FAILED\n";
	}

	$html_string .= "\n<BR>\n";
	$html_string .= $db_object->update_table();

}

$html_string .= "\n<BR>\n";
$html_string .= "\n<BR>\n";

$web_page->add_to_body($html_string);

$web_page->conclude_page();
$web_page->write_page();

/*
 * End of file:	update_inventor.php
 */
?>
