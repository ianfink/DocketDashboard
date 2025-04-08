<?
/**
 * @author		Ian M. Fink
 *
 * @file		show_inventor.php
 *
 * @copyright	Copyright (C) 2025 Ian M. Fink.  All rights reserved.
 * 				A commercial license can be made available.
 *
 * Contact:		www.linkedin.com/in/ianfink
 *
 * Tabstop:		4
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

$web_page = new web_page_class("Show Inventor", $dd_php_files);

$web_page->add_style(dd_get_style());
$web_page->set_body_tag(dd_get_body_tag());
$web_page->add_leader(dd_get_leader());
$web_page->add_trailer(dd_get_trailer());

$what_to_do = "";

$input_method = "";

if ($_POST['inventor_id'] != NULL) {
	$input_method = "POST";
} else if ($_GET['inventor_id'] != NULL) {
	$input_method = "GET";
}


if (!empty($_REQUEST['form_change_info'])) {
	$row = dd_get_inventor_row($_REQUEST['form_id']);
	$xhr_form = dd_update_inventor_form($row);
	$web_page->add_to_xhr_html($xhr_form);
	$web_page->write_xhr_html();
} else if ($input_method == "") {
	$html_string .= "Inventor ID not found.";
} else {
	$web_page->include_js_file("get_inventor_update_form.js");
	$inventor_id = $input_method == "POST" ? $_POST['inventor_id'] :
		$_GET['inventor_id'];
	$row = dd_get_inventor_row($inventor_id);
	if ($row['error'] != NULL) {
		$html_string .= $row['error'];
	} else {
		$html_string .= "<CENTER>\n";
		$html_string .= dd_show_inventor_table($row);
		$html_string .= "\n<BR>\n";
		$html_string .= dd_show_applications_of_inventor($inventor_id);
		$html_string .= "</CENTER>\n";
	}
}



$html_string .= "\n<BR>\n";
$html_string .= "\n<BR>\n";

$web_page->add_to_body($html_string);

$web_page->conclude_page();
$web_page->write_page();

/*
 * End of file:	show_inventor.php
 */
?>
