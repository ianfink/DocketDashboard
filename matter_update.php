<?
/*
 * Copyright (C) Ian M. Fink.
 * All rights reserved.
 * This work is unpublished.
 */

$dd_php_files = $_ENV['PHP_INCLUDE_PATH'];
$html_string = "";

include $dd_php_files . "/dd_funcs.php";
include dd_get_body_file();
include dd_get_leader_file();
include dd_get_style_file();
include dd_get_trailer_file();

if (!dd_init()) {
	exit(0);
}

$web_page = new web_page_class("Matter Update", $dd_php_files);

$web_page->add_style(dd_get_style());
$web_page->set_body_tag(dd_get_body_tag());
$web_page->add_leader(dd_get_leader());
$web_page->add_trailer(dd_get_trailer());

$what_to_do = "";


if (!empty($_REQUEST['form_oa_id']) && !empty($_REQUEST['form_update'])) {
	$what_to_do = "update_oa";
} else if (!empty($_REQUEST['form_oa_id']) && !empty($_REQUEST['form_submit_it'])) {
	$what_to_do = "process_oa_update";
} else if (!empty($_REQUEST['form_client']) && !empty($_REQUEST['form_matter']) && !empty($_REQUEST['form_oa_id'])) {
	$what_to_do = "default_oa";
} else if (!empty($_REQUEST['form_application_id']) && !empty($_REQUEST['form_submit_it'])) {
	$what_to_do = "process_application_update";
} else if (!empty($_REQUEST['form_client']) && !empty($_REQUEST['form_matter']) && !empty($_REQUEST['form_application_id'])) {
	$what_to_do = "default_application";
}

$html_string = "";

if (!strcmp("update_oa", $what_to_do)) {
	$html_string .= dd_update_matter();
} else if (!strcmp("process_oa_update", $what_to_do)) {
	$ret_val = dd_process_update_matter_oa_form($_REQUEST['form_oa_id']);
	if (strcmp($ret_val, "SUCCESS") != 0) {
		$html_string .= "ERROR: " . $ret_val;
	}
} else if (!strcmp("process_application_update", $what_to_do)) {
	$ret_val = dd_process_update_matter_application_status_form($_REQUEST['form_application_id']);
	if (strcmp($ret_val, "SUCCESS") != 0) {
		$html_string .= "ERROR: " . $ret_val;
	}
} else if (!strcmp("default_oa", $what_to_do)) {
	$rows = dd_get_all_updates($_REQUEST['form_client'], $_REQUEST['form_matter']);
	if (count($rows) > 0) {
		$html_string .= "<TABLE>\n";
		$html_string .= "\t<TR class=\"dd_tr_header\">";
		$html_string .= "<TD>Updated by</TD><TD>Timestamp</TD><TD>Update</TD>";
		$html_string .= "</TR>\n";
		$is_odd = TRUE;
		foreach ($rows as $row) {
			$html_string .= sprintf("\t<TR class=\"%s\">", $is_odd ? "dd_tr_odd" : "dd_tr_even");
			$is_odd = !$is_odd;
			$html_string .= "<TD>" . $row['username'] . "</TD>";
			$html_string .= "<TD>" . $row['the_update_timestamp'] . "</TD>";
			$html_string .= "<TD>" . $row['the_update'] . "</TD>";
			$html_string .= "</TR>\n";
		}
		$html_string .= "</TABLE>\n";
		$html_string .= "<BR>\n";
	}
	$html_string .= dd_update_matter_oa_form($_REQUEST['form_client'], $_REQUEST['form_matter'],
		$_REQUEST['form_oa_id']);
} else if (!strcmp("default_application", $what_to_do)) {
	$rows = dd_get_all_updates($_REQUEST['form_client'], $_REQUEST['form_matter']);
	if (count($rows) > 0) {
		$html_string .= "<TABLE>\n";
		$html_string .= "\t<TR class=\"dd_tr_header\">";
		$html_string .= "<TD>Updated by</TD><TD>Timestamp</TD><TD>Update</TD>";
		$html_string .= "</TR>\n";
		$is_odd = TRUE;
		foreach ($rows as $row) {
			$html_string .= sprintf("\t<TR class=\"%s\">", $is_odd ? "dd_tr_odd" : "dd_tr_even");
			$is_odd = !$is_odd;
			$html_string .= "<TD>" . $row['username'] . "</TD>";
			$html_string .= "<TD>" . $row['the_update_timestamp'] . "</TD>";
			$html_string .= "<TD>" . $row['the_update'] . "</TD>";
			$html_string .= "</TR>\n";
		}
		$html_string .= "</TABLE>\n";
		$html_string .= "<BR>\n";
	}
	$html_string .= dd_update_matter_application_status_form($_REQUEST['form_client'],
		$_REQUEST['form_matter'], $_REQUEST['form_application_id']);
} else {
	$html_string .= "I don't know what to do.";
}


$web_page->add_to_body("<CENTER>\n");
$web_page->add_to_body($html_string);
$web_page->add_to_body("</CENTER>\n");


$web_page->conclude_page();
$web_page->write_page();

?>
