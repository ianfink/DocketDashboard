<?
/**
 * @author		Ian M. Fink
 *
 * @file		get_matter_select.php
 *
 * @copyright	Copyright (C) 2025 Ian M. Fink.  All rights reserved.
 * 				A commercial license can be made available.
 *
 * Contact:		www.linkedin.com/in/ianfink
 *
 * Tabstop:		4
 * 
 */

$html_string = "";
$dd_php_files = $_ENV['PHP_INCLUDE_PATH'];

include $dd_php_files . "/dd_funcs.php";
/*
include dd_get_body_file();
include dd_get_leader_file();
include dd_get_style_file();
*/

if (empty($_GET["form_client"])) {
	$html_string .= "<!-- not found -->";
} else {
	$html_string .= dd_form_dropdown("form_matter", dd_get_matter_array($_GET["form_client"]));
}

echo $html_string;

/*
 * End of file:	get_matter_select.php
 */
?>
