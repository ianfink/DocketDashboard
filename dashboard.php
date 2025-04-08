<?
/**
 * @author		Ian M. Fink
 *
 * @file		dashboard.php
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

if (!dd_init()) {
	exit(0);
}

header("Refresh: 3600; dashboard");

require_once dd_get_body_file();
require_once dd_get_leader_file();
require_once dd_get_style_file();
require_once dd_get_trailer_file();

$html_string .= "<HTML>\n";
$html_string .= "<HEAD>\n";
$html_string .= dd_get_style();
$html_string .= "<TITLE>Dashboard</TITLE>\n";
$html_string .= "</HEAD>\n";
$html_string .= dd_get_body_tag();
$html_string .= dd_get_leader();

/*
$input_method = "";
if ($_POST['client'] != NULL || $_POST['matter']) {
	$input_method = "POST";
} else if ($_GET['client'] != NULL || $_GET['matter']) {
	$input_method = "GET";
}
*/


$html_string .= "<CENTER>\n";
/*
if ($input_method == "") {
	$html_string .= dd_create_list_inventor_form();
} else {
	$form_client = $input_method == "POST" ? $_POST['client'] :
		$_GET['client'];
	$form_matter = $input_method == "POST" ? $_POST['matter'] :
		$_GET['matter'];
	$html_string .= dd_list_applications($form_client, $form_matter, null);
}
*/

$html_string .= "<NOBR>Updated:&nbsp;&nbsp;" . $globals->date_now->format("d F Y H:i:s") . "</NOBR><BR><BR>\n";

$html_string .= dd_OA_dashboard("ifink", FALSE);
$html_string .= "\n<BR>\n";
$html_string .= dd_application_dashboard("ifink", FALSE);

$html_string .= "</CENTER>\n";

$html_string .= dd_get_trailer();

$html_string .= "</BODY>\n";
$html_string .= "</HTML>\n";

echo $html_string;

/*
 * End of file: dashboard.php
 */
?>
