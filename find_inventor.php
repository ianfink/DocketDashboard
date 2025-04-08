<?
/**
 * @author		Ian M. Fink
 *
 * @file		find_inventor.php
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

$web_page = new web_page_class("Find Inventor", $dd_php_files);

$web_page->refresh_it(3600, "dashboard");

$web_page->add_style(dd_get_style());
$web_page->set_body_tag(dd_get_body_tag());
$web_page->add_leader(dd_get_leader());
$web_page->add_trailer(dd_get_trailer());
$globals->page_debug_object = $web_page->page_debug_object;

$required_array = array("first_name", "last_name", "client");
$something_requested = FALSE;

$table_columns = array("first_name", "middle_name", "last_name", "suffix",
	"preferred_name", "street_address", "apartment_id", "city", "state",
	"zipcode_seven", "zipcode_four", "timezone", "home_phone", "work_phone",
	"cell_phone", "email", "citizenship", "client");

foreach ($table_columns as $the_column) {
	if (!empty($_REQUEST['form_' . $the_column])) {
		$something_requested = TRUE;
		break;
	}
}

/*
foreach ($required_array as $required_element) {
	$tmp = "form_" . $required_element;
	$all_required = $all_required && !empty($_REQUEST[$tmp]);
//	$html_string .= "<!-- form_" . $required_element . " -->\n";
//	$html_string .= "<!-- \"" .  $_POST["form_" . $required_element] . "\" -->\n";
//	$html_string .= "<!-- \"" .  !empty($_POST["form_" . $required_element]) . "\" -->\n";
}
*/


if (!$something_requested) {
	$form_string = "";
	$form_string .= dd_form_start("find_inventor", "POST");
	$form_string .= "<CENTER>\n";
	$form_string .= "<TABLE> <!-- first table -->\n";

	$form_string .= "<TR class=\"dd_bold\">\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">Find Inventor</TD>\n";
	$form_string .= "</TR>\n";

	$tmp_client_array = dd_get_client_array();
	$def_array[''] = "Select";
	$the_client_array = array_merge($def_array, $tmp_client_array);

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
		. dd_input_text_with_size("form_first_name", 80) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Middle Name:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size("form_middle_name", 80) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Last Name:</TD>";
	$form_string .= "<TD align=\"left\">"
		. dd_input_text_with_size("form_last_name", 80) .  "</TD>\n";
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

	$def_array[''] = "Select";
	$the_country_array = array_merge($def_array, $the_country_array);

	$form_string .= dd_form_dropdown("form_citizenship", $the_country_array);
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	/* SUBMIT */

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">";
	$form_string .= "<BR><INPUT TYPE=\"submit\" VALUE=\"Find Inventor\">";
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
	$int_columns = array("zip_seven", "zip_four");
	$colums_utilized = array();
	foreach ($all_colums as $the_column) {
		$tmp = "form_" . $the_column;
		if (!empty($_REQUEST[$tmp])) {
			array_push($colums_utilized, $the_column);
		}
	}
	
//	$sql_stmt = "INSERT INTO inventor (";
	$sql_stmt = "SELECT * FROM inventor WHERE ";

	$sql_wheres = array();

	foreach ($colums_utilized as $the_column) {
		if (in_array($the_column, $int_columns)) {
			$tmp = sprintf("%s = :%s_binder", $the_column, $the_column);
		} else {
			$tmp = sprintf("%s ILIKE :%s_binder", $the_column, $the_column);
//			$tmp = sprintf("%s ILIKE CONCAT('%%', :%s_binder , '%%')", $the_column, $the_column);
//			$tmp = $the_column . " ILIKE CONCAT('%', " . ":" . $the_column . "_binder" . ", '%')";
// select * from inventor where first_name ILIKE CONCAT('%', 'stu', '%');

		}
		array_push($sql_wheres, $tmp);
	}

	$sql_stmt .= implode(" AND ", $sql_wheres);


	// $html_string .= $sql_stmt;

	$tmp_results = $dd_db_handle->prepare($sql_stmt);

	foreach ($colums_utilized as $the_column) {
		$tmp = sprintf(":%s_binder", $the_column);
//		$tmp_val = $_REQUEST["form_" . $the_column];
		$tmp_val = $_REQUEST["form_" . $the_column];
		$tmp_val = "%$tmp_val%";
		if (!strcmp($the_column, "zipcode_seven") || !strcmp($the_column, "zipcode_four")) {
			$tmp_results->bindValue($tmp, $tmp_val, PDO::PARAM_INT);
		} else {
			$tmp_results->bindValue($tmp, $tmp_val, PDO::PARAM_STR);
//			$tmp_results->bindParam($tmp, $tmp_val, PDO::PARAM_STR);
		}
	}

	$exec_bool = $tmp_results->execute();

	if ($exec_bool) {
		$inventor_array = $tmp_results->fetchAll();
		$tmp_results->closeCursor();
		$html_string .= dd_show_inventor_result_table($inventor_array);
//		$html_string .= "\n<BR>\n";
//		$html_string .= $sql_stmt;
	} else {
		$err_array =  $tmp_results->errorInfo();
		$ret_string = sprintf("Error: SQLSTATE=\"%s\" Error Message=\"%s\" SQL=\"%s\"",
			$err_array[0], $err_array[2], $sql_stmt);
		$html_string .= "<FONT size=\"+1\" color=\"red\">" . $ret_string . "</FONT>";
	}

} /* process form end */

$web_page->add_to_body($html_string);

$web_page->conclude_page();
$web_page->write_page();

/*
 * End of file:	find_inventor.php
 */
?>
