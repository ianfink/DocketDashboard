<?
/**
 * @author		Ian M. Fink
 *
 * @file		update_office_action.php
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
$html_string .= "<SCRIPT>\n";
$html_string .= dd_get_js_file("assign_today.js");
$html_string .= "\n</SCRIPT>\n";
$html_string .= "<TITLE>Update Office Action</TITLE>\n";
$html_string .= "</HEAD>\n";
$html_string .= dd_get_body_tag();
$html_string .= dd_get_leader();

$html_string .= "<CENTER>\n";

$client_val = $_POST['form_client'];
$matter_val = $_POST['form_matter'];
$id_val = $_REQUEST['form_id'];

function
uoa_main_form($coa_main_required_array)
{
	global $dd_db_handle;
	global	$client_val;
	global	$matter_val;
	global	$id_val;
	$query_string = "";

	$query_string .= "SELECT * FROM office_action WHERE id = :id_binder";
	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->bindValue(":id_binder", $id_val,  PDO::PARAM_INT);
	$exec_bool = $tmp_results->execute();


	if (!$exec_bool) {
		/* do something */
	}

	$rows = $tmp_results->fetchAll();
	$OA_row = $rows[0];

	$query_string = "";
	$query_string .= "SELECT * FROM application WHERE id = :id_binder";
	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->bindValue(":id_binder", $OA_row['application_id'],  PDO::PARAM_INT);
	$exec_bool = $tmp_results->execute();

	if (!$exec_bool) {
		/* do something */
	}

	$rows = $tmp_results->fetchAll();
	$app_row = $rows[0];

	$form_string = "";
	$form_string .= dd_form_start("update_office_action", "POST");
	$form_string .= sprintf("<INPUT type=\"hidden\" name=\"form_id\" value=\"%s\">\n", $id_val);
	$form_string .= "<INPUT type=\"hidden\" name=\"form_submit_it\" value=\"who cares\">\n";
	$form_string .= "<INPUT type=\"hidden\" name=\"form_application_id\" value=\"" . $OA_row['application_id'] . "\">\n";
	$form_string .= "<TABLE> <!-- first table -->\n";

	$form_string .= "<TR class=\"dd_bold\">\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">Update Office Action</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Client:</TD>";
	$form_string .= "<TD align=\"left\">" . $OA_row['client'] . "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Matter:</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_anchor($OA_row['matter'], sprintf("find_application?client=%s&matter=%s", urlencode($OA_row['client']),
		urlencode($OA_row['matter'])));
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">OA Rejection Type:</TD>";
	$form_string .= "<TD align=\"left\">";

	$oa_type_array = dd_get_oa_type_array();
	$form_string .= dd_form_dropdown_selected("form_oa_type", $oa_type_array, $OA_row['oa_type']);
	$form_string .= "</TD>";
	$form_string .= "</TR>\n";

//OA REPLY TYPE
	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">OA Reply Type:</TD>";
	$form_string .= "<TD align=\"left\">";

	$reply_type_array = dd_get_reply_type_array();
	$form_string .= dd_form_dropdown_selected("form_reply_type", $reply_type_array, $OA_row['reply_type']);
	$form_string .= "</TD>";
	$form_string .= "</TR>\n";

// application_assigned_to
	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Assigned to:</TD>";
	$form_string .= "<TD align=\"left\">";
//	$form_string .= $app_row['application_assigned_to'];
	$user_array = dd_get_user_array();
	$form_string .= dd_form_dropdown_selected("form_application_assigned_to", $user_array,
		$app_row['application_assigned_to']);

	$form_string .= "</TD>";
	$form_string .= "</TR>\n";

/*
	if ($application_ids_count > 1) {
		$application_type_array = dd_get_type_array();
		$form_string .= "<TR>\n";
		$form_string .= "<TD align=\"right\">Select Applicaiton:</TD>";
		$form_string .= "<TD align=\"left\">";

		foreach ($application_ids as $row) {
			// html/form value ,/= what is shown in dropdown
			$applications_dropdown_array[$row['id']] = $application_type_array[$row['application_type_id']] . " " . $row['application_no'];
		}
		$form_string .= dd_form_dropdown("form_application_id", $applications_dropdown_array);
		$form_string .= "</TD>";
		$form_string .= "</TR>\n";
	}
*/
	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Application No:</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_formatted_application_no($app_row['application_no'], "US");
	$form_string .= "</TD>";
	$form_string .= "</TR>\n";

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
	$form_string .= dd_date_OR_dropdown_date_with_default_and_DOM_id("oa_date", 10, 10, $OA_row['oa_date'], "id_oa_date_id");
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">File by date (YYYY-MM-DD):</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_date_OR_dropdown_date_with_default_and_DOM_id("file_by_date", 10, 10, $OA_row['file_by_date'], "id_file_by_date_id");
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Due to client date (YYYY-MM-DD):</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_date_OR_dropdown_date_with_default_and_DOM_id("due_to_client_date", 10, 10, $OA_row['due_to_client_date'], "id_due_to_client_date_id");
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Next action due date (YYYY-MM-DD):</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_date_OR_dropdown_date_with_default_and_DOM_id("hard_date", 10, 10, $OA_row['hard_date'], "id_hard_date_id");
//	$form_string .= dd_date_OR_dropdown_date_with_default_and_DOM_id("next_action_due", 10, 10, $OA_row['next_action_due'], "id_next_action_due_id");
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Filing date (YYYY-MM-DD):</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_date_OR_dropdown_date_with_default_and_DOM_id("filing_date", 10, 10, $OA_row['filing_date'], "id_filing_date_id");
	$form_string .= "</TD>\n";


	$form_string .= "</TR>\n";
	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Total budget (in dollars):</TD>";
	$form_string .= sprintf("<TD align=\"left\"><INPUT type=\"text\" name=\"form_total_budget\" size=\"10\" value=\"%s\"></TD>\n",
		$OA_row['total_budget']);
	$form_string .= "</TR>\n";


	/* SUBMIT */

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">";
	$form_string .= "<BR><INPUT TYPE=\"submit\" VALUE=\"Update Office Action\">";
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";
	$form_string .= "</TABLE> <!-- first table -->\n";
	$form_string .= "</FORM>\n";

//	$html_string .= $form_string;
	return $form_string;
} /* uoa_main_form */

if (!empty($_POST['form_submit_it']) && !empty($_POST['form_id'])) {
	global $dd_db_handle;
	$column_nvps = array();
	$all_columns = array("id", "client", "matter", "application_id", "oa_date", "due_to_client_date",
		"file_by_date", "hard_date", "filing_date", "oa_type", "reply_type", "total_budget",
		"oa_status");
	$date_columns = array("oa_date", "due_to_client_date", "file_by_date", "hard_date",
		"filing_date");
	$date_suffixes = array("_year", "_month", "_day");
	$set_updates = array();
	$int_columns = array('id', 'application_id', 'total_budget');
	
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
		
//	$sql_stmt = "INSERT INTO office_action (";
// NEED UPDATE

	$sql_stmt = "UPDATE office_action SET ";
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

	if (!empty($_REQUEST['form_application_assigned_to'])) {
		$form_application_assigned_to = $_REQUEST['form_application_assigned_to'];
		$app_id = $_REQUEST['form_application_id'];

		$html_string .= "\n<BR>\n";
		$html_string .= "app_id = " . $app_id;
		$html_string .= "\n<BR>\n";
		$application_row = dd_get_application_row($dd_db_handle, $app_id);
		$html_string .= sprintf("matter = '%s' client = '%s'", $application_row['matter'],
			$application_row['client']);
		$html_string .= "\n<BR>\n";
		if (strcmp($application_row['application_assigned_to'], $form_application_assigned_to) != 0) {
			$assignment_result = dd_reassign_application($dd_db_handle, $app_id,
				$form_application_assigned_to, $application_row);
/*
			$sql_stmt = "UPDATE application SET application_assigned_to = ";
			$sql_stmt .= " :application_assigned_to_binder ";
			$sql_stmt .= " WHERE id = " . $_REQUEST['form_application_id'];
			$tmp_results = $dd_db_handle->prepare($sql_stmt);
			$tmp_results->bindValue(':application_assigned_to_binder',
				$form_application_assigned_to, PDO::PARAM_STR);
			$html_string .= $sql_stmt . "\n<BR>\n";
			$html_string .= "\n<BR>\n";
			$html_string .= $form_application_assigned_to . "\n<BR>\n";

			$exec_bool = $tmp_results->execute();
*/
			if (strcmp($assignment_result, "SUCCESS") == 0) {
				$html_string .= "Reassignment UPDATE SUCESS\n";
			} else {
				$html_string .= "Reassignment UPDATE FAILURE: " . $assignment_result . "\n";
			}
		}
		$html_string .= $sql_stmt . "\n<BR>\n";
	}
} else if (!empty($_GET['form_id'])) {
	$html_string .= "<!-- HERE THERE -->\n";
	$html_string .= uoa_main_form(array());
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
 * End of file:	update_office_action.php
 */
?>
