<?
/**
 * @author		Ian M. Fink
 *
 * @file		create_application.php
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
$html_string .= "<TITLE>Create Application</TITLE>\n";
$html_string .= "</HEAD>\n";
$html_string .= dd_get_body_tag();
$html_string .= dd_get_leader();

$html_string .= "<CENTER>\n";

$client_val = $_POST['form_client'];
$matter_val = $_POST['form_matter'];

if (!empty($_POST['form_client']) && !empty($_POST['form_matter']) && !empty($_POST['form_client_matter_title'])) {
	global $dd_db_handle;
	$columns_utilized_nvps = array();
	$all_columns = array("client", "matter", "application_type_id", "description",
		"client_matter_title", "application_title", "file_by_date", "filing_date", "bar_date",
		"next_action_due", "total_budget", "next_action_id", "application_assigned_to",
		"pat_app_pub_no", "pat_no", "application_no", "country_id", "client_matter_id",
		"abandoned");
	$date_columns = array("file_by_date", "filing_date", "bar_date", "next_action_due");
	$date_suffixes = array("_year", "_month", "_day");
	
	foreach ($all_columns as $column) {
		if (!empty($_POST["form_" . $column])) {
			$column_nvps[$column] = $_POST["form_" . $column];
		} else {
			$column_nvps[$column] = "";
		}
	}

//	$columns_utilized = dd_columns_utilized($all_columns);

	foreach ($date_columns as $date_column) {
		if (!empty($column_nvps[$date_column])) {
			continue;
		}
		$html_string .= "foodoo: " . $date_column . "<BR>\n";
		$date_drop_downs_utilized = TRUE;
		foreach ($date_suffixes as $date_suffix) {
			$tmp = "form_" . $date_column . $date_suffix;
			if (empty($_POST[$tmp]) || !strcmp("NONE", $_POST[$tmp])) {
				$date_drop_downs_utilized = FALSE;
				break;
			}
		}

		if ($date_drop_downs_utilized) {
//			$_POST["form_" . $date_column] =
			$column_nvps[$date_column] = 
				$_POST["form_" . $date_column . "_year"] . "-" . 
				$_POST["form_" . $date_column . "_month"] . "-" . 
				$_POST["form_" . $date_column . "_day"];
//			array_push($columns_utilized, $date_column);
		}
	}
		
//	$sql_stmt = "INSERT INTO application (" . implode(", ", $columns_utilized) . ") VALUES (";
	$sql_stmt = "INSERT INTO application (";
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

	$html_string .= $sql_stmt . "\n<BR>\n";

	foreach ($all_columns as $column) {
		$column_pdos[$column] = PDO::PARAM_STR;
	}
	$column_pdos["application_type_id"] = PDO::PARAM_INT;
	$column_pdos["total_budget"] = PDO::PARAM_INT;
	$column_pdos["next_action_id"] = PDO::PARAM_INT;

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

	$exec_bool = $tmp_results->execute();

	if ($exec_bool) {
		$html_string .= "INSERT SUCESS\n";
	} else {
		$html_string .= "INSERT FAILURE\n";
	}
} else if (!empty($_POST['form_client']) && !empty($_POST['form_matter'])) {
	$form_string = "";
	$form_string .= dd_form_start("create_application", "POST");
	$form_string .= sprintf("<INPUT type=\"hidden\" id=\"form_client\" name=\"form_client\" value=\"%s\">", $client_val);
	$form_string .= sprintf("<INPUT type=\"hidden\" id=\"form_matter\" name=\"form_matter\" value=\"%s\">", $matter_val);

	$form_string .= "<INPUT type=\"hidden\" id=\"form_abandoned\" name=\"form_abandoned\" value=\"FALSE\">";
	$form_string .= "<TABLE> <!-- first table -->\n";

	$form_string .= "<TR class=\"dd_bold\">\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">Create New Application</TD>\n";
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
	$form_string .= "<TD align=\"right\">Select Applicaiton Type:</TD>";
	$form_string .= "<TD align=\"left\">";

	$application_type_array = dd_get_application_type_array();
	$form_string .= dd_form_dropdown("form_application_type_id", $application_type_array);
	$form_string .= "</TD>";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">(If unknown, leave blank)</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Client's Application ID:</TD>";
	$form_string .= "<TD align=\"left\"><INPUT type=\"text\" name=\"form_client_matter_id\" size=\"80\"></TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Client's title:</TD>";
	$form_string .= "<TD align=\"left\"><INPUT type=\"text\" name=\"form_client_matter_title\" size=\"80\"></TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Application's title:</TD>";
	$form_string .= "<TD align=\"left\"><INPUT type=\"text\" name=\"form_application_title\" size=\"80\"></TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">File by date (YYYY-MM-DD):</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_date_OR_dropdown_date("form_file_by_date", 10, 10);
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Bar by date (YYYY-MM-DD):</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_date_OR_dropdown_date("form_bar_date", 10, 10);
	$form_string .= "</TD>\n";


/*$form_string .= "<TD align=\"left\"><INPUT type=\"text\" name=\"form_bar_date\" size=\"80\"></TD>\n"; */
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Next action due date (YYYY-MM-DD):</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_date_OR_dropdown_date("next_action_due", 10, 10);
	$form_string .= "</TD>\n";


/*	$form_string .= "<TD align=\"left\"><INPUT type=\"text\" name=\"form_next_action_due\" size=\"80\"></TD>\n"; */
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Filing date (YYYY-MM-DD):</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_date_OR_dropdown_date("form_filing_date", 10, 10);
	$form_string .= "</TD>\n";


/*	$form_string .= "<TD align=\"left\"><INPUT type=\"text\" name=\"form_next_action_due\" size=\"80\"></TD>\n"; */
	$form_string .= "</TR>\n";
	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Total budget (in dollars):</TD>";
	$form_string .= "<TD align=\"left\"><INPUT type=\"text\" name=\"form_total_budget\" size=\"80\"></TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Assigned to (atty/agent/advisor):</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_form_dropdown("form_application_assigned_to", dd_get_users_for_dropdown());
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Country:</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_form_dropdown("form_country_id", dd_get_country_ids());
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Patent Application No.:</TD>";
	$form_string .= "<TD align=\"left\"><INPUT type=\"text\" name=\"form_application_no\" size=\"80\"></TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Patent Application Publication No.:</TD>";
	$form_string .= "<TD align=\"left\"><INPUT type=\"text\" name=\"form_pat_app_pub_no\" size=\"80\"></TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Patent No.:</TD>";
	$form_string .= "<TD align=\"left\"><INPUT type=\"text\" name=\"form_pat_no\" size=\"80\"></TD>\n";
	$form_string .= "</TR>\n";


	/* SUBMIT */

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">";
	$form_string .= "<BR><INPUT TYPE=\"submit\" VALUE=\"Create New Application\">";
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";
	$form_string .= "</TABLE> <!-- first table -->\n";
	$form_string .= "</FORM>\n";

	$html_string .= $form_string;

} else if (!empty($_POST["form_client"])) {
	$form_string = "";
	$form_string .= dd_form_start("create_application", "POST");
	$form_string .= "<TABLE>\n";
	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">Create New Application</TD>\n";
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
	$form_string .= dd_form_start("create_application", "POST");
	$form_string .= "<TABLE>\n";
	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">Create New Application</TD>\n";
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

$html_string .= dd_get_trailer();

$html_string .= "</BODY>\n";
$html_string .= "</HTML>\n";

echo $html_string;

/*
 * End of file:	create_application.php
 */
?>
