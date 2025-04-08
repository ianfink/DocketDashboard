<?
/**
 * @author		Ian M. Fink
 *
 * @file		update_application.php
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

$html_string .= "<HTML>\n";
$html_string .= "<HEAD>\n";
$html_string .= dd_get_style();
$html_string .= "<TITLE>Update Application</TITLE>\n";
$html_string .= "</HEAD>\n";
$html_string .= dd_get_body_tag();
$html_string .= dd_get_leader();

$html_string .= "<CENTER>\n";

$application_val = $_GET['form_id'];
$client_val = $_POST['form_client'];
$matter_val = $_POST['form_matter'];

if (!empty($_POST['form_client']) && !empty($_POST['form_matter']) && !empty($_POST['form_id'])) {
	global $dd_db_handle;
	$columns_utilized_nvps = array();
	$all_columns = array("id", "client", "matter", "application_type_id", "description",
		"client_matter_title", "application_title", "file_by_date", "filing_date", "bar_date",
		"next_action_due", "total_budget", "next_action_id", "application_assigned_to",
		"pat_app_pub_no", "pat_no", "application_no", "country_id", "client_matter_id");
	$date_columns = array("file_by_date", "filing_date", "bar_date", "next_action_due");
	$date_suffixes = array("_year", "_month", "_day");
	$int_columns = array('id', 'total_budget', 'next_action_id');
	$set_updates = array();
	
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
// NEED TO USER UPDATE NOT INSERT

	$sql_stmt = "UPDATE application ";
	$first_column = TRUE;
	foreach ($column_nvps as $key => $value) {
		if (empty($value)) {
			continue;
		}
		if (!strcmp("id", $key)) {
			continue;
		}
		$tmp_binder = ":" . $key . "_binder";
		$tmp_stmt = $key . " = " . $tmp_binder;
		array_push($set_updates, $tmp_stmt);
	}

	$sql_stmt .= " SET ";
	$sql_stmt .= implode(", ", $set_updates);
	$sql_stmt .= " WHERE id = :id_binder";

	$tmp_results = $dd_db_handle->prepare($sql_stmt);

	foreach ($column_nvps as $key => $value) {
		if (empty($value)) {
			continue;
		}
		$tmp_binder = ":" . $key . "_binder";
		if (in_array($key, $int_columns)) {
			$tmp_results->bindValue($tmp_binder, $value, PDO::PARAM_INT);
		} else {
			$tmp_results->bindValue($tmp_binder, $value, PDO::PARAM_STR);
		}
	}

	$html_string .= $sql_stmt . "\n<BR>\n";

	$exec_bool = $tmp_results->execute();

	if ($exec_bool) {
		$html_string .= "UPDATE SUCESS\n";
	} else {
		$html_string .= "UPDATE FAILURE\n";
	}
// } else if (!empty($_POST['form_client']) && !empty($_POST['form_matter'])) {
} else if (!empty($_GET['form_id'])) {
	$query_string = "SELECT * FROM application WHERE id = :id_binder";
	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->bindValue(":id_binder", $_GET['form_id'], PDO::PARAM_INT);
	$exec_bool = $tmp_results->execute();
	/* check to see if user can access the client (at some point) */
	$rows = $tmp_results->fetchAll();
	$row = $rows[0];

	
	$form_string = "";
	$form_string .= dd_form_start("update_application", "POST");
	$form_string .= sprintf("<INPUT type=\"hidden\" id=\"form_client\" name=\"form_client\" value=\"%s\">", $row['client']);
	$form_string .= sprintf("<INPUT type=\"hidden\" id=\"form_matter\" name=\"form_matter\" value=\"%s\">", $row['matter']);
	$form_string .= sprintf("<INPUT type=\"hidden\" id=\"form_id\" name=\"form_id\" value=\"%s\">", $row['id']);
	$form_string .= "<TABLE> <!-- first table -->\n";

	$form_string .= "<TR class=\"dd_bold\">\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">Update Application</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Client:</TD>";
	$form_string .= "<TD align=\"left\">" . $row['client'] . "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Matter:</TD>";
	$form_string .= "<TD align=\"left\">" . $row['matter'] . "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Select Applicaiton Type:</TD>";
	$form_string .= "<TD align=\"left\">";

	$application_type_array = dd_get_application_type_array();
	$form_string .= dd_form_dropdown_selected("form_application_type_id", $application_type_array,
		$row['application_type_id']);
	$form_string .= "</TD>";
	$form_string .= "</TR>\n";

/*
	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">(If unknown, leave blank)</TD>\n";
	$form_string .= "</TR>\n";
*/

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Client's Application ID:</TD>";
	if (empty($row['client_matter_id'])) {
		$form_string .= "<TD align=\"left\"><INPUT type=\"text\" name=\"form_client_matter_id\" size=\"80\"></TD>\n";
	} else {
		$form_string .= sprintf("<TD align=\"left\"><INPUT type=\"text\" name=\"form_client_matter_id\" size=\"80\" value=\"%s\"></TD>\n", $row['client_matter_id']);
	}
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Client's title:</TD>";
	if (empty($row['client_matter_title'])) {
		$form_string .= "<TD align=\"left\"><INPUT type=\"text\" name=\"form_client_matter_title\" size=\"80\"></TD>\n";
	} else {
		$form_string .= sprintf("<TD align=\"left\"><INPUT type=\"text\" name=\"form_client_matter_title\" size=\"80\" value=\"%s\"></TD>\n", $row['client_matter_title']);
	}
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Application's title:</TD>";
	if (empty($row['application_title'])) {
		$form_string .= "<TD align=\"left\"><INPUT type=\"text\" name=\"form_application_title\" size=\"80\"></TD>\n";
	} else {
		$form_string .= sprintf("<TD align=\"left\"><INPUT type=\"text\" name=\"form_application_title\" size=\"80\" value=\"%s\"></TD>\n", $row['application_title']);
	}
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">File by date (YYYY-MM-DD):</TD>";
	$form_string .= "<TD align=\"left\">";
	if (empty($row['file_by_date'])) {
		$form_string .= dd_date_OR_dropdown_date("form_file_by_date", 10, 10);
	} else {
		$form_string .= dd_date_OR_dropdown_date_with_default("form_file_by_date", 10, 10, $row['file_by_date']);
	}
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Bar by date (YYYY-MM-DD):</TD>";
	$form_string .= "<TD align=\"left\">";
	if (empty($row['bar_date'])) {
		$form_string .= dd_date_OR_dropdown_date("form_bar_date", 10, 10);
	} else {
		$form_string .= dd_date_OR_dropdown_date_with_default("bar_date", 10, 10, $row['bar_date']);
	}
	$form_string .= "</TD>\n";


/*$form_string .= "<TD align=\"left\"><INPUT type=\"text\" name=\"form_bar_date\" size=\"80\"></TD>\n"; */
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Next action due date (YYYY-MM-DD):</TD>";
	$form_string .= "<TD align=\"left\">";
	if (empty($row['next_action_due'])) {
		$form_string .= dd_date_OR_dropdown_date("form_next_action_due", 10, 10);
	} else {
		$form_string .= dd_date_OR_dropdown_date_with_default("next_action_due", 10, 10, $row['next_action_due']);
	}
	$form_string .= "</TD>\n";


/*	$form_string .= "<TD align=\"left\"><INPUT type=\"text\" name=\"form_next_action_due\" size=\"80\"></TD>\n"; */
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Filing date (YYYY-MM-DD):</TD>";
	$form_string .= "<TD align=\"left\">";
	if (empty($row['filing_date'])) {
		$form_string .= dd_date_OR_dropdown_date("form_filing_date", 10, 10);
	} else {
		$form_string .= dd_date_OR_dropdown_date_with_default("form_filing_date", 10, 10, $row['filing_date']);
	}
	$form_string .= "</TD>\n";


/*	$form_string .= "<TD align=\"left\"><INPUT type=\"text\" name=\"form_next_action_due\" size=\"80\"></TD>\n"; */
	$form_string .= "</TR>\n";
	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Total budget (in dollars):</TD>";
	$form_string .= "<TD align=\"left\">";
	if (empty($row['total_budget'])) {
		$form_string .= "<INPUT type=\"text\" name=\"form_total_budget\" size=\"80\">";
	} else {
		$form_string .= sprintf("<INPUT type=\"text\" name=\"form_total_budget\" size=\"80\" value=\"%s\">", $row['total_budget']);
	}
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Assigned to (atty/agent/advisor):</TD>";
	$form_string .= "<TD align=\"left\">";
	if (empty($row['application_assigned_to'])) {
		$form_string .= dd_form_dropdown("form_application_assigned_to", dd_get_users_for_dropdown());
	} else {
		$form_string .= dd_form_dropdown_selected("form_application_assigned_to", dd_get_users_for_dropdown(), $row['application_assigned_to']);
	}
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Country:</TD>";
	$form_string .= "<TD align=\"left\">";
	if (empty($row['application_assigned_to'])) {
		$form_string .= dd_form_dropdown("form_country_id", dd_get_country_ids());
	} else {
		$form_string .= dd_form_dropdown_selected("form_country_id", dd_get_country_ids(), $row['application_assigned_to']);
	}
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Patent Application No.:</TD>";
	$form_string .= "<TD align=\"left\">";
	if (empty($row['application_no'])) {
		$form_string .= "<INPUT type=\"text\" name=\"form_application_no\" size=\"80\">";
	} else {
		$form_string .= sprintf("<INPUT type=\"text\" name=\"form_application_no\" size=\"80\" value=\"%s\">", $row['application_no']);
	}
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Patent Application Publication No.:</TD>";
	$form_string .= "<TD align=\"left\">";
	if (empty($row['pat_app_pub_no'])) {
		$form_string .= "<INPUT type=\"text\" name=\"form_pat_app_pub_no\" size=\"80\">";
	} else {
		$form_string .= sprintf("<INPUT type=\"text\" name=\"form_pat_app_pub_no\" size=\"80\" value=\"%s\">", $row['pat_app_pub_no']);
	}
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Patent No.:</TD>";
	$form_string .= "<TD align=\"left\">";
	if (empty($row['pat_no'])) {
		$form_string .= "<INPUT type=\"text\" name=\"form_pat_no\" size=\"80\">";
	} else {
		$form_string .= sprintf("<INPUT type=\"text\" name=\"form_pat_no\" size=\"80\" value=\"%s\">", $row['pat_no']);
	}
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";


	/* SUBMIT */

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">";
	$form_string .= "<BR><INPUT TYPE=\"submit\" VALUE=\"Update Application\">";
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
 * End of file:	update_application.php
 */
?>
