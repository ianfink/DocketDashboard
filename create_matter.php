<?
/**
 * @author		Ian M. Fink
 *
 * @file		create_matter.php
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

include $dd_php_files . "/dd_funcs.php";
include dd_get_body_file();
include dd_get_leader_file();
include dd_get_style_file();
include dd_get_trailer_file();

if (!dd_init()) {
	exit(0);
}

$html_string .= "<HTML>\n";
$html_string .= "<HEAD>\n";
$html_string .= dd_get_style();
$html_string .= "<TITLE>Create Matter</TITLE>\n";
$html_string .= "</HEAD>\n";
$html_string .= dd_get_body_tag();
$html_string .= dd_get_leader();

$input_method = "";

if ($_POST['client_val'] != NULL || $_POST['new_matter_val']) {
	$input_method = "POST";
} else if ($_GET['client_val'] != NULL || $_GET['new_matter_val']) {
	$input_method = "GET";
}

if ($input_method == "") {
	$html_string .= "<CENTER>\n";
	$html_string .= dd_create_matter_form();
	$html_string .= "</CENTER>\n";
} else {
	$client_val = $input_method == "POST" ? $_POST['client_val'] :
		$_GET['client_val'];
	$new_matter_val = $input_method == "POST" ? $_POST['new_matter_val'] :
		$_GET['new_matter_val'];
	$html_string .= "<CENTER>\n";
	$html_string .= dd_newly_created_matter($client_val, $new_matter_val);
	$html_string .= "</CENTER>\n";
}

$html_string .= dd_get_trailer();

$html_string .= "</BODY>\n";
$html_string .= "</HTML>\n";

echo $html_string;

/*
 * End of file:	create_matter.php
 */
?>
