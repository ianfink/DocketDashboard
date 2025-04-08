<?
/**
 * @author		Ian M. Fink
 *
 * @file		new_inventor.php
 *
 * @copyright	Copyright (C) 2025 Ian M. Fink.  All rights reserved.
 * 				A commercial license can be made available.
 *
 * Contact:		www.linkedin.com/in/ianfink
 *
 * Tabstop:		4
 * 
 */

global $dd_db_handle;

$html_string = "";
$dd_php_files = $_ENV['PHP_INCLUDE_PATH'];

require_once $dd_php_files . "/dd_funcs.php";
require_once dd_get_body_file();
require_once dd_get_leader_file();
require_once dd_get_style_file();
require_once dd_get_trailer_file();

if (!dd_init()) {
	exit(0);
}

$html_string .= "<HTML>\n";
$html_string .= "<HEAD>\n";
$html_string .= dd_get_style();
$html_string .= "<TITLE>New Inventor</TITLE>\n";
$html_string .= "</HEAD>\n";
$html_string .= dd_get_body_tag();
$html_string .= dd_get_leader();

$required_array = array("first_name", "last_name", "client");
$all_required = TRUE;

foreach ($required_array as $required_element) {
	$tmp = "form_" . $required_element;
	$all_required = $all_required && !empty($_POST[$tmp]);
/*
	$html_string .= "<!-- form_" . $required_element . " -->\n";
	$html_string .= "<!-- \"" .  $_POST["form_" . $required_element] . "\" -->\n";
	$html_string .= "<!-- \"" .  !empty($_POST["form_" . $required_element]) . "\" -->\n";
*/
}


if (!$all_required) {
	$form_string = "";
	$form_string .= dd_form_start("new_inventor", "POST");
	$form_string .= "<CENTER>\n";
	$form_string .= "<TABLE> <!-- first table -->\n";

	$form_string .= "<TR class=\"dd_bold\">\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">New Inventor</TD>\n";
	$form_string .= "</TR>\n";

	$the_client_array = dd_get_client_array();
	$the_country_array = dd_get_country_array();

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Client:</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_form_dropdown("form_client", $the_client_array);
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">First Name:</TD>";
	$form_string .= "<TD align=\"left\">"
		. dd_input_text_required_with_size("form_first_name", 80) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Middle Name:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size("form_middle_name", 80) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Last Name:</TD>";
	$form_string .= "<TD align=\"left\">"
		. dd_input_text_required_with_size("form_last_name", 80) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Suffix (e.g., Jr., III, etc.):</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size("form_suffix", 80) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Preferred Name:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size("form_preferred_name", 80) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Street Address:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size("form_street_address", 80) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Appartment ID/Unit/No.:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size("form_apartment_id", 80) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">City:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size("form_city", 80) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">State:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size("form_state", 80) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Zip Code (five):</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size("form_zipcode_seven", 80) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Zip Code (four):</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size("form_zipcode_four", 80) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Time Zone:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size("form_timezone", 80) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Home Phone:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size("form_home_phone", 80) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Cell Phone:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size("form_cell_phone", 80) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Work Phone:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size("form_work_phone", 80) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Email Address:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size("form_email", 80) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Citizenship:</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_form_dropdown("form_citizenship", $the_country_array);
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	/* SUBMIT */

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">";
	$form_string .= "<BR><INPUT TYPE=\"submit\" VALUE=\"Save New Inventor\">";
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";
	$form_string .= "</TABLE> <!-- first table -->\n";
	$form_string .= "</CENTER>\n";
	$form_string .= "</FORM>\n";

	$html_string .= $form_string;
} else {
	$all_colums = array("first_name", "middle_name", "last_name", "preferred_name",
		"street_address", "apartment_id", "city", "state", "zipcode_seven", "zipcode_four",
		"timezone", "home_phone", "work_phone", "cell_phone", "client", "email", "citizenship");
	$colums_utilized = array();
	foreach ($all_colums as $the_column) {
		$tmp = "form_" . $the_column;
		if (!empty($_POST[$tmp])) {
			array_push($colums_utilized, $the_column);
		}
	}
	
	$sql_stmt = "INSERT INTO inventor (";
	$is_first = TRUE;
	foreach ($colums_utilized as $the_column) {
		if ($is_first) {
			$sql_stmt .= $the_column;
			$is_first = FALSE;
		} else {
			$sql_stmt .= ", " .$the_column;
		}
	}

	$sql_stmt .= ") VALUES (";
	$is_first = TRUE;
	foreach ($colums_utilized as $the_column) {
		if ($is_first) {
			$sql_stmt .= sprintf(":%s_binder", $the_column);
			$is_first = FALSE;
		} else {
			$sql_stmt .= sprintf(", :%s_binder", $the_column);
		}
	}
	$sql_stmt .= ")";


	// $html_string .= $sql_stmt;

	$tmp_results = $dd_db_handle->prepare($sql_stmt);

	foreach ($colums_utilized as $the_column) {
		$tmp = sprintf(":%s_binder", $the_column);
		$tmp_val = trim($_POST["form_" . $the_column]);
		if (!strcmp($the_column, "zipcode_seven") || !strcmp($the_column, "zipcode_four")) {
			$tmp_results->bindValue($tmp, $tmp_val, PDO::PARAM_INT);
		} else {
			$tmp_results->bindValue($tmp, $tmp_val, PDO::PARAM_STR);
		}
	}

	$exec_bool = $tmp_results->execute();

	if ($exec_bool) {
		$ret_string = "SUCCESS";
	} else {
		$err_array =  $tmp_results->errorInfo();
		$ret_string = sprintf("Error: SQLSTATE=\"%s\" Error Message=\"%s\" SQL=\"%s\"",
			$err_array[0], $err_array[2], $insert_sql);
		}

	if (!strcmp($ret_string, "SUCCESS")) {
		$html_string .= "It worked!";
	} else {
		$html_string .= "<FONT size=\"+1\" color=\"red\">" . $ret_string . "</FONT>";
		
	}
} /* process form end */

$html_string .= dd_get_trailer();

$html_string .= "</BODY>\n";
$html_string .= "</HTML>\n";

echo $html_string;

/*
 * End of file:	new_inventor.php
 */
?>
