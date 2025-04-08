<?
/**
 * @author		Ian M. Fink
 *
 * @file		find_application.php
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

$web_page = new web_page_class("Find Application", $dd_php_files);

$web_page->refresh_it(360, "dashboard");

$web_page->add_style(dd_get_style());
$web_page->set_body_tag(dd_get_body_tag());
$web_page->add_leader(dd_get_leader());
$web_page->add_trailer(dd_get_trailer());
$globals->page_debug_object = $web_page->page_debug_object;

$web_page->include_js_file("get_matter_dropdown.js");

$form_client = $_REQUEST['client'];
$form_matter = $_REQUEST['matter'];
$form_app_ser_no = $_REQUEST['form_app_ser_no'];
$form_client_matter_id = $_REQUEST['form_client_matter_id'];

$html_string .= "<CENTER>\n";

if (empty($form_client) && empty($form_matter) && empty($form_app_ser_no) && empty($form_client_matter_id)) {
	$html_string .= dd_find_application_form();
} else if (!empty($form_app_ser_no) && empty($form_client) && empty($form_matter)) {
	$form_app_ser_no = str_replace("/", "", $form_app_ser_no);
	$form_app_ser_no = str_replace(",", "", $form_app_ser_no);
	$form_app_ser_no = trim($form_app_ser_no);
	$html_string .= dd_list_applications(null, null, null, $form_app_ser_no, null);
} else if (!empty($form_client_matter_id)) { 
	$html_string .= dd_list_applications(null, null, null, null, trim($form_client_matter_id));
} else {
	$html_string .= dd_list_applications($form_client, $form_matter, null, null, null);
}

$html_string .= "</CENTER>\n";

$web_page->add_to_body($html_string);

$web_page->conclude_page();
$web_page->write_page();

/*
 * End of file:	find_application.php
 */
 ?>
