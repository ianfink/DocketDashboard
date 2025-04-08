<?
/**
 * @author		Ian M. Fink
 *
 * @file		show_matter.php
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

$html_string .= "<HTML>\n";
$html_string .= "<HEAD>\n";
$html_string .= dd_get_style();
$html_string .= "<TITLE>Show Matter</TITLE>\n";
$html_string .= "</HEAD>\n";
$html_string .= dd_get_body_tag();
$html_string .= dd_get_leader();

$input_method = "";

if ($_POST['client'] != NULL) {
	$input_method = "POST";
} else if ($_GET['client'] != NULL) {
	$input_method = "GET";
}

if ($input_method == "") {
	$html_string .= "Client/Matter not found.";
} else {
	$client = $input_method == "POST" ? $_POST['client'] :
		$_GET['client'];
	$matter = $input_method == "POST" ? $_POST['matter'] :
		$_GET['matter'];
	$row = dd_get_client_matter($client, $matter);
	if ($row['error'] != NULL) {
		$html_string .= $row['error'];
	} else {
		$html_string .= dd_show_matter($row);
		$html_string .= "<BR>\n";
		$html_string .= "<BR>\n";
		$html_string .= "<BR>\n";
		$html_string .= dd_list_inventors($client, $matter);
	}
}



$html_string .= "\n<BR>\n";
$html_string .= "\n<BR>\n";


$html_string .= "</BODY>\n";
$html_string .= "</HTML>\n";

echo $html_string;

/*
 * End of file:	show_matter.php
 */
?>
