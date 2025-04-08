<?
/**
 * @author		Ian M. Fink
 *
 * @file		login.php
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
$html_string .= "<TITLE>Docket Dashboard Login</TITLE>\n";
$html_string .= "</HEAD>\n";
$html_string .= dd_get_body_tag();
$html_string .= dd_get_leader();

$input_method = "";

if ($_POST['username'] != NULL) {
	$input_method = "POST";
} else if ($_GET['username'] != NULL) {
	$input_method = "GET";
}

if ($_POST['username'] == NULL) {
	$html_string .= "<CENTER>\n";
	$html_string .= dd_form_start("/cgi-bin/login", "POST");
	$html_string .= "<TABLE>\n";
	$html_string .= "<TR>\n";
	$html_string .= "\t<TD align=\"right\">Username:</TD>";
	$html_string .= "<TD align=\"left\">";
	$html_string .= "<INPUT TYPE=\"text\" NAME=\"username\">";
	$html_string .= "</TD>\n";
	$html_string .= "</TR>\n";

	$html_string .= "<TR>\n";
	$html_string .= "\t<TD align=\"right\">Password:</TD>";
	$html_string .= "<TD align=\"left\">";
	$html_string .= "<INPUT TYPE=\"password\" NAME=\"password\">";
	$html_string .= "</TD>\n";
	$html_string .= "</TR>\n";

	$html_string .= "<TR>\n";
	$html_string .= "\t<TD align=\"center\" colspan=\"2\">";
	$html_string .= "<INPUT TYPE=\"submit\" VALUE=\"Login\">";
	$html_string .= "</TD>\n";
	$html_string .= "</TR>\n";
	$html_string .= "</TABLE>\n";
	$html_string .= "</FORM>\n";
	$html_string .= "</CENTER>\n";
} else {
	$username = $_POST['username'];
	$password = $_POST['password'];
/*	$row = dd_get_user_row($username); */
/*	if ($row['password'] == hash("sha256", $password)) { */
	if (dd_password_correct($username, $password)) {
		$html_string .= "Login Successful.";
		header("Location: http://127.0.0.1/cgi-bin/db1");
	} else {
		$html_string .= "Login Unsuccessful.";
		header("Location: http://127.0.0.1/cgi-bin/login");
		$html_string .= "";
		$html_string .= "<BR>\n";
		$html_string .= "<BR>\n";
		$html_string .= "<BR>\n";
	}
}



$html_string .= "\n<BR>\n";
$html_string .= "\n<BR>\n";


$html_string .= "</BODY>\n";
$html_string .= "</HTML>\n";

echo $html_string;

/*
 * End of file:	login.php
 */
?>
