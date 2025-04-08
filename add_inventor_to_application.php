<?
/**
 * @author		Ian M. Fink
 *
 * @file		add_inventor_to_application.php
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

include $dd_php_files . "/dd_funcs.php";
include dd_get_body_file();
include dd_get_leader_file();
include dd_get_style_file();
include dd_get_trailer_file();

$html_string .= "<HTML>\n";
$html_string .= "<HEAD>\n";
$html_string .= dd_get_style();
$html_string .= "<TITLE>Add Inventor to Application</TITLE>\n";
$html_string .= "</HEAD>\n";
$html_string .= dd_get_body_tag();
$html_string .= dd_get_leader();

$input_method = "";

if ($_REQUEST['client'] != NULL || $_REQUEST['matter']) {
	$input_method = "POST";
} else if ($_GET['client'] != NULL || $_GET['matter']) {
	$input_method = "GET";
}


$html_string .= "<CENTER>\n";
// form_application_id

if (isset($_REQUEST['form_application_id']) && isset($_REQUEST['form_inventor_id'])) {
	dd_add_inventor_to_application($_REQUEST['form_application_id'], $_REQUEST['form_inventor_id']);
} else if (isset($_REQUEST['form_application_id']) && isset($_REQUEST['form_client'])) {
	$client_inventor_array = dd_get_client_inventor_id_array($_REQUEST['form_client']);
	$form_string = "";
	$form_string .= dd_form_start("add_inventor_to_application", "POST");
	$form_string .= "<TABLE>\n";
	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">Add Inventor to Application<BR>";
	$form_string .= sprintf("Client: %s Matter: %s<BR>", $_REQUEST['form_client'],
		$_REQUEST['form_matter']);
	$form_string .= sprintf("Title: \"%s\"", $_REQUEST['form_application_title']);
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";
	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Select Inventor:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_form_dropdown("form_inventor_id", $client_inventor_array);
	$form_string .= sprintf("<INPUT type=\"hidden\" id=\"form_application_id\" name=\"form_application_id\" value=\"%s\">", $_REQUEST['form_application_id']);
	$form_string .= "</TD>";

	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">";
	$form_string .= "<INPUT TYPE=\"submit\" value=\"Add Inventor to Application\">";
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "</TABLE>\n";
	$form_string .= "</FORM>\n";
	$html_string .= $form_string;
}
/*
if ($input_method == "") {
	$html_string .= dd_create_list_inventor_form();
} else {
	$form_client = $input_method == "POST" ? $_REQUEST['client'] :
		$_GET['client'];
	$form_matter = $input_method == "POST" ? $_REQUEST['matter'] :
		$_GET['matter'];
	$html_string .= dd_list_applications($form_client, $form_matter, null);
}
*/

$html_string .= "</CENTER>\n";

$html_string .= dd_get_trailer();

$html_string .= "</BODY>\n";
$html_string .= "</HTML>\n";

echo $html_string;

/*
 * End of file:	add_inventor_to_application.php
 */
?>
