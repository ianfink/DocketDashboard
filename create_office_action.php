<?
/**
 * @author		Ian M. Fink
 *
 * @file		create_office_action.php
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
$html_string .= "<TITLE>Create Office Action</TITLE>\n";
$html_string .= "</HEAD>\n";
$html_string .= dd_get_body_tag();
$html_string .= dd_get_leader();

$html_string .= "<CENTER>\n";

$client_val = $_POST['form_client'];
$matter_val = $_POST['form_matter'];

function
coa_main_form($coa_main_required_array)
{
	global	$client_val;
	global	$matter_val;

	$application_ids = dd_get_application_ids($client_val, $matter_val);
	$application_ids_count = count($application_ids);

	if ($application_ids_count == 0) {
		return sprintf("<SPAN class=\"blink\">No Application found for</SPAN> Client: %s Matter: %s", $client_val, $matter_val);
	}

	$form_string = "";
	$form_string .= dd_form_start("create_office_action", "POST");
	$form_string .= sprintf("<INPUT type=\"hidden\" id=\"form_client\" name=\"form_client\" value=\"%s\">\n", $client_val);
	$form_string .= sprintf("<INPUT type=\"hidden\" id=\"form_matter\" name=\"form_matter\" value=\"%s\">\n", $matter_val);
	$form_string .= "<INPUT type=\"hidden\" id=\"form_filed\" name=\"form_filed\" value=\"FALSE\">\n";
	if ($application_ids_count == 1) {
		$form_string .= sprintf("<INPUT type=\"hidden\" id=\"form_application_id\" name=\"form_application_id\" value=\"%s\">\n", $application_ids[0]['id']);
	}
	$form_string .= "<INPUT type=\"hidden\" id=\"form_submit_it\" name=\"form_submit_it\" value=\"who cares\">\n";
	$form_string .= "<INPUT type=\"hidden\" id=\"form_submit_it\" name=\"form_oa_status\" value=\"Not Started\">\n";
	$form_string .= "<TABLE> <!-- first table -->\n";

	$form_string .= "<TR class=\"dd_bold\">\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">Create New Office Action</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Client:</TD>";
	$form_string .= "<TD align=\"left\">" . $client_val . "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Matter:</TD>";
	$form_string .= "<TD align=\"left\">" . $matter_val . "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">OA Applicaiton Type:</TD>";
	$form_string .= "<TD align=\"left\">";

	$oa_type_array = dd_get_oa_type_array();
	$form_string .= dd_form_dropdown("form_oa_type", $oa_type_array);
	$form_string .= "</TD>";
	$form_string .= "</TR>\n";

	if ($application_ids_count > 1) {
		$application_type_array = dd_get_type_array();
		$form_string .= "<TR>\n";
		$form_string .= "<TD align=\"right\">Select Applicaiton:</TD>";
		$form_string .= "<TD align=\"left\">";

		foreach ($application_ids as $row) {
			/* html/form value ,/= what is shown in dropdown */
			$applications_dropdown_array[$row['id']] = $application_type_array[$row['application_type_id']] . " " . $row['application_no'];
		}
		$form_string .= dd_form_dropdown("form_application_id", $applications_dropdown_array);
		$form_string .= "</TD>";
		$form_string .= "</TR>\n";
	}

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">(If unknown, leave blank)</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	if (in_array("oa_date", $coa_main_required_array)) {
		$form_string .= "<TD align=\"right\">Office Action date (YYYY-MM-DD) <SPAN class=\"blink\">Required:</SPAN></TD>";
	} else {
		$form_string .= "<TD align=\"right\">Office Action date (YYYY-MM-DD):</TD>";
	}

	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_date_OR_dropdown_date("form_oa_date", 10, 10);
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">File by date (YYYY-MM-DD):</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_date_OR_dropdown_date("form_file_by_date", 10, 10);
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Due to client date (YYYY-MM-DD):</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_date_OR_dropdown_date("form_due_to_client_date", 10, 10);
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Next action due date (YYYY-MM-DD):</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_date_OR_dropdown_date("form_next_action_due", 10, 10);
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Filing date (YYYY-MM-DD):</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_date_OR_dropdown_date("form_filing_date", 10, 10);
	$form_string .= "</TD>\n";


	$form_string .= "</TR>\n";
	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Total budget (in dollars):</TD>";
	$form_string .= "<TD align=\"left\"><INPUT type=\"text\" name=\"form_total_budget\" size=\"10\"></TD>\n";
	$form_string .= "</TR>\n";


	/* SUBMIT */

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">";
	$form_string .= "<BR><INPUT TYPE=\"submit\" VALUE=\"Create New Office Action\">";
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";
	$form_string .= "</TABLE> <!-- first table -->\n";
	$form_string .= "</FORM>\n";

//	$html_string .= $form_string;
	return $form_string;
} /* coa_main_form */

if (!empty($_POST['form_client']) && !empty($_POST['form_matter']) && !empty($_POST['form_submit_it'])) {
	global $dd_db_handle;
	$column_nvps = array();
	$all_columns = array("client", "matter", "application_id", "oa_date", "due_to_client_date",
		"file_by_date", "hard_date", "filing_date", "oa_type", "reply_type", "total_budget",
		"oa_status", "filed");
	$date_columns = array("oa_date", "due_to_client_date", "file_by_date", "hard_date",
		"filing_date");
	$date_suffixes = array("_year", "_month", "_day");
	
	foreach ($all_columns as $column) {
		if (!empty($_POST["form_" . $column])) {
			$column_nvps[$column] = $_POST["form_" . $column];
		} else {
			$column_nvps[$column] = "";
		}
	}

	foreach ($date_columns as $date_column) {
		if (!empty($column_nvps[$date_column])) {
			continue;
		}
//		$html_string .= "foodoo: " . $date_column . "<BR>\n";
		$date_drop_downs_utilized = TRUE;
		foreach ($date_suffixes as $date_suffix) {
			$tmp = "form_" . $date_column . $date_suffix;
			if (empty($_POST[$tmp]) || !strcmp("NONE", $_POST[$tmp])) {
				$date_drop_downs_utilized = FALSE;
				break;
			}
		}

		if ($date_drop_downs_utilized) {
			$column_nvps[$date_column] = 
				$_POST["form_" . $date_column . "_year"] . "-" . 
				$_POST["form_" . $date_column . "_month"] . "-" . 
				$_POST["form_" . $date_column . "_day"];
		}
	}
		
	$sql_stmt = "INSERT INTO office_action (";
	$first_column = TRUE;
	foreach ($column_nvps as $key => $value) {
		if (empty($value)) {
			continue;
		}
		if (!$first_column) {
			$sql_stmt .= ", ";
		} else {
			$first_column = FALSE;
		}
		$sql_stmt .= $key;
	}
	$sql_stmt .= ") VALUES (";

	$first_column = TRUE;
	foreach ($column_nvps as $key => $value) {
		if (empty($value)) {
			continue;
		}
		if (!$first_column) {
			$sql_stmt .= ", ";
		} else {
			$first_column = FALSE;
		}
		$sql_stmt .= ":" . $key . "_binder";
	}
	$sql_stmt .= ")";

//	$html_string .= $sql_stmt . "\n<BR>\n";

	foreach ($all_columns as $column) {
		$column_pdos[$column] = PDO::PARAM_STR;
	}
	$column_pdos["application_id"] = PDO::PARAM_INT;
	$column_pdos["total_budget"] = PDO::PARAM_INT;

	$tmp_results = $dd_db_handle->prepare($sql_stmt);

	foreach ($column_nvps as $key => $value) {
		if (!empty($value)) {
			$tmp_results->bindValue(":" . $key . "_binder", $value, $column_pdos[$key]);
		}
	}

/*
	foreach ($_POST as $key => $value) {
		$html_string .= sprintf("'%s' = '%s'<BR>\n", $key, $value);
	}
*/

	if (empty($column_nvps['oa_date'])) {
		$html_string .= coa_main_form(array("oa_date"));
	} else {
		$exec_bool = $tmp_results->execute();
		if ($exec_bool) {
			$html_string .= "INSERT SUCESS\n";
		} else {
			$html_string .= "INSERT FAILURE\n";
		}
	}
} else if (!empty($_POST['form_client']) && !empty($_POST['form_matter'])) {
	$html_string .= coa_main_form(array());
} else if (!empty($_POST["form_client"])) {
	$form_string = "";
	$form_string .= dd_form_start("create_office_action", "POST");
	$form_string .= "<TABLE>\n";
	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">Create New Office Action</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Select Matter:</TD>";
	$form_string .= "<TD align=\"left\">";

	$matter_array = dd_get_matter_array($client_val);
	$form_string .= dd_form_dropdown("form_matter", $matter_array);
	$form_string .= sprintf("<INPUT type=\"hidden\" id=\"form_client\" name=\"form_client\" value=\"%s\">", $client_val);
	$form_string .= "</TD>";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">";
	$form_string .= "<INPUT TYPE=\"submit\" VALUE=\"Next\">";
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";
	$form_string .= "</TABLE>\n";
	$form_string .= "</FORM>\n";

	$html_string .= $form_string;
} else {
	$form_string = "";
	$form_string .= dd_form_start("create_office_action", "POST");
	$form_string .= "<TABLE>\n";
	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">Create New Office Action</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";

	$form_string .= "<TD align=\"right\">Select Client:</TD>";
	$form_string .= "<TD align=\"left\">";

	$client_array = dd_get_client_array();
	$form_string .= dd_form_dropdown("form_client", $client_array);
	$form_string .= "</TD>";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">";
	$form_string .= "<INPUT TYPE=\"submit\" VALUE=\"Next\">";
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";
	$form_string .= "</TABLE>\n";
	$form_string .= "</FORM>\n";

	$html_string .= $form_string;
}

$html_string .= "</CENTER>\n";

/*
foreach ($_POST as $key => $value) {
	$html_string .= sprintf("_POST key='%s' value='%s'<BR>\n", $key, $value);
}

if (!empty($column_nvps)) {
	foreach ($column_nvps as $key => $value) {
		$html_string .= sprintf("column_nvps key='%s' value='%s'<BR>\n", $key, $value);
	}
}
*/

$html_string .= dd_get_trailer();

$html_string .= "</BODY>\n";
$html_string .= "</HTML>\n";

echo $html_string;

/*
 * End of file:	create_office_action.php
 */
?>
