<?
/**
 * @author		Ian M. Fink
 *
 * @file		dd_funcs.php
 *
 * @copyright	Copyright (C) 2025 Ian M. Fink.  All rights reserved.
 * 				A commercial license can be made available.
 *
 * Contact:		www.linkedin.com/in/ianfink
 *
 * Tabstop:		4
 * 
 */

/*
 * Includes
 */

include $dd_php_files . "/dd_objects.php";

/*
 * Globals
 */

$dd_db_file = "/Users/ian/docket_dashboard/db/docket.db";
$dd_db_PDO_sqlite_file = "sqlite:" . $dd_db_file;

$dd_db_PDO_postgres_database = "postgres:" . $dd_db_file;
$dd_db_postgres_user = "ian";
$dd_db_postgres_password = "changeme";

$dd_use_full_path = False;
$dd_full_path = "";
$dd_local_timezone = "America/Chicago";
$dd_db_handle = dd_db_handle_setup();

class user_class {
	public $username;
	public $password;
	public $first_name;
	public $middle_name;
	public $last_name;
	public $preferred_name;
	public $firm_username;
	public $timekeeper_id;
	public $timezone;
	public $timezone_object;
	public $user_level;
	public $salt;
	public $position;

	private $user_stats;

	/*---------------------------------------------------------------------*/

	public function
	__construct($constructor_username) {
		global	$dd_db_handle;
		$query_string = "";

		$query_string .= "SELECT * FROM user_t WHERE username = :username_binder";
		$tmp_results = $dd_db_handle->prepare($query_string);
		$tmp_results->bindValue(":username_binder", $constructor_username, PDO::PARAM_STR);
		$exec_bool = $tmp_results->execute();

		$rows = $tmp_results->fetchALL();
		$tmp_results->closeCursor();

		if (count($rows) != 0) {
			$row = $rows[0];
			$this->username = $row['username'];
			$this->password = $row['password'];
			$this->first_name = $row['first_name'];
			$this->middle_name = $row['middle_name'];
			$this->last_name = $row['last_name'];
			$this->preferred_name = $row['preferred_name'];
			$this->firm_username = $row['firm_username'];
			$this->timekeeper_id = $row['timekeeper_id'];
			$this->timezone = $row['timezone'];
			$this->user_level = $row['user_level'];
			$this->salt = $row['salt'];
			$this->position = $row['position'];
		}
	
		$this->user_stats = array();
		$this->user_stats['http_user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$this->user_stats['remote_address'] = $_SERVER['REMOTE_ADDR'];
		$this->user_stats['http_referer'] = $_SERVER['HTTP_REFERER'];
		$this->user_stats['query_string'] = $_SERVER['QUERY_STRING'];
		$this->user_stats['username'] = $this->username;
		$this->user_stats['access_timestamp'] = 'now()';
		
		$this->store_user_stats();
	} /* __construct */

	/*---------------------------------------------------------------------*/

	public function
	store_user_stats() {
		global	$dd_db_handle;
		$insert_string = "";
		$table_object = new use_table_object("page_access", $this->user_stats);

		return;

		$table_object->pre_prepare_insert_string(TRUE);
		$table_object->execute_insert_string($dd_db_handle, array(), TRUE);



/*
		$tmp_results = $dd_db_handle->prepare($query_string);
		$tmp_results->bindValue(":username_binder", $constructor_username, PDO::PARAM_STR);
		$exec_bool = $tmp_results->execute();
*/
	} /* store_user_stats */
} /* the_user_class */

class globals_class {
	public	$session_username;
	public	$session_userlevel;
	public	$userlevel_array;
	public	$public_img_url_dir;
	public	$public_img__dir;
	public	$current_user;
	public	$date_now;
	public	$page_debug_object;

	public function __construct() {
		$this->session_username = "ifink";
		$this->session_userlevel = 1000;
		$this->public_img_url_dir = "/img/";
		$this->public_img_dir = "/Users/ian/docket_dashboard/www/html" . $this->public_img_url_dir;
		$this->userlevel_array['foo0'] = 0;
		$this->userlevel_array['foo1'] = 10;
		$this->userlevel_array['foo2'] = 20;
		$this->userlevel_array['admin'] = 1000;
		$this->date_now = new DateTime();
	}

	public function init_user($the_username) {
		$this->current_user = new user_class($the_username);
	}
}

$globals = new globals_class();

$dd_time_zones = array (
	'Eastern'			=> 'America/New_York'
	, 'Central'			=> 'America/Chicago'
	, 'Mountain'		=> 'America/Denver'
	, 'Mountain no DST'	=> 'America/Phoenix'
	, 'Pacific'			=> 'America/Los_Angeles'
	, 'Alaska'			=> 'America/Anchorage'
	, 'Hawaii'			=> 'America/Adak'
	, 'Hawaii no DST'	=> 'Pacific/Honolulu'
	, 'London'			=> 'Europe/London'
	, 'Rome'			=> 'Europe/Rome'
	, 'Berlin'			=> 'Europe/Berlin'
	, 'Dublin'			=> 'Europe/Dublin'
	, 'Paris'			=> 'Europe/Paris'
	, 'Taipei'			=> 'Asia/Taipei'
	, 'Shanghai'		=> 'Asia/Shanghai'
);

$dd_num_string_to_month_name = array (
	'01'	=> 'January'
	, '02'	=> 'February'
	, '03'	=> 'March'
	, '04'	=> 'April'
	, '05'	=> 'May'
	, '06'	=> 'June'
	, '07'	=> 'July'
	, '08'	=> 'August'
	, '09'	=> 'September'
	, '10'	=> 'October'
	, '11'	=> 'November'
	, '12'	=> 'December'
);

/***********************************************************************/

function
dd_init()
{
	global	$globals;
	global	$dd_local_timezone;
	global	$dd_time_zones;

	$globals->init_user('ifink');
	$the_date_time_zone = new DateTimeZone($dd_time_zones[$globals->current_user->timezone]);
	$globals->current_user->timezone_object = $the_date_time_zone;
	$globals->date_now->setTimezone($the_date_time_zone);
//	date_default_timezone_set($dd_time_zones[$globals->current_user->timezone]);

	return TRUE;
} /* dd_init */

/***********************************************************************/

function
dd_make_public_img_tag($public_img)
{
	global	$globals;

	list($width, $height, $type, $attr) = getimagesize($globals->public_img_dir . $public_img);

	$tmp_string = sprintf("<IMG SRC=\"%s\" HEIGHT=\"%s\" WIDTH=\"%s\">",
		$globals->public_img_url_dir . $public_img, $height, $width);


//	$tmp_string = sprintf("fullpath = '%s'", $globals->public_img_dir . $public_img);
	
	return $tmp_string;
} /* dd_make_public_img_tag */

/***********************************************************************/

function
dd_make_public_img_tag_reduced($public_img, $percentage)
{
	global	$globals;

	list($width, $height, $type, $attr) = getimagesize($globals->public_img_dir . $public_img);

	$tmp_string = sprintf("<IMG SRC=\"%s\" HEIGHT=\"%s\" WIDTH=\"%s\">",
		$globals->public_img_url_dir . $public_img, ($height * $percentage),
		($width * $percentage));


//	$tmp_string = sprintf("fullpath = '%s'", $globals->public_img_dir . $public_img);
	
	return $tmp_string;
} /* dd_make_public_img_tag */

/***********************************************************************/

function
dd_date_to_DD_Month_YYYY($the_date)
{
	global $dd_num_string_to_month_name;

	if (!strcmp(gettype($the_date), "object")) {
		return $the_date->format("d F Y");
	}

	$tmp_str = strval($the_date);

	list($the_year, $the_month, $the_day) = explode("-", $tmp_str);

	return sprintf("%s %s %s", $the_day, $dd_num_string_to_month_name[$the_month], $the_year);
} /* dd_date_to_DD_Month_YYYY */

/***********************************************************************/

function
dd_date_add_months($the_date, $num_months)
{
	if (!strcmp(gettype($the_date), "object")) {
		$my_date = clone $the_date;
	} else {
		$tmp_str = strval($the_date);
		$my_date = new DateTime($tmp_str);
	}

	$my_interval = new DateInterval("P" . strval($num_months) . "M");
	$my_date->add($my_interval);

	return $my_date;
//	return $my_date->format("Y-m-d");

/*
	list($the_year, $the_month, $the_day) = explode("-", $tmp_str);

	$month_int = intval($the_month);
	$month_int += $num_months;
	
	if ($month_int > 12) {
		$year_int = intval($the_year);
		$year_int++;
		$the_year = strval($year_int);
		$month_int -= 12;
	}

	return sprintf("%s-%02d-%s", $the_year, $month_int, $the_day);
*/
} /* dd_date_add_months */

/***********************************************************************/

function
dd_db_handle_setup()
{
	$tmp_db_handle = dd_get_db_handle();
/*	$tmp_db_handle->enableExceptions(TRUE); */ 
	$tmp_db_handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	return $tmp_db_handle;
} /* dd_db_handle_setup */

/***********************************************************************/

function
dd_setup()
{
} /* dd_setup */

/***********************************************************************/

function
dd_cleanup()
{
	global $dd_db_handle;

	dd_release_db_handle($dd_db_handle);
} /* dd_cleanup */

/***********************************************************************/

function
dd_finish_rows($tmp_result)
{
/*	while ($row = $tmp_result->fetchArray()) { */
	while ($row = $tmp_result->fetch()) {

	}

} /* dd_finish_rows */

/***********************************************************************/

function
dd_generate_salt()
{
	$bytes = random_bytes(12);
	
	return(base64_encode(bin2hex($bytes)));
} /* dd_generate_salt */

/***********************************************************************/

function
dd_password_correct($form_username, $form_password)
{
	$row =  dd_get_user_row($form_username); 
	$db_salt = $row['salt'];
	$db_password = $row['password'];
	return( 0 == strcmp($db_password, hash("sha256", $db_salt . $form_password)) );
} /* dd_password_correct */

/***********************************************************************/

function
dd_unix_time_now()
{
	return strval(time());
} /* dd_unix_time_now */

/***********************************************************************/
/*
function
dd_start_of_day(
*/

/***********************************************************************/

function
dd_string_to_unix_time_int($tmp_time_string)
{
	return intval($tmp_time_string);
} /* dd_string_to_unix_time_int */

/***********************************************************************/

function
dd_day_month_year($tmp_unix_time)
{
	return date("d M Y", $tmp_unix_time);
} /* dd_day_month_year */

/***********************************************************************/

function
dd_is_on_weekend($tmp_unix_time)
{
	$tmp_string = date("D", $tmp_unix_time);

	return !strcmp($tmp_string, "Sat") || !strcmp($tmp_string, "Sun");
} /* dd_is_on_weekend */

/***********************************************************************/

function
dd_set_cookie($name, $value, $expiration)
{
	setcookie($name, $value, $expiration, "/");
}

/***********************************************************************/

function
dd_get_php_full_filename()
{
	return $_SERVER['SCRIPT_FILENAME'];
} /* dd_get_php_full_filename */

/***********************************************************************/

function
dd_get_php_filename()
{
	$dd_script_filename_array = str_getcsv(dd_get_php_full_filename(), "/");
	$tmp_len = count($dd_script_filename_array, COUNT_NORMAL);

	return $dd_script_filename_array[$tmp_len - 1];
} # dd_get_php_filename

/***********************************************************************/

function
dd_get_php_files()
{
	return $_ENV['PHP_INCLUDE_PATH'];
} /* dd_get_php_files */

/***********************************************************************/

function
dd_get_whatever_file($whatever, $tmp_php_filename)
{
	return dd_get_php_files() . "/" . $whatever . "/" . $tmp_php_filename;
} /* dd_get_whatever_file */

/***********************************************************************/

function
dd_get_body_file()
{
	$tmp_str = dd_get_whatever_file("body", dd_get_php_filename());
	
	if (file_exists($tmp_str)) {
		return $tmp_str;
	} else {
		return dd_get_whatever_file("body", "default.php");
	}

} /* dd_get_body_file */

/***********************************************************************/

function
dd_get_leader_file()
{
	$tmp_str = dd_get_whatever_file("leader", dd_get_php_filename());
	
	if (file_exists($tmp_str)) {
		return $tmp_str;
	} else {
		return dd_get_whatever_file("leader", "default.php");
	}

} /* dd_get_leader_file */

/***********************************************************************/

function
dd_get_trailer_file()
{
	$tmp_str = dd_get_whatever_file("trailer", dd_get_php_filename());
	
	if (file_exists($tmp_str)) {
		return $tmp_str;
	} else {
		return dd_get_whatever_file("trailer", "default.php");
	}

} /* dd_get_trailer_file */

/***********************************************************************/

function
dd_get_style_file()
{
	$tmp_str = dd_get_whatever_file("style", dd_get_php_filename());
	
	if (file_exists($tmp_str)) {
		return $tmp_str;
	} else {
		return dd_get_whatever_file("style", "default.php");
	}

} /* dd_get_style_file */

/***********************************************************************/
function
dd_get_body_contents()
{
	return "";
} /* dd_get_body_contents */

/***********************************************************************/

function
dd_get_body_tag()
{
	return function_exists("dd_get_custom_body_tag") ? 
		dd_get_custom_body_tag() : dd_get_default_body_tag();
} /* dd_get_body_tag */

/***********************************************************************/

function
dd_get_leader()
{
	return function_exists("dd_get_custom_leader") ? 
		dd_get_custom_leader() : dd_get_default_leader();
} /* dd_get_leader */

/***********************************************************************/

function
dd_get_trailer()
{
	return function_exists("dd_get_custom_trailer") ? 
		dd_get_custom_trailer() : dd_get_default_trailer();
} /* dd_get_trailer */

/***********************************************************************/

function
dd_get_style()
{
	return function_exists("dd_get_custom_style") ? 
		dd_get_custom_style() : dd_get_default_style();
} /* dd_get_style */

/***********************************************************************/

function
dd_anchor($anchor_text, $cgi_file)
{
	$tmp_str = "<A HREF=\"$cgi_file\">";
	$tmp_str .= $anchor_text;
	$tmp_str .= "</A>";

	return $tmp_str;	
} /* dd_anchor */

/***********************************************************************/

function
dd_leader_anchor($anchor_text, $cgi_file)
{
	$tmp_str = "<A HREF=\"$cgi_file\" class=\"round_button_class\">";
	$tmp_str .= $anchor_text;
	$tmp_str .= "</A>";

	return $tmp_str;	
} /* dd_anchor */

/***********************************************************************/

function
dd_get_db_handle()
{
	global $dd_db_file;
	global $dd_db_PDO_sqlite_file;
	global $dd_db_PDO_postgres_database;
	global $dd_db_postgres_user;
	global $dd_db_postgres_password;

/*	return new SQLite3($dd_db_file); */

/* PDO sqlite */
/*	return new PDO($dd_db_PDO_sqlite_file); */

/* PDO postgres */
	return new PDO('pgsql:host=127.0.0.1;port=5432;dbname=docket;user=ian;password=changeme');

/* Another PDO postgres */
/*
	return new PDO('pgsql:host=127.0.0.1;port=5432;dbname=docket' 'ian', 'changme', array(
    PDO::ATTR_PERSISTENT => true
));
*/

/*
;port=%d;dbname=%s;user=%s;password=%s"

PDO('dblib:host=your_hostname;dbname=your_db;charset=UTF-8', $user, $pass);
*/
/*
array(
    PDO::ATTR_PERSISTENT => true
)
*/

} /* dd_get_db_handle */

/***********************************************************************/

function
dd_release_db_handle($dd_tmp_db_handle)
{
/*	$dd_tmp_db_handle->close(); */
	$dd_tmp_db_handle = null;

	return;
} /* dd_release_db_handle */

/***********************************************************************/

function
dd_tmp_table_print()
{
	global	$dd_db_handle;
/*	global $dd_db_file; */
	$tmp_str = "";

	$tmp_results = $dd_db_handle->query('SELECT * FROM supervising_atty');

	$tmp_str .= "<TABLE>\n";

	$row_num = 0;
/*	while ($row = $tmp_results->fetchArray()) { */
	while ($row = $tmp_results->fetch()) {
		$tmp_str .= "<TR>\n";
		$tmp_limit = count($row) / 2;
		$tmp_str .= "<TD>" . strval($tmp_limit) . "</TD>\n";
		for ($i=0; $i<$tmp_limit; $i++) {
			if ($row_num % 2 == 1) {
				$tmp_str .= "<TD BGCOLOR=\"#606060\">" 
					. $row[strval($i)] .  "</TD>\n";
			} else {
				$tmp_str .= "<TD>" . $row[strval($i)] . "</TD>\n";
			}
		}
		$tmp_str .= "</TR>\n";
		$row_num++;
	}

	$tmp_results->closeCursor();

	$tmp_str .= "</TABLE>\n";


	return $tmp_str;
} /* dd_tmp_table_print */


/***********************************************************************/

function
dd_get_inventors($client, $matter)
{
	global	$dd_db_handle;
	$ret_string = "";
	$tmp_string = "";
	$rows = 0;

/*
	$the_query = "SELECT * FROM inventor ";
	$the_query .= "WHERE id IN ";
	$the_query .= "(SELECT inventor_id FROM ";
	$the_query .= "inventor_matter_relationship WHERE client = '";
	$the_query .= $client . "' AND ";
	$the_query .= "matter = '";
	$the_query .= $matter . "')";
*/

	$the_query = "SELECT * FROM inventor ";
	$the_query .= "WHERE id IN ";
	$the_query .= "(SELECT inventor_id FROM ";
	$the_query .= "inventor_matter_relationship WHERE client = :client_binder ";
	$the_query .= " AND matter = :matter_binder)";

	$tmp_results = $dd_db_handle->prepare($the_query);
	$tmp_results->bindValue(':client_binder', $client, PDO::PARAM_STR);
	$tmp_results->bindValue(':matter_binder', $matter, PDO::PARAM_STR);
	$tmp_results->execute();


/*	$tmp_results = $dd_db_handle->query($the_query); */

	$ret_string = "<TABLE>\n";

/*	while ($row = $tmp_results->fetchArray()) { */
	while ($row = $tmp_results->fetch()) {
		$tmp_string .= "<TR>\n";
		$tmp_limit = count($row) / 2;
		for ($i=0; $i<$tmp_limit; $i++) {
			$tmp_string .= "<TD>" . $row[strval($i)] . "</TD>";
		}
		$tmp_string .= "\n</TR>\n";
		$rows++;
	}

	$tmp_results->closeCursor();

	if ($rows > 0) {
		$ret_string .= $tmp_string;
		$ret_string .= "</TABLE>\n";
	} else {
		$ret_string = "";
	}

	return $ret_string;
} /* dd_get_inventors */

/***********************************************************************/

function
dd_form_start($dd_action, $dd_method)
{
	global $dd_use_full_path;
	global $dd_full_path;
	$ret_string = sprintf("<FORM ACTION=\"%s\" METHOD=\"%s\">\n",
		$dd_action, $dd_method);
	
	return $ret_string;
} /* dd_form_start */

/***********************************************************************/

function
dd_form_dropdown($dd_name, $dd_value_array)
{

	$ret_string = "";

	$ret_string .= sprintf("<SELECT NAME=\"%s\" ID=\"id_%s_id\" CLASS=\"select-css\">\n", 
		$dd_name, $dd_name);

	foreach ($dd_value_array as $op_name => $op_value) {
		$ret_string .= sprintf("\t<OPTION VALUE=\"%s\">%s</OPTION>\n",
			$op_name, $op_value);
	}

	$ret_string .= "</SELECT>\n";

	return $ret_string;
} /* dd_form_dropdown */

/***********************************************************************/

function
dd_form_dropdown_selected($dd_name, $dd_value_array, $the_selected)
{

	$ret_string = "";

	$ret_string .= sprintf("<SELECT NAME=\"%s\" ID=\"id_%s_id\" CLASS=\"select-css\">\n", 
		$dd_name, $dd_name);

	foreach ($dd_value_array as $op_name => $op_value) {
		if (!strcmp($the_selected, $op_name)) {
			$ret_string .= sprintf("\t<OPTION VALUE=\"%s\" selected>%s</OPTION>\n",
				$op_name, $op_value);
		} else {
			$ret_string .= sprintf("\t<OPTION VALUE=\"%s\">%s</OPTION>\n",
				$op_name, $op_value);
		}
	}

	$ret_string .= "</SELECT>\n";

	return $ret_string;
} /* dd_form_dropdown_selected */

/***********************************************************************/

function
dd_form_dropdown2($dd_name, $dd_value_array)
{

	$ret_string = "";

	$the_id = sprintf("id_%s_id",  $dd_name);

	$ret_string .= sprintf("<SELECT NAME=\"%s\" ID=\"%s\" CLASS=\"select-css\" onchange=\"get_matter_dropdown('%s')\">\n", $dd_name, $the_id, $the_id);
//	$ret_string .= sprintf("<SELECT NAME=\"%s\" CLASS=\"select-css\" onchange=\"r2d2()\">\n", 
//		$dd_name);

	foreach ($dd_value_array as $op_name => $op_value) {
		$ret_string .= sprintf("\t<OPTION VALUE=\"%s\">%s</OPTION>\n",
			$op_name, $op_value);
	}

	$ret_string .= "</SELECT>\n";

	return $ret_string;
} /* dd_form_dropdown2 */

/***********************************************************************/

function
dd_get_client_array()
{
	global	$dd_db_handle;
	global	$globals;
	$dd_client_array = array();

	$wall_access_array = dd_get_wall_access_array($globals->session_username);

	$tmp_results = $dd_db_handle->prepare('SELECT * FROM client ORDER BY description ASC');
	$tmp_results->execute();

	$rows = $tmp_results->fetchAll();
	$tmp_results->closeCursor();

	foreach ($rows as $row) {
		if (!in_array($row['client'], $wall_access_array) &&
			$globals->session_userlevel < $globals->userlevel_array['admin']) {
			continue;
		}
		/* html/form value ,/= what is shown in dropdown */
		$dd_client_array[$row['client']] = empty($row['description']) ?
			sprintf("%s", $row['alt_client_id']) :
			sprintf("%s (%s)", $row['alt_client_id'], $row['description']);
	}

	return $dd_client_array;
} /* dd_get_client_array */

/***********************************************************************/

function
dd_get_country_array()
{
	global	$dd_db_handle;
	global	$globals;
	$dd_country_array = array();

	$tmp_results = $dd_db_handle->prepare('SELECT * FROM country ORDER BY short_name ASC');
	$tmp_results->execute();

	$rows = $tmp_results->fetchAll();
	$tmp_results->closeCursor();

	$dd_country_array['US'] = "US (United States)";

	foreach ($rows as $row) {
		if (!strcmp($row['short_name'], "US")) {
			continue;
		}
		/* html/form value ,/= what is shown in dropdown */
		$dd_country_array[$row['short_name']] = 
			sprintf("%s (%s)", $row['short_name'], $row['long_name']);
	}

	return $dd_country_array;
} /* dd_get_country_array */

/***********************************************************************/

function
dd_get_wall_access_array($wall_access_username)
{
	global	$dd_db_handle;
	$wall_access_array = array();
	$query_string = "";

	$query_string .= "SELECT client FROM wall_access WHERE username = :username_binder";
	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->bindValue(":username_binder", $wall_access_username);
	$tmp_results->execute();

	$rows = $tmp_results->fetchAll();
	$tmp_results->closeCursor();

	foreach ($rows as $row) {
		array_push($wall_access_array, $row['client']);
	}

	return $wall_access_array;
} /* dd_get_wall_access_array */

/***********************************************************************/

function
dd_get_matter_array($client_val)
{
	global	$dd_db_handle;
	$matter_array = array();
	$query_string = "";

	$query_string .= "SELECT matter FROM matter WHERE client = :client_binder ORDER BY matter ASC";
	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->bindValue(':client_binder', $client_val, PDO::PARAM_STR);
	$tmp_results->execute();

	$matter_rows = $tmp_results->fetchAll();
	$tmp_results->closeCursor();

	foreach ($matter_rows as $row) {
		/* html/form value ,/= what is shown in dropdown */
		$matter_array[$row['matter']] = $row['matter'];
	}

	return $matter_array;
} /* dd_get_matter_array */

/***********************************************************************/

function
dd_get_application_type_array()
{
	global	$dd_db_handle;
	$application_type_array = array();

	$query_string = "";
	$query_string .= "SELECT * FROM application_type ORDER BY id"; 
	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->execute();

	$application_type_rows = $tmp_results->fetchAll();
	$tmp_results->closeCursor();

	foreach ($application_type_rows as $row) {
		/* html/form value ,/= what is shown in dropdown */
		$application_type_array[$row['id']] = $row['type'];
	}

	return $application_type_array;
} /* dd_get_application_type_array */

/***********************************************************************/

function
dd_get_oa_type_array()
{
	global	$dd_db_handle;
	$oa_type_array = array();

	$query_string = "";
	$query_string .= "SELECT * FROM office_action_type ORDER BY oa_type ASC"; 
	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->execute();

	$oa_type_rows = $tmp_results->fetchAll();
	$tmp_results->closeCursor();

	foreach ($oa_type_rows as $row) {
		/* html/form value ,/= what is shown in dropdown */
		$oa_type_array[$row['oa_type']] = $row['oa_type'];
	}

	return $oa_type_array;
} /* dd_get_oa_type_array */

/***********************************************************************/

function
dd_get_reply_type_array()
{
	global	$dd_db_handle;
	$reply_type_array = array();

	$query_string = "";
	$query_string .= "SELECT * FROM reply_type ORDER BY reply_type ASC"; 
	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->execute();

	$rows = $tmp_results->fetchAll();
	$tmp_results->closeCursor();

	foreach ($rows as $row) {
		/* html/form value ,/= what is shown in dropdown */
		$reply_type_array[$row['reply_type']] = $row['reply_type'];
	}

	return $reply_type_array;
} /* dd_get_reply_type_array */

/***********************************************************************/

function
dd_get_user_array()
{
	global	$dd_db_handle;
	$user_array = array();

	$query_string = "";
	$query_string .= "SELECT * FROM user_t ORDER BY username";
	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->execute();

	$rows = $tmp_results->fetchAll();
	$tmp_results->closeCursor();

	foreach ($rows as $row) {
		/* html/form value ,/= what is shown in dropdown */
		$user_array[$row['username']] = sprintf("%s (%s %s)", $row['username'],
			$row['first_name'], $row['last_name']);
	}

	return $user_array;
} // dd_get_user_array

/***********************************************************************/

function
dd_now_timestamp_tz()
{
	global	$globals;

	return sprintf("%s%03d", $globals->date_now->format("Y-m-d H:i:s.0"),
		$globals->date_now->getOffset() / 3600);
} // dd_now_timestamp_tz

/***********************************************************************/

function
dd_reassign_application(&$db_handle, $app_id, $to_whom, &$application_row)
{
	global	$globals;
	$ret_string = "SUCCESS";

	$the_timestamp = dd_now_timestamp_tz();

//	$applicaton_row = dd_get_application_row($db_handle, $app_id);

	$the_update_text = sprintf("Application reassigned from %s to %s by %s",
		$application_row['application_assigned_to'], $to_whom, $globals->session_username);

	try {
		$db_handle->beginTransaction();
		$sql_stmt = "UPDATE application SET application_assigned_to = ";
		$sql_stmt .= " :application_assigned_to_binder ";
		$sql_stmt .= " WHERE id = " . $app_id;
		$tmp_results = $db_handle->prepare($sql_stmt);
		$tmp_results->bindValue(':application_assigned_to_binder', $to_whom, PDO::PARAM_STR);
		$exec_bool = $tmp_results->execute();

		$sql_stmt = "INSERT INTO matter_updates ";
		$sql_stmt .= " (client, matter, the_update, username, the_update_timestamp) ";
		$sql_stmt .= " VALUES (:client_binder, :matter_binder, :the_update_binder, :username_binder, :the_update_timestamp_binder)";

		$tmp_results = $db_handle->prepare($sql_stmt);
		$tmp_results->bindValue(':client_binder', $application_row['client'], PDO::PARAM_STR);
		$tmp_results->bindValue(':matter_binder', $application_row['matter'], PDO::PARAM_STR);
		$tmp_results->bindValue(':the_update_binder', $the_update_text, PDO::PARAM_STR);
		$tmp_results->bindValue(':username_binder', $globals->current_user->username,
			PDO::PARAM_STR);
		$tmp_results->bindValue(':the_update_timestamp_binder', $the_timestamp, PDO::PARAM_STR);
		$exec_bool = $tmp_results->execute();

		$db_handle->commit();
	} catch(Exception $e) {
		$ret_string = $e->getMessage();
		$db_handle->rollBack();
	}

	return $ret_string;
} // dd_reassign_application

/***********************************************************************/

function
dd_get_oa_status_array()
{
	global	$dd_db_handle;
	$oa_statuses_array = array();

	$query_string = "";
	$query_string .= "SELECT * FROM possible_oa_statuses ORDER BY oa_status"; 
	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->execute();

	$rows = $tmp_results->fetchAll();
	$tmp_results->closeCursor();

	foreach ($rows as $row) {
		/* html/form value ,/= what is shown in dropdown */
		$oa_statuses_array[$row['oa_status']] = $row['oa_status'];
	}

	return $oa_statuses_array;
} /* dd_get_oa_status_array */

/***********************************************************************/

function
dd_get_application_status_array()
{
	global	$dd_db_handle;
	$application_statuses_array = array();

	$query_string = "";
	$query_string .= "SELECT * FROM possible_application_statuses ORDER BY application_status"; 
	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->execute();

	$rows = $tmp_results->fetchAll();
	$tmp_results->closeCursor();

	foreach ($rows as $row) {
		/* html/form value ,/= what is shown in dropdown */
		$application_statuses_array[$row['application_status']] = $row['application_status'];
	}

	return $application_statuses_array;
} /* dd_get_oa_status_array */

/***********************************************************************/

function
dd_get_type_array()
{
	global	$dd_db_handle;
	$application_type_array = array();

	$query_string = "";
	$query_string .= "SELECT * FROM application_type ORDER BY type ASC"; 
	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->execute();

	$oa_type_rows = $tmp_results->fetchAll();
	$tmp_results->closeCursor();

	foreach ($oa_type_rows as $row) {
		/* html/form value ,/= what is shown in dropdown */
		$application_type_array[$row['id']] = $row['type'];
	}

	return $application_type_array;
} /* dd_get_type_array */

/***********************************************************************/

function
dd_generate_years_array()
{
	$years_array = array();
	$year_end = intval(date("Y")) + 2;
	$year_begin = $year_end - 22;

	$years_array['NONE'] = "Year";

	for ($i=$year_end; $i>=$year_begin; $i--) {
		$the_year = sprintf("%d", $i);
		/* html/form value ,/= what is shown in dropdown */
		$years_array[$the_year] = $the_year;
	}

	return $years_array;
} /* dd_generate_years_array */

/***********************************************************************/

function
dd_generate_months_array()
{
	$months_array = array();

	$months_array['NONE'] = "Month";

	for ($i=1; $i<=12; $i++) {
		$date_object = DateTime::createFromFormat('!m', $i);
		/* html/form value ,/= what is shown in dropdown */
		$month_string = sprintf("%02d", $i);
		$months_array[$month_string] =  $month_string . " (" . $date_object->format('F') . ")";
	}

	return $months_array;
} /* dd_generate_months_array */

/***********************************************************************/

function
dd_generate_days_array()
{
	$days_array = array();

	$days_array['NONE'] = "Day";

	for ($i=1; $i<=31; $i++) {
		$day_string = sprintf("%02d", $i);
		$days_array[$day_string] = $day_string;
	}

	return $days_array;
} /* dd_generate_days_array */

/***********************************************************************/

function
dd_find_application($form_client, $form_matter)
{
	$form_string = "";
//	$form_string .= dd_make_public_img_tag("add_inventor2.png")
	$form_string .= "<TABLE>\n";
	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">Find Application</TD>\n";
	$form_string .= "</TR>\n";

	if (0 != strcmp($form_client, "") && 0 != strcmp($form_matter)) {
		$query_string = "SELECT * application ";
		$query_string .= " WHERE client = :client_binder AND matter = :matter";

		$form_string .= "</TABLE>\n";
		$form_string .= "</FORM>\n";
	} else if (0 != strcmp($form_client, "")) {

		$form_string .= "</TABLE>\n";
		$form_string .= "</FORM>\n";
	} else {

	}


	return $form_string;
} /* dd_find_application */

/***********************************************************************/

function
dd_create_matter_form()
{
	$form_string = "";
	$form_string .= dd_form_start("create_matter", "POST");
	$form_string .= "<TABLE>\n";
	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">Create New Matter</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";

	$form_string .= "<TD align=\"right\">Client:</TD>";
	$form_string .= "<TD align=\"left\">";

	$client_array = dd_get_client_array();
	$form_string .= dd_form_dropdown("client_val", $client_array);
	$form_string .= "</TD>";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">New Matter:</TD>\n";
	$form_string .= "<TD align=\"left\">";

	$form_string .= "<INPUT TYPE=\"text\" NAME=\"new_matter_val\" REQUIRED>";
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">";
	$form_string .= "<INPUT TYPE=\"submit\" value=\"Create New Matter\">";
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";
	$form_string .= "</TABLE>\n";
	$form_string .= "</FORM>\n";

	return $form_string;
} /* dd_create_matter_form */

/***********************************************************************/

function
dd_add_inventor_to_application($form_application_id, $form_inventor_id)
{
	global	$dd_db_handle;
	$query_string = "";

	$query_string .= "INSERT INTO inventor_application_relationship (inventor_id, application_id) ";
	$query_string .= " VALUES (:inventor_id_binder, :application_id)";
	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->bindParam(":inventor_id_binder", $form_inventor_id, PDO::PARAM_INT);
	$tmp_results->bindParam(":application_id", $form_application_id, PDO::PARAM_INT);
	$exec_bool = $tmp_results->execute();

	return $exec_bool;
} /* dd_add_inventor_to_application */

/***********************************************************************/

function
dd_create_list_inventors_form()
{
	$form_string = "";
	$form_string .= dd_form_start("list_inventors", "POST");
	$form_string .= "<TABLE>\n";
	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\"><NOBR>List Inventors</NOBR></TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";

	$form_string .= "<TD align=\"right\">Client:</TD>";
	$form_string .= "<TD align=\"left\">";

	$client_array = dd_get_client_array();
	$form_string .= dd_form_dropdown("client_val", $client_array);
	$form_string .= "</TD>";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">New Matter:</TD>\n";
	$form_string .= "<TD align=\"left\">";

	$form_string .= "<INPUT TYPE=\"text\" NAME=\"new_matter_val\" REQUIRED>";
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">";
	$form_string .= "<INPUT TYPE=\"submit\" value=\"Create New Matter\">";
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";
	$form_string .= "</TABLE>\n";
	$form_string .= "</FORM>\n";

	return $form_string;
} /* dd_create_matter_form */

/***********************************************************************/

function
dd_newly_created_matter($client_val, $new_matter_val)
{
	$tmp_string = "";

	$tmp_string .= "<table>\n";
	$tmp_string .= "<tr>\n";
	$tmp_string .= "<td align=\"center\" colspan=\"2\">";
	$tmp_string .= "Newly Created Matter</td>\n";
	$tmp_string .= "</tr>\n";

	$tmp_string .= "<tr>\n";
	$tmp_string .= "<td align=\"right\">Client:</td>\n";
	$tmp_string .= "<td align=\"left\">" . $client_val . "</td>\n";
	$tmp_string .= "</tr>\n";

	$tmp_string .= "<tr>\n";
	$tmp_string .= "<td align=\"right\">Matter:</td>\n";
	$tmp_string .= "<td align=\"left\">" . $new_matter_val . "</td>\n";
	$tmp_string .= "</tr>\n";

	$tmp_string .= "<tr>\n";
	$tmp_string .= "<td align=\"center\" colspan=\"2\">";
	$insert_result = dd_create_new_matter($client_val, $new_matter_val);
	if ($insert_result == "SUCCESS") {
		$tmp_string .= "<FONT color=\"green\">";
		$tmp_string .= "Matter successfully created.";
		$tmp_string .= "</FONT>\n";
	} else {
		$tmp_string .= "<FONT color=\"red\" SIZE=\"+2\">";
		$tmp_string .= $insert_result;
		$tmp_string .= "</FONT>\n";
	}

	$tmp_string .= "</td>\n";

	$tmp_string .= "</tr>\n";

	$tmp_string .= "<table>\n";


	return $tmp_string;
} /* dd_newly_created_matter */

/***********************************************************************/

function
dd_create_new_matter($client, $matter)
{
	global	$dd_db_handle;
	$insert_sql = "";

	$insert_sql .= "INSERT INTO matter VALUES (";
	$insert_sql .= " '" . $client . "'"; /* client TEXT NOT NULL */
	$insert_sql .= " , '" . $matter . "'"; /* matter TEXT NOT NULL */
	$insert_sql .= " , NULL";	/*  client_matter_id TEXT */
	$insert_sql .= " , NULL";	/*  description TEXT */
	$insert_sql .= " , -1";		/*  filing_date INTEGER */
	$insert_sql .= " , -1";		/*  bar_date INTEGER */
	$insert_sql .= " , -1";		/*  next_action_due INTEGER */
	$insert_sql .= " , 0";		/*  total_budget INTEGER */
	$insert_sql .= " , 0";		/*  user_budget INTEGER */
	$insert_sql .= " , -1";		/*  next_action_id INTEGER */
	$insert_sql .= " , -1";		/*  matter_type_id INTEGER */
	$insert_sql .= " )";

	$insert_sql = "";


	$insert_sql .= "INSERT INTO matter VALUES (";
	$insert_sql .= " :client_binder"; /* client TEXT NOT NULL */
	$insert_sql .= " , :matter_binder"; /* matter TEXT NOT NULL */
	$insert_sql .= " , NULL";	/*  client_matter_id TEXT */
	$insert_sql .= " , NULL";	/*  description TEXT */
	$insert_sql .= " , -1";		/*  filing_date INTEGER */
	$insert_sql .= " , -1";		/*  bar_date INTEGER */
	$insert_sql .= " , -1";		/*  next_action_due INTEGER */
	$insert_sql .= " , 0";		/*  total_budget INTEGER */
	$insert_sql .= " , 0";		/*  user_budget INTEGER */
	$insert_sql .= " , -1";		/*  next_action_id INTEGER */
	$insert_sql .= " , -1";		/*  matter_type_id INTEGER */
	$insert_sql .= " )";

	$insert_sql = "";

	$insert_sql .= "INSERT INTO matter (client, matter) VALUES (";
	$insert_sql .= " :client_binder"; /* client TEXT NOT NULL */
	$insert_sql .= " , :matter_binder"; /* matter TEXT NOT NULL */
	$insert_sql .= " )";

	try {
		$tmp_results = $dd_db_handle->prepare($insert_sql);
		$tmp_results->bindParam(':client_binder', $client, PDO::PARAM_STR);
		$tmp_results->bindParam(':matter_binder', $matter, PDO::PARAM_STR);
		$exec_bool = $tmp_results->execute();
		if ($exec_bool) {
			$ret_string = "SUCCESS";
		} else {
			$err_array =  $tmp_results->errorInfo();
			$ret_string = sprintf("Error: SQLSTATE=\"%s\" Error Message=\"%s\" SQL=\"%s\"",
				$err_array[0], $err_array[2], $insert_sql);

		}
	} catch (Exception $e) {
		$ret_string = "Error: " . $e->getMessage();
/*
		$ret_string = "Caught exception: " . $e->getMessage();
		if ($ttt_db->ErrorCode == 0) {
			$ret_string = "Caught SUCCESS";
		} else {
			$ret_string = "Error: " . $ttt_db->ErrorMsg;
		}
*/
	}

	return $ret_string;
} /* dd_create_new_matter */


/***********************************************************************/

function
dd_find_application_form()
{
	$form_string = "";
	$form_string .= dd_form_start("find_application", "GET");
	$form_string .= "<TABLE>\n";
	$form_string .= "<TR>\n";
	$form_string .= "<TD class=\"dd_bold_me\" align=\"center\" colspan=\"2\">Find Application by Client/Matter</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";

	$form_string .= "<TD align=\"right\">Client:</TD>";
	$form_string .= "<TD align=\"left\">";

	$client_array = dd_get_client_array();
	$form_string .= dd_form_dropdown2("client", $client_array);
	$form_string .= "</TD>";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$matter_array = dd_get_matter_array(array_key_first($client_array));
	$form_string .= "<TD align=\"right\">Matter:</TD>\n";
	$form_string .= "<TD align=\"left\">";

	$form_string .= "<DIV id=\"the_matters_id\">";

//	$form_string .= "<INPUT TYPE=\"text\" NAME=\"matter\" REQUIRED>";
	$form_string .= dd_form_dropdown("matter", $matter_array);
	$form_string .= "</DIV>";
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">";
	$form_string .= "<INPUT TYPE=\"submit\" VALUE=\"Find It\">";
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";
	$form_string .= "</TABLE>\n";
	$form_string .= "</FORM>\n";

// find by application serial no.
	$form_string .= "<P></P>";
	$form_string .= dd_form_start("find_application", "GET");
	$form_string .= "<TABLE>\n";
	$form_string .= "<TR>\n";
	$form_string .= "<TD class=\"dd_bold_me\" align=\"center\" colspan=\"2\">Find Application by Application Serial Number</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "\t<TD align=\"right\">Application Serial No.:</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= "<INPUT TYPE=\"text\" NAME=\"form_app_ser_no\">";
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "\t<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">";
	$form_string .= "<INPUT TYPE=\"submit\" VALUE=\"Find It\">";
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";
	
	$form_string .= "</TABLE>\n";

	$form_string .= "</FORM>\n";

// find by client's ID for application
	$form_string .= "<P></P>";
	$form_string .= dd_form_start("find_application", "GET");
	$form_string .= "<TABLE>\n";
	$form_string .= "<TR>\n";
	$form_string .= "<TD class=\"dd_bold_me\" align=\"center\" colspan=\"2\">Find Application by Client Application Identifierr</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "\t<TD align=\"right\">Client Application Identifier:</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= "<INPUT TYPE=\"text\" NAME=\"form_client_matter_id\">";
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "\t<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">";
	$form_string .= "<INPUT TYPE=\"submit\" VALUE=\"Find It\">";
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";
	
	$form_string .= "</TABLE>\n";

	$form_string .= "</FORM>\n";

	return $form_string;
} /* dd_find_application_form */

/***********************************************************************/

function
dd_get_application_types()
{
	global	$dd_db_handle;
	$ret_array = array();

	$query_string = "";
	$query_string .= "SELECT * FROM application_type ORDER BY id ASC";
	$tmp_results = $dd_db_handle->prepare($query_string);
	$exec_bool = $tmp_results->execute();

	$query_result_rows = $tmp_results->fetchAll();
	$tmp_results->closeCursor();

	foreach ($query_result_rows as $query_result_row) {
		$ret_array[$query_result_row['id']] = $query_result_row['type'];
	}

	return($ret_array);
} /* dd_get_application_types */

/***********************************************************************/

function
dd_application_table_header()
{
	$header_string = "";
	$header_string .= "<TR CLASS=\"dd_bold2\">\n";
	$header_string .= "\t<TD>&nbsp;</TD>";
	$header_string .= "<TD>Client</TD>";
	$header_string .= "<TD>Matter</TD>";
	$header_string .= "<TD>Client's ID</TD>";
	$header_string .= "<TD>Inventor(s)</TD>\n";
	$header_string .= "<TD>Application Type</TD>";
	$header_string .= "<TD>Application Title</TD>";
	$header_string .= "<TD>File by</TD>";
	$header_string .= "<TD>Bar Date</TD>";
	$header_string .= "<TD>Filing Date</TD>";
	$header_string .= "<TD>Responsible</TD>";
	$header_string .= "<TD>Application Country</TD>";
	$header_string .= dd_td_default_nobr("Application Serial No.");
	$header_string .= "<TD>Application Pub. No.</TD>";
	$header_string .= "<TD>Patent No.</TD>\n";
	$header_string .= "<TD><!-- Action -->&nbsp;</TD>\n";
	$header_string .= "</TR>\n";

	return $header_string;
} /* dd_application_table_header */

/***********************************************************************/

function
dd_list_applications($form_client, $form_matter, $the_application_id, $the_app_ser_no, $the_client_matter_id)
{
	$tmp_string = "";
	$middle_t_string = "";
	$inventor_count = 0;
	$query_string = "";
	$middle_t_string = "";

	$middle_t_string .= "<TABLE>\n";
	$tmp_string .= dd_get_application_html_table_rows($form_client, $form_matter, $the_application_id, $the_app_ser_no, $the_client_matter_id);
	if (empty($tmp_string)) {
		$middle_t_string .= sprintf("<TR><TD><FONT color=\"#FF0000\">No application found for Client: \"%s\" Matter: \"%s\"</FONT></TD></TR>", $form_client, $form_matter);
	} else {
		$middle_t_string .= dd_application_table_header();
		$middle_t_string .= $tmp_string;
	}

	$middle_t_string .= "</TABLE>\n";

	return $middle_t_string;
} /* dd_list_applications */

/***********************************************************************/

function
dd_get_application_html_table_rows($form_client, $form_matter, $the_application_id, $the_app_ser_no, $the_client_matter_id)
{
	global	$dd_db_handle;
	global	$globals;
	$query_string = "";

	$application_types = dd_get_application_types();

	if (($form_client != null) && ($form_matter != null)) {
		$query_string .= "SELECT * FROM application WHERE client = :client_binder";
		$query_string .= " AND matter = :matter_binder";
		$tmp_results = $dd_db_handle->prepare($query_string);
		$tmp_results->bindParam(':client_binder', $form_client, PDO::PARAM_STR);
		$tmp_results->bindParam(':matter_binder', $form_matter, PDO::PARAM_STR);
	} else if (!empty($the_app_ser_no)) {
		$query_string .= "SELECT * FROM application WHERE application_no ILIKE :ser_no_binder";
		$tmp_results = $dd_db_handle->prepare($query_string);
		$the_app_ser_no = '%' . $the_app_ser_no . '%';
		$tmp_results->bindParam(':ser_no_binder', $the_app_ser_no, PDO::PARAM_STR);
	} else if (!empty($the_client_matter_id)) {
		$query_string .= "SELECT * FROM application WHERE ";
		$query_string .= " client_matter_id ILIKE :client_matter_id_binder";
		$tmp_results = $dd_db_handle->prepare($query_string);
		$the_client_matter_id = '%' . $the_client_matter_id . '%';
		$tmp_results->bindParam(':client_matter_id_binder', $the_client_matter_id, PDO::PARAM_STR);
	} else {
		$query_string .= "SELECT * FROM application WHERE id = :application_id_binder";
		$tmp_results = $dd_db_handle->prepare($query_string);
		$tmp_results->bindParam(':application_id_binder', $the_application_id, PDO::PARAM_INT);
	}
	$exec_bool = $tmp_results->execute();
	if (!$exec_bool) {
		$err_array =  $tmp_results->errorInfo();
		return(sprintf("Error: SQLSTATE=\"%s\" Error Message=\"%s\"",
			$err_array[0], $err_array[2]));
	}

	$rows = $tmp_results->fetchAll();
	$tmp_results->closeCursor();

// APPS

	$row_is_odd = TRUE;

	foreach ($rows as $row) {
		$middle_t_string .= "\t<TR class=\"" . ($row_is_odd ? "dd_tr_odd" : "dd_tr_even") . "\">";
		$row_is_odd = !$row_is_odd;
		$the_dropdowns = array();
		$the_dropdowns["Update Application Information"] =  sprintf("update_application?form_id=%s", urlencode($row['id']));
		$the_dropdowns["Show All Updates"] = sprintf("matter_update?form_client=%s&form_matter=%s&form_application_id=%s", urlencode($row['client']), urlencode($row['matter']), urlencode($row['id']));
		$the_dropdowns["Enter Update"] = sprintf("matter_update?form_client=%s&form_matter=%s&form_application_id=%s", urlencode($row['client']), urlencode($row['matter']), urlencode($row['id']));

		if (empty($row['pat_no'])) {
//			$add_inventor_form_str = dd_form_start("add_inventor_to_application", "POST");
			$add_inventor_form_str = "add_inventor_to_application?";
			$add_inventor_form_str .= "form_application_id=" . urlencode($row['id']);
			$add_inventor_form_str .= "&";
			$add_inventor_form_str .= "form_client=" . urlencode($row['client']);
			$add_inventor_form_str .= "&";
//			$add_inventor_form_str .= "form_application_title\" name=\"form_application_title\" value=\"%s\">", $row['application_title']);
			$add_inventor_form_str .= "form_matter=" . urlencode($row['matter']);
			$the_dropdowns["Add Inventor"] = $add_inventor_form_str;
		}

		$middle_t_string .= dd_td_default(dd_dropdown_button("Action", $the_dropdowns));
		// APP HERE
/*
			sprintf("<UL><LI>%s</LI><LI>%s</LI>",
				dd_anchor("Update Application Information",
					sprintf("update_application?form_id=%s", urlencode($row['id']))),
				dd_anchor("Enter Update",
					sprintf("matter_update?form_client=%s&form_matter=%s&form_application_id=%s",
						urlencode($row['client']), urlencode($row['matter']),
						urlencode($row['id'])))), "left");
*/
		$middle_t_string .= sprintf("\t<TD>%s</TD>", $row['client']);
		$middle_t_string .= dd_td_default($row['matter']);
		$middle_t_string .= dd_td_default($row['client_matter_id']);
		$the_inventor_rows = dd_get_applicaton_inventors($row['id']);
		//$middle_string .= dd_td_default(
		$middle_t_string .= sprintf("<TD>%s</TD>", dd_applicaton_inventor_list($the_inventor_rows));
		$middle_t_string .= dd_td_default_nobr($application_types[$row['application_type_id']]);
		$middle_t_string .= sprintf("<TD>%s</TD>", $row['application_title']);
		$middle_t_string .= dd_td_default_nobr(dd_date_to_DD_Month_YYYY($row['file_by_date']));
		$middle_t_string .=  dd_td_default_nobr($row['bar_date'] == null ?
			"NONE" : dd_date_to_DD_Month_YYYY($row['bar_date']));
		$middle_t_string .=  dd_td_default_nobr($row['filing_date'] == null ?
			"NONE" : dd_date_to_DD_Month_YYYY($row['filing_date']));
		$middle_t_string .= dd_td_default_nobr($row['application_assigned_to']);
		$middle_t_string .= dd_td_default(strval($row['country_id']));

//		$middle_t_string .= sprintf("<TD>%s</TD>", strval($row['country_id']));
		$middle_t_string .= dd_td_default_nobr($row['application_no'] == null ?
			"UNASSIGNED" : $row['application_no']);
		$middle_t_string .= dd_td_default_nobr($row['pat_app_pub_no'] == null ?
			"UNPUBLISHED" : $row['pat_app_pub_no']);
		$middle_t_string .= dd_td_default_nobr($row['pat_no'] == null ?
			"UNISSUED" : $row['pat_no']);
		$middle_t_string .= "<TD>";


		$middle_t_string .= "</TD>\n";
		$middle_t_string .= "</TR>\n";
	}


	return $middle_t_string;
} /* dd_get_application_html_table_rows */

/***********************************************************************/

function
dd_td_default_nobr($td_str, $the_align = "center")
{
	return sprintf("<TD align=\"%s\" valign=\"center\"><NOBR>%s</NOBR></TD>", 
		$the_align, $td_str);
} /* dd_td_default_nobr */

/***********************************************************************/

function
dd_td_default($td_str, $the_align = "center")
{
	return sprintf("<TD align=\"%s\" valign=\"center\">%s</TD>", 
		$the_align, $td_str);
} /* dd_td_default */

/***********************************************************************/

function
dd_th_default($td_str, $the_align = "center")
{
	return sprintf("<TH align=\"%s\" valign=\"center\">%s</TH>", 
		$the_align, $td_str);
} /* dd_th_default */

/***********************************************************************/

function
dd_td_with_bg_color($td_str, $the_color = "#000000", $the_align = "center")
{
	return sprintf("<TD bgcolor=\"%s\" align=\"%s\" valign=\"center\">%s</TD>", 
		$the_color, $the_align, $td_str);
} /* dd_td_with_bg_color */

/***********************************************************************/

function
dd_get_applicaton_inventors($the_application_id)
{
	global $dd_db_handle;

	$query_string = "";
	$query_string .= "SELECT * FROM inventor WHERE id IN";
	$query_string .= " (SELECT inventor_id FROM inventor_application_relationship";
	$query_string .= " WHERE application_id = :application_id_binder)";

	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->bindParam(':application_id_binder', $the_application_id, PDO::PARAM_INT);
	$exec_bool = $tmp_results->execute();

	$the_inventors = $tmp_results->fetchAll();
	$tmp_results->closeCursor();

	return($the_inventors);

/*
	if ($exec_bool) {
		$ret_string = "SUCCESS";
	} else {
		$err_array =  $tmp_results->errorInfo();
		$ret_string = sprintf("Error: SQLSTATE=\"%s\" Error Message=\"%s\"",
			$err_array[0], $err_array[2]);
	}
*/



	if ($ret_string != "SUCCESS") {
		return $ret_string;
	}

/*	while ($row = $tmp_results->fetchArray()) { */
	$odd = TRUE;
	while ($row = $tmp_results->fetch()) {
		/* $middle_t_string .= sprintf("<TR CLASS=\"%s\">\n", $odd ? "dd_tr_odd" : "dd_tr_even"); */
		$middle_t_string .= "<TR CLASS=\"dd_stripe\">\n";
		$middle_t_string .= "\t<TD>";
		$middle_t_string .= dd_anchor($row['first_name'], 
			sprintf("show_inventor?inventor_id=%d", $row['id']));
		$middle_t_string .= "</TD>";
		$middle_t_string .= "<TD>";
		if ($row['middle_name'] != "") {
			$middle_t_string .= dd_anchor($row['middle_name'], 
				sprintf("show_inventor?inventor_id=%d", $row['id']));
		}
		$middle_t_string .= "</TD>";
		$middle_t_string .= "<TD>";
		$middle_t_string .= dd_anchor($row['last_name'], 
			sprintf("show_inventor?inventor_id=%d", $row['id']));
		$middle_t_string .= "</TD>\n";
		$middle_t_string .= "</TR>\n";
		$odd = !$odd;
		$inventor_count++;
	}
/*
	$tmp_string .= "Client: " . $client_val . "<BR>\n";
	$tmp_string .= "Matter: " . $matter_val . "<BR>\n";
	$tmp_string .= "<BR>\n";
*/

	$tmp_string .= "<TABLE>\n";

	if ($inventor_count == 0) {
		$tmp_string .= "<TR>\n";
		$tmp_string .= "\t<TD>";
		$tmp_string .= "Client: " . $client_val . "<BR>\n";
		$tmp_string .= "Matter: " . $matter_val . "<BR>\n";
		$tmp_string .= "NO Inventor found";
		$tmp_string .= "</TD>\n";
		$tmp_string .= "</TR>\n";
	} else if ($inventor_count == 1) {
		$tmp_string .= "<TR>\n";
		$tmp_string .= "\t<TD colspan=\"3\">";
		$tmp_string .= "Client: " . $client_val . "<BR>\n";
		$tmp_string .= "Matter: " . $matter_val . "<BR>\n";
		$tmp_string .= "Sole Inventor";
		$tmp_string .= "</TD>\n";
		$tmp_string .= "</TR>\n";
	}

	$tmp_string .= "<TR>\n";
	$tmp_string .= "\t<TD colspan=\"3\">";
	$tmp_string .= "Client: " . $client_val . "<BR>\n";
	$tmp_string .= "Matter: " . $matter_val . "<BR>\n";
	$tmp_string .= "<CENTER>Inventors:</CENTER>\n";
	$tmp_string .= "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= "<TR CLASS=\"dd_bold\">\n";
	$tmp_string .= "\t<TD>First Name</TD><TD>Middle Name</TD><TD>Last Name</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= "<TR><TD colspan=\"3\"><HR></TD></TR>\n";
	
	$tmp_string .= $middle_t_string;
	$tmp_string .= "</TABLE>\n";

	return $tmp_string;

	return "FOO";
} /* dd_get_applicaton_inventors */

/***********************************************************************/

function
dd_applicaton_inventor_list($the_inventor_rows)
{
	$ret_string = "";

	foreach ($the_inventor_rows as $row) {
		$tmp_string = "";
		if (null != $row['first_name']) {
			$tmp_string .= $row['first_name'];
		}
		if (null != $row['middle_name']) {
			$tmp_string .= " " . $row['middle_name'];
		}
		if (null != $row['last_name']) {
			$tmp_string .= " " . $row['last_name'];
		}
		$ret_string .= "<NOBR>";
		$ret_string .= dd_anchor($tmp_string, "show_inventor?inventor_id=" .  $row['id']);
		$ret_string .= "</NOBR>";
		$ret_string .= "<BR>\n";
	}

	return($ret_string);
} /* dd_applicaton_inventor_list */

/***********************************************************************/

function
dd_get_inventor_row($inventor_id)
{
	global	$dd_db_handle;
	$query_string = "";

/*	$query_string .= "SELECT * FROM inventor WHERE id = " . $inventor_id; */
	$query_string .= "SELECT * FROM inventor WHERE id = :inventor_id_binder";

	try {
		$tmp_results = $dd_db_handle->prepare($query_string);
		$tmp_results->bindParam(':inventor_id_binder', $inventor_id, PDO::PARAM_INT);
		$exec_bool = $tmp_results->execute();
		if ($exec_bool) {
			$ret_string = "SUCCESS";
		} else {
			$err_array =  $tmp_results->errorInfo();
			$ret_string = sprintf("Error: SQLSTATE=\"%s\" Error Message=\"%s\"",
				$err_array[0], $err_array[2]);

		}
/*		$tmp_results = $dd_db_handle->query($query_string); */
	} catch (Exception $e) {
		$ret_string = "Error: " . $e->getMessage();
	}

	if ($ret_string != "SUCCESS") {
		$row['error'] = $ret_string;
		return $row;
	}

/*	$row = $tmp_results->fetchArray(); */
	$row = $tmp_results->fetch();
	$tmp_results->closeCursor();

	return $row;
} /* dd_get_inventor_row */

/***********************************************************************/

function
dd_show_inventor_table($row)
{
	global	$globals;
	$tmp_string = "";
	$tr = "<TR CLASS=\"dd_stripe\">\n";

	$tmp_string .= "<DIV id=\"id_inventor_table_id\">\n";
//	$tmp_string .= sprintf("<FORM onClick=\"javascript:get_inventor_update_form('%s', '%s', '%s')\">\n",
//		$row['id'], "id_inventor_table_id", "foo=bar");

	$tmp_string .= "<FORM>\n";
	$tmp_string .= "<TABLE style=\"width:100%\">";

	$tmp_string .= $tr;
	$tmp_string .= "\t<TD align=\"right\">First Name:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['first_name'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= $tr;
	$tmp_string .= "\t<TD align=\"right\">Middle Name:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['middle_name'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= $tr;
	$tmp_string .= "\t<TD align=\"right\">Last Name:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['last_name'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= $tr;
	$tmp_string .= "\t<TD align=\"right\">Preferred Name:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['preferred_name'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= $tr;
	$tmp_string .= "\t<TD align=\"right\">Street Address:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['street_address'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= $tr;
	$tmp_string .= "\t<TD align=\"right\">Apartment ID:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['apartment_id'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= $tr;
	$tmp_string .= "\t<TD align=\"right\">City:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['city'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= $tr;
	$tmp_string .= "\t<TD align=\"right\">State:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['state'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= $tr;
	$tmp_string .= "\t<TD align=\"right\">Zip:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['zipcode_seven'];
	if ($row['zipcode_four'] != NULL) {
		$tmp_string .= "-" . $row['zipcode_four'];
	}
	$tmp_string .=  "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= $tr;
	$tmp_string .= "\t<TD align=\"right\">Home Phone:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['home_phone'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= $tr;
	$tmp_string .= "\t<TD align=\"right\">Work Phone:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['work_phone'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= $tr;
	$tmp_string .= "\t<TD align=\"right\">Cell Phone:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['cell_phone'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= $tr;
	$tmp_string .= "\t<TD align=\"right\">Email:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['email'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= $tr;
	$tmp_string .= "\t<TD align=\"right\">Client:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['client'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= $tr;
	$tmp_string .= $tr;
	$tmp_string .= "\t<TD align=\"right\">Citizenship:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['citizenship'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= "<TD colspan=\"2\" align=\"center\">";
//	$tmp_string .= "<INPUT TYPE=\"submit\" value=\"Change Inventor Info\">";
	$tmp_string .= sprintf("<BUTTON type=\"button\" class=\"input_button_class\" onclick=\"get_inventor_update_form('%s', '%s', '%s')\">Change Inventor Info</BUTTON>\n",
		$row['id'], "id_inventor_table_id", "foo=bar");
	$tmp_string .= "</TD>";
	$tmp_string .= "</TR>\n";
// RIGHT HERE
	$tmp_string .= "</TABLE>\n";
	$tmp_string .= "</FORM>\n";
	$tmp_string .= "</DIV>\n";


	return $tmp_string;
} /* dd_show_inventor_table */

/***********************************************************************/

function
dd_update_inventor_form($row)
{
	global	$globals;
	$form_string = "";

	$form_string .= dd_form_start("update_inventor", "POST");
	$form_string .= sprintf("<INPUT type=\"hidden\" name=\"form_id\" value=\"%s\">", $row['id']);
	$form_string .= "<TABLE> <!-- first table -->\n";

	$form_string .= "<TR class=\"dd_bold\">\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">Update Inventor</TD>\n";
	$form_string .= "</TR>\n";

	$the_client_array = dd_get_client_array();
	$the_country_array = dd_get_country_array();

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Client:</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_form_dropdown_selected("form_client", $the_client_array, $row['client']);
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">First Name:</TD>";
	$form_string .= "<TD align=\"left\">"
		. dd_input_text_with_size_and_default("form_first_name", 80, $row['first_name'], TRUE) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Middle Name:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size_and_default("form_middle_name", 80, $row['middle_name']) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Last Name:</TD>";
	$form_string .= "<TD align=\"left\">"
		. dd_input_text_with_size_and_default("form_last_name", 80, $row['last_name'], TRUE) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Suffix (e.g., Jr., III, etc.):</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size_and_default("form_suffix", 80, $row['suffix']) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Preferred Name:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size_and_default("form_preferred_name", 80, $row['preferred_name']) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Street Address:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size_and_default("form_street_address", 80, $row['street_address']) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Appartment ID/Unit/No.:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size_and_default("form_apartment_id", 80, $row['apartment_id']) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">City:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size_and_default("form_city", 80, $row['city']) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">State:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size_and_default("form_state", 80, $row['state']) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Zip Code (five):</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size_and_default("form_zipcode_seven", 80, $row['zipcode_seven']) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Zip Code (four):</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size_and_default("form_zipcode_four", 80, $row['zipcode_four']) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Time Zone:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size_and_default("form_timezone", 80, $row['timezone']) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Home Phone:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size_and_default("form_home_phone", 80, $row['home_phone']) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Cell Phone:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size_and_default("form_cell_phone", 80, $row['cell_phone']) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Work Phone:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size_and_default("form_work_phone", 80, $row['work_phone']) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Email Address:</TD>";
	$form_string .= "<TD align=\"left\">" . dd_input_text_with_size_and_default("form_email", 80, $row['email']) .  "</TD>\n";
	$form_string .= "</TR>\n";

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"right\">Citizenship:</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_form_dropdown_selected("form_citizenship", $the_country_array, $row['citizenship']);
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";

	/* SUBMIT */

	$form_string .= "<TR>\n";
	$form_string .= "<TD align=\"center\" colspan=\"2\">";
	$form_string .= "<BR><INPUT TYPE=\"submit\" VALUE=\"Update Inventor Information\">";
	$form_string .= "</TD>\n";
	$form_string .= "</TR>\n";
	$form_string .= "</TABLE> <!-- first table -->\n";
	$form_string .= "</FORM>\n";

	return $form_string;
} // dd_update_inventor_form

/***********************************************************************/

function
dd_applications_of_inventor($inventor_id)
{
	global	$dd_db_handle;
	$tmp_string = "";
	$query_string = "";

	$query_string .= "SELECT application_id FROM inventor_application_relationship WHERE";
	$query_string .= " inventor_id = :inventor_id_binder";
	
	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->bindParam(':inventor_id_binder', $inventor_id, PDO::PARAM_INT);
	$exec_bool = $tmp_results->execute();
	$application_rows = $tmp_results->fetchALL();
	$tmp_results->closeCursor();

	return($application_rows);
} /* dd_applications_of_inventor */

/***********************************************************************/

function
dd_show_applications_of_inventor($inventor_id)
{
	$ret_string = "";

	$applications_of_inventor = dd_applications_of_inventor($inventor_id);

	$ret_string .= "<TABLE>\n";
	$ret_string .= dd_application_table_header();

	foreach ($applications_of_inventor as $application_row) {
		$ret_string .= dd_get_application_html_table_rows(null, null, $application_row['application_id'], null, null);
	}

	$ret_string .= "</TABLE>\n";

	return $ret_string;
} /* dd_show_applications_of_inventor */

/***********************************************************************/

function
dd_matters_of_inventor($inventor_id, $client)
{
	global	$dd_db_handle;
	$tmp_string = "";
	$query_string = "";

	$query_string .= "SELECT * FROM inventor_application_relationship WHERE";
	$query_string .= " inventor_id = :inventor_id_binder";



	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->bindParam(':inventor_id_binder', $inventor_id, PDO::PARAM_INT);
	$exec_bool = $tmp_results->execute();
		if ($exec_bool) {
			$ret_string = "SUCCESS";
		} else {
			$err_array =  $tmp_results->errorInfo();
			$ret_string = sprintf("Error: SQLSTATE=\"%s\" Error Message=\"%s\"",
				$err_array[0], $err_array[2]);

		}

	if ($ret_string != "SUCCESS") {
		return $ret_string;
	}

	$tmp_string .= "<TABLE>\n";
	$tmp_string .= "<TR>\n";
	$tmp_string .= "<TD>Client</TD><TD>Matter</TD>\n";
	$tmp_string .= "</TR>\n";

/*	while ($row = $tmp_results->fetchArray()) { */
	while ($row = $tmp_results->fetch()) {
		$tmp_string .= "<TR>\n";
		$tmp_string .= "\t<TD>" . $row['client'] . "</TD>";
		$tmp_string .= "<TD>" . dd_anchor($row['matter'],
			sprintf("show_matter?client=%s&matter=%s",
				urlencode($client), urlencode($row['matter'])));
		$tmp_string .= "</TD>\n";
		$tmp_string .= "</TR>\n";
	}

	$tmp_results->closeCursor();

	$tmp_string .= "</TABLE>\n";

	return $tmp_string;
} /* dd_matters_of_inventor */


/***********************************************************************/

function
dd_get_client_inventor_id_array($client_val)
{
	global	$dd_db_handle;
	$inventor_array = array();
	$query_string = "";

	$query_string .= "SELECT id, first_name, middle_name, last_name ";
	$query_string .= " FROM inventor WHERE client = :client_binder ORDER BY last_name ASC";
	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->bindParam(':client_binder', $client_val, PDO::PARAM_STR);
	$exec_bool = $tmp_results->execute();

	$rows = $tmp_results->fetchAll();
	$tmp_results->closeCursor();

	foreach ($rows as $row) {
		/* html/form value ,/= what is shown in dropdown */
		/*
		$inventor_array[$row["id"]] = empty($row["middle_name"]) ?
			$row["first_name"] . " " . $row["last_name"] :
			$row["first_name"] . " " . $row["middle_name"] . " " . $row["last_name"];
		*/
		$inventor_array[$row["id"]] = empty($row["middle_name"]) ?
			$row["last_name"] . ", " . $row["first_name"] :
			$row["last_name"] . ", " . $row["first_name"] . " " . $row["middle_name"];
	}

	return $inventor_array;
} /* dd_get_client_inventor_array */

/***********************************************************************/

function
dd_get_client_matter($client, $matter)
{
	global $dd_db_handle;
	$query_string = "";

/*
	$query_string .= "SELECT * FROM matter WHERE client = '" . $client . "'";
	$query_string .= " AND matter = '" . $matter . "'";
*/

	$query_string .= "SELECT * FROM matter WHERE client = :client_binder";
	$query_string .= " AND matter = :matter_binder";

	try {
/*		$tmp_results = $dd_db_handle->query($query_string); */
		$tmp_results = $dd_db_handle->prepare($query_string);
		$tmp_results->bindParam(':client_binder', $client, PDO::PARAM_STR);
		$tmp_results->bindParam(':matter_binder', $matter, PDO::PARAM_STR);
		$exec_bool = $tmp_results->execute();
		if ($exec_bool) {
			$ret_string = "SUCCESS";
		} else {
			$err_array =  $tmp_results->errorInfo();
			$ret_string = sprintf("Error: SQLSTATE=\"%s\" Error Message=\"%s\"",
				$err_array[0], $err_array[2]);

		}
	} catch (Exception $e) {
		$ret_string = "Error: " . $e->getMessage();
	}

	if ($ret_string != "SUCCESS") {
		$row['error'] = $ret_string;
		return $row;
	}

/*	$row = $tmp_results->fetchArray(); */
	$row = $tmp_results->fetch();
	$tmp_results->closeCursor();

	return $row;
} /* dd_get_client_matter */

/***********************************************************************/

function
dd_show_client_matter($row)
{
	global $dd_local_timezone;
	$tmp_str = "";

	$tmp_str .= "<TABLE>\n";

	$tmp_string .= "<TR>\n";
	$tmp_string .= "\t<TD align=\"right\">Client:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['client'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= "<TR>\n";
	$tmp_string .= "\t<TD align=\"right\">Matter:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['matter'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= "<TR>\n";
	$tmp_string .= "\t<TD align=\"right\">Client's Matter ID:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['client_matter_id'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= "<TR>\n";
	$tmp_string .= "\t<TD align=\"right\">Description:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['client_matter_id'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= "<TR>\n";
	$tmp_string .= "\t<TD align=\"right\">Client's title:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['client_matter_title'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= "<TR>\n";
	$tmp_string .= "\t<TD align=\"right\">Matter title:</TD>";
	$tmp_string .= "<TD align=\"left\">" . $row['matter_title'] . "</TD>\n";
	$tmp_string .= "</TR>\n";

	$tmp_string .= "<TR>\n";
	$tmp_string .= "\t<TD align=\"right\">File by date:</TD>";
	if ($row['file_by_date'] > 0) {
		$tmp_string .= "<TD align=\"left\">" . dd_day_month_year($row['file_by_date']) . "</TD>\n";
	} else {
		$tmp_string .= "<TD align=\"left\">No File by Date</TD>\n";
	}


	$tmp_string .= "<TR>\n";
	$tmp_string .= "\t<TD align=\"right\">Filing date:</TD>";
	if ($row['filing_date'] > 0) {
		$tmp_string .= "<TD align=\"left\">" . dd_day_month_year($row['filing_date']) . "</TD>\n";
	} else {
		$tmp_string .= "<TD align=\"left\">No File by Date</TD>\n";
	}

	$tmp_string .= "<TR>\n";
	$tmp_string .= "\t<TD align=\"right\">Filing date:</TD>";
	if ($row['bar_date'] > 0) {
		$tmp_string .= "<TD align=\"left\">" . dd_day_month_year($row['bar_date']) . "</TD>\n";
	} else {
		$tmp_string .= "<TD align=\"left\">No Bar Date</TD>\n";
	}

	$tmp_string .= "<TR>\n";
	$tmp_string .= "\t<TD align=\"right\">Next action due date:</TD>";
	if ($row['bar_date'] > 0) {
		$tmp_string .= "<TD align=\"left\">" . dd_day_month_year($row['next_action_due']) . "</TD>\n";
	} else {
		$tmp_string .= "<TD align=\"left\">No Next Action Due Date</TD>\n";
	}

	$tmp_string .= "<TR>\n";
	$tmp_string .= "\t<TD align=\"right\">Total Budget (in dollars):</TD>";
	$tmp_string .= "<TD align=\"left\">";
	$tmp_string .= ($row['total_budget'] > 0) ? $row['total_budget'] : "No total budget";
	$tmp_string .= "</TD>";

	$tmp_string .= "<TR>\n";
	$tmp_string .= "\t<TD align=\"right\">User Budget (in dollars):</TD>";
	$tmp_string .= "<TD align=\"left\">";
	$tmp_string .= ($row['user_budget'] > 0) ? $row['user_budget'] : "No user budget";
	$tmp_string .= "</TD>";

	$tmp_string .= "<TR>\n";
	$tmp_string .= "\t<TD align=\"right\">Next action:</TD>";
	$tmp_string .= dd_next_matter_action($row['next_action_id']);
	$tmp_string .= "</TR>\n";

	$tmp_string .= "<TR>\n";
	$tmp_string .= "\t<TD align=\"right\">Matter type:</TD>";
	$tmp_string .= dd_show_matter_action($row['matter_type_id']);
	$tmp_string .= "</TR>\n";

	return $tmp_str;
} /* dd_show_client_matter */

/***********************************************************************/

function
dd_show_matter($the_row)
{
	$tmp_str = "";

	$tmp_str .= "<TABLE>\n";

	$tmp_str .= "<TR CLASS=\"dd_stripe\">\n";
	$tmp_str .= "\t<TD align=\"right\">Client:</TD>";
	$tmp_str .= "<TD align=\"left\">" . $the_row['client'] . "</TD>\n";
	$tmp_str .= "</TR>\n";

	$tmp_str .= "<TR CLASS=\"dd_stripe\">\n";
	$tmp_str .= "\t<TD align=\"right\">Matter:</TD>";
	$tmp_str .= "<TD align=\"left\">" . $the_row['matter'] . "</TD>\n";
	$tmp_str .= "</TR>\n";

	$tmp_str .= "<TR CLASS=\"dd_stripe\">\n";
	$tmp_str .= "\t<TD align=\"right\">Client's Matter ID:</TD>";
	$tmp_str .= "<TD align=\"left\">" . $the_row['client_matter_id'] . "</TD>\n";
	$tmp_str .= "</TR>\n";

	$tmp_str .= "<TR CLASS=\"dd_stripe\">\n";
	$tmp_str .= "\t<TD align=\"right\">Description:</TD>";
	$tmp_str .= "<TD align=\"left\">" . $the_row['description'] . "</TD>\n";
	$tmp_str .= "</TR>\n";

	$tmp_str .= "<TR CLASS=\"dd_stripe\">\n";
	$tmp_str .= "\t<TD align=\"right\">Title given by client:</TD>";
	$tmp_str .= "<TD align=\"left\">" . $the_row['client_matter_title'] . "</TD>\n";
	$tmp_str .= "</TR>\n";

	$tmp_str .= "<TR CLASS=\"dd_stripe\">\n";
	$tmp_str .= "\t<TD align=\"right\">Matter Title:</TD>";
	$tmp_str .= "<TD align=\"left\">" . $the_row['matter_title'] . "</TD>\n";
	$tmp_str .= "</TR>\n";

	$tmp_str .= "<TR CLASS=\"dd_stripe\">\n";
	$tmp_str .= "\t<TD align=\"right\">File by Date:</TD>";
	if ($the_row['file_by_date'] != NULL && $the_row['file_by_date'] > 0) {
		$tmp_str .= "<TD align=\"left\">" .  dd_day_month_year($the_row['file_by_date']);
		$tmp_str .= "</TD>\n";
	} else {
		$tmp_str .= "<TD align=\"left\"></TD>";
	}
	$tmp_str .= "</TR>\n";

	$tmp_str .= "<TR CLASS=\"dd_stripe\">\n";
	$tmp_str .= "\t<TD align=\"right\">Filing Date:</TD>";
	if ($the_row['filing_date'] != NULL && $the_row['filing_date'] > 0) {
		$tmp_str .= "<TD align=\"left\">" .  dd_day_month_year($the_row['filing_date']);
		$tmp_str .= "</TD>\n";
	} else {
		$tmp_str .= "<TD align=\"left\"></TD>";
	}
	$tmp_str .= "</TR>\n";

	$tmp_str .= "<TR CLASS=\"dd_stripe\">\n";
	$tmp_str .= "\t<TD align=\"right\">Bar Date:</TD>";
	if ($the_row['bar_date'] != NULL && $the_row['bar_date'] > 0) {
		$tmp_str .= "<TD align=\"left\">" .  dd_day_month_year($the_row['bar_date']);
		$tmp_str .= "</TD>\n";
	} else {
		$tmp_str .= "<TD align=\"left\"></TD>";
	}
	$tmp_str .= "</TR>\n";

	$tmp_str .= "<TR CLASS=\"dd_stripe\">\n";
	$tmp_str .= "\t<TD align=\"right\">Next Action Due Date:</TD>";
	if ($the_row['next_action_due'] != NULL && $the_row['next_action_due'] > 0) {
		$tmp_str .= "<TD align=\"left\">" .  dd_day_month_year($the_row['next_action_due']);
		$tmp_str .= "</TD>\n";
	} else {
		$tmp_str .= "<TD align=\"left\"></TD>";
	}
	$tmp_str .= "</TR>\n";

	$tmp_str .= "<TR CLASS=\"dd_stripe\">\n";
	$tmp_str .= "\t<TD align=\"right\">Total Budget:</TD>";
	if ($the_row['total_budget'] != NULL && $the_row['total_budget'] > 0) {
		$tmp_str .= "<TD align=\"left\">" .  $the_row['total_budget'];
		$tmp_str .= "</TD>\n";
	} else {
		$tmp_str .= "<TD align=\"left\">UNKNOWN</TD>";
	}
	$tmp_str .= "</TR>\n";

	$tmp_str .= "<TR CLASS=\"dd_stripe\">\n";
	$tmp_str .= "\t<TD align=\"right\">User Budget:</TD>";
	if ($the_row['user_budget'] != NULL && $the_row['user_budget'] > 0) {
		$tmp_str .= "<TD align=\"left\">" .  $the_row['user_budget'];
		$tmp_str .= "</TD>\n";
	} else {
		$tmp_str .= "<TD align=\"left\">UNKNOWN</TD>";
	}
	$tmp_str .= "</TR>\n";

	$tmp_str .= "<TR CLASS=\"dd_stripe\">\n";
	$tmp_str .= "\t<TD align=\"right\">Matter Type:</TD>";
	if ($the_row['matter_type_id'] != NULL) { 
		$tmp_str .= "<TD align=\"left\">" .  dd_get_matter_type($the_row['matter_type_id']);
		$tmp_str .= "</TD>\n";
	} else {
		$tmp_str .= "<TD align=\"left\">UNKNOWN</TD>";
	}
	$tmp_str .= "</TR>\n";



	return $tmp_str;
} /* dd_show_matter */

/***********************************************************************/

function
dd_get_matter_type($matter_type_id)
{
	/* need to get the string from the database */

	return $matter_type_id;
} /* dd_get_matter_type */

/***********************************************************************/

function
dd_get_user_row($username)
{
	global $dd_db_handle;

/*	$query_string = "SELECT * FROM user WHERE user_name = '" . $username . "'"; */
	$query_string = "SELECT * FROM user WHERE user_name = :username_binder";

	try {
/*		$tmp_results = $dd_db_handle->query($query_string); */
		$tmp_results = $dd_db_handle->prepare($query_string);
		$tmp_results->bindParam(':username_binder', $username, PDO::PARAM_STR);
		$exec_bool = $tmp_results->execute();
		if ($exec_bool) {
			$ret_string = "SUCCESS";
		} else {
			$err_array =  $tmp_results->errorInfo();
			$ret_string = sprintf("Error: SQLSTATE=\"%s\" Error Message=\"%s\"",
				$err_array[0], $err_array[2]);

		}
	} catch (Exception $e) {
		$ret_string = "Error: " . $e->getMessage();
	}

	if ($ret_string != "SUCCESS") {
		$row['error'] = $ret_string;
		return $row;
	}

/*	$row = $tmp_results->fetchArray(); */

	$row = $tmp_results->fetch();
	$tmp_results->closeCursor();

	return $row;
} /* dd_get_user_row */

/***********************************************************************/

function
dd_copyright_statement($cr_statement_type)
{
	$for_html = strcmp($cr_statement_type, "HTML") == 0;
	$the_year = date("Y");
	$copyright_statement = "";
	$copyright_statement .= sprintf("Copyright %s 2019", $for_html ? "&copy;" : "(C)");
	$copyright_statement .= (strcmp("2019", $the_year) == 0) ? " " : "-" . $the_year . " ";
	$copyright_statement .= "Ian M. Fink.  All Rights Reserved.";

	return $for_html ?  $copyright_statement : "<!-- " . $copyright_statement . " -->\n";
} /* dd_copyright_commnented_statement */

/***********************************************************************/

function
dd_input_text_with_size($dd_name, $dd_size)
{
	if (empty($_POST[$dd_name])) {
		return sprintf("<INPUT type=\"text\" name=\"%s\" size=\"%d\">", $dd_name, $dd_size);
	} else {
		return sprintf("<INPUT type=\"text\" name=\"%s\" value=\"%s\" size=\"%d\">",
			$dd_name, $_POST[$dd_name], $dd_size);
	}
} /* dd_input_text_with_size */

/***********************************************************************/

function
dd_input_text_with_size_and_default($dd_name, $dd_size, $the_default, $is_required = FALSE)
{
	$is_required_str = $is_required ? " REQUIRED" : "";

	if (empty($the_default)) {
		return sprintf("<INPUT type=\"text\" name=\"%s\" size=\"%d\"%s>", $dd_name, $dd_size,
			$is_required_str);
	} else {
		return sprintf("<INPUT type=\"text\" name=\"%s\" value=\"%s\" size=\"%d\"%s>",
			$dd_name, $the_default, $dd_size, $is_required_str);
	}
} /* dd_input_text_with_size */

/***********************************************************************/

function
dd_input_text_required_with_size($dd_name, $dd_size)
{
	if (empty($_POST[$dd_name])) {
		return sprintf("<INPUT type=\"text\" name=\"%s\" size=\"%d\" REQUIRED>", $dd_name, $dd_size);
	} else {
		return sprintf("<INPUT type=\"text\" name=\"%s\" value=\"%s\" size=\"%d\" REQUIRED>",
			$dd_name, $_POST[$dd_name], $dd_size);
	}
} /* dd_input_text_required_with_size */

/***********************************************************************/

function
dd_get_country_ids()
{
	global $dd_db_handle;

	$country_ids = array();

	$country_ids["US"] = "United States (US)";
	
	$query_string = "";
	$query_string .= "SELECT long_name, short_name FROM country ORDER BY long_name ASC";
	$tmp_results = $dd_db_handle->prepare($query_string);
	$exec_bool = $tmp_results->execute();
	$rows = $tmp_results->fetchALL();
	$tmp_results->closeCursor();

	foreach ($rows as $row) {
		if (!strcmp("US", $row["short_name"])) {
			continue;
		}
		/* html/form value ,/= what is shown in dropdown */
		$country_ids[$row["short_name"]] = $row["long_name"] . " (" . $row["short_name"] . ")";
	}
	
	return $country_ids;
} /* dd_get_country_ids */

/***********************************************************************/

function
dd_get_users_for_dropdown()
{
	global $dd_db_handle;

	$users = array();

//	$users["DD_ERROR"] = "Select One";
	$users["UNASSIGNED"] = "UNASSIGNED";
	
	$query_string = "";
	$query_string .= "SELECT username, first_name, middle_name, last_name, preferred_name";
	$query_string .= " FROM user_t ORDER BY last_name ASC";
	$tmp_results = $dd_db_handle->prepare($query_string);
	$exec_bool = $tmp_results->execute();
	$rows = $tmp_results->fetchALL();
	$tmp_results->closeCursor();

	if (!$exec_bool) {
			$users["ERROR"] = "SQL ERROR";
	}

	foreach ($rows as $row) {
		if (!strcmp($row["username"], "UNASSIGNED")) {
			continue;
		}
		$tmp = $row["first_name"];
		if (!empty($row["preferred_name"])) {
			$tmp .= " \"" . $row["preferred_name"] . "\"";
		}
		if (!empty($row["preferred_name"])) {
			$tmp .= " " . $row["middle_name"];
		}
		$tmp .= " " . $row["last_name"] . " (" . $row["username"] . ")";
		/* html/form value ,/= what is shown in dropdown */
		$users[$row["username"]] = $tmp;
	}

	return $users;
} /* dd_get_users_for_dropdown */

/***********************************************************************/

function
dd_columns_utilized($all_columns)
{
/* ATTENTION:  this assumes that the form "names" are of:  "form_" . $column */
	$columns_utilized = array();

	foreach ($all_columns as $column) {
		if (!empty($_POST["form_" . $column])) {
			array_push($columns_utilized, $column);
		}
	}

	return $columns_utilized;
} /* dd_columns_utilized */

/***********************************************************************/

function
dd_date_plus_months($the_date, $num_months)
{
	list($the_year_str, $the_month_str, $the_day_str) = explode($the_date, "-");
	$the_month_int = intval($the_month_str) + $num_months;

	if ($the_month_int > 12) {
		$the_year_int = intval($the_year_str);
		$the_year_int++;
		$the_month_int -= 12;
		$ret_string = sprintf("%02d-%02d-%s", $the_year_int, $the_month_int, $the_day_str);
	} else {
		$ret_string = sprintf("%s-%02d-%s", $the_year_str, $the_month_int, $the_day_str);
	}

	return $ret_string;
} /* dd_date_plus_months */

/***********************************************************************/

function
dd_date_OR_dropdown_date($column_name, $the_size, $the_maxlength)
{
	$ret_string = "";
	$the_years_array = dd_generate_years_array();
	$the_months_array = dd_generate_months_array();
	$the_days_array = dd_generate_days_array();

	$ret_string .= sprintf("<INPUT type=\"text\" name=\"%s\"", $column_name);
	if ($the_size > 0) {
		$ret_string .= sprintf(" size=\"%d\"", $the_size); 
	}
	if ($the_maxlength > 0) {
		$ret_string .= sprintf(" maxlength=\"%d\"", $the_maxlength);
	}
	
	$ret_string .= "> OR ";
	
	$ret_string .= dd_form_dropdown("form_" . $column_name . "_year", $the_years_array);
	$ret_string .= dd_form_dropdown("form_" . $column_name . "_month", $the_months_array);
	$ret_string .= dd_form_dropdown("form_" . $column_name . "_day", $the_days_array);

	return $ret_string;
} /* dd_date_OR_dropdown_date */

/***********************************************************************/

function
dd_date_OR_dropdown_date_with_default($column_name, $the_size, $the_maxlength, $the_default)
{
	$ret_string = "";
	$the_years_array = dd_generate_years_array();
	$the_months_array = dd_generate_months_array();
	$the_days_array = dd_generate_days_array();

	$ret_string .= sprintf("<INPUT type=\"text\" name=\"%s\" value=\"%s\"",
		$column_name, $the_default);
	if ($the_size > 0) {
		$ret_string .= sprintf(" size=\"%d\"", $the_size); 
	}
	if ($the_maxlength > 0) {
		$ret_string .= sprintf(" maxlength=\"%d\"", $the_maxlength);
	}
	
	$ret_string .= "> OR ";
	
	$ret_string .= dd_form_dropdown("form_" . $column_name . "_year", $the_years_array);
	$ret_string .= dd_form_dropdown("form_" . $column_name . "_month", $the_months_array);
	$ret_string .= dd_form_dropdown("form_" . $column_name . "_day", $the_days_array);

	return $ret_string;
} /* dd_date_OR_dropdown_date_with_default */

/***********************************************************************/

function
dd_date_OR_dropdown_date_with_default_and_DOM_id($column_name, $the_size, $the_maxlength, $the_default, $the_DOM_id)
{
	global	$globals;
	$ret_string = "";
	$the_years_array = dd_generate_years_array();
	$the_months_array = dd_generate_months_array();
	$the_days_array = dd_generate_days_array();

	$ret_string .= sprintf("<INPUT type=\"text\" name=\"%s\" value=\"%s\" id=\"%s\"",
		"form_" . $column_name, $the_default, $the_DOM_id);
	if ($the_size > 0) {
		$ret_string .= sprintf(" size=\"%d\"", $the_size); 
	}
	if ($the_maxlength > 0) {
		$ret_string .= sprintf(" maxlength=\"%d\"", $the_maxlength);
	}
	
	$ret_string .= "> \nOR ";
	$ret_string .= sprintf("<BUTTON type=\"button\" class=\"input_button_class\" onclick=\"assign_today('%s', '%s')\">Today</BUTTON> OR ", $the_DOM_id, $globals->date_now->format("Y-m-d"));

	
	$ret_string .= dd_form_dropdown("form_" . $column_name . "_year", $the_years_array);
	$ret_string .= dd_form_dropdown("form_" . $column_name . "_month", $the_months_array);
	$ret_string .= dd_form_dropdown("form_" . $column_name . "_day", $the_days_array);

	return $ret_string;
} /* dd_date_OR_dropdown_date_with_default */

/***********************************************************************/

function
dd_format_US_app_no($the_app_no)
{
	$first_two = substr($the_app_no, 0, 2);
	$first_three = substr($the_app_no, 3, 3);
	$second_three = substr($the_app_no, 5, 3);

	return sprintf("%s/%s,%s", $first_two, $first_three, $second_three);
} // dd_format_US_app_no

/***********************************************************************/

function
dd_get_application_row(&$db_handle, $the_id)
{
	$query_string = "";
	$query_string .= "SELECT * FROM application WHERE id = :id_binder";

	$tmp_results = $db_handle->prepare($query_string);
	$tmp_results->bindValue(':id_binder', $the_id, PDO::PARAM_INT);
	$exec_bool = $tmp_results->execute();

	$the_rows = $tmp_results->fetchAll();
	$tmp_results->closeCursor();

	return $the_rows[0];
} // dd_get_application_row

/***********************************************************************/

function
dd_get_application_ids($client_val, $matter_val)
{
	global $dd_db_handle;
	$query_string = "";

	$query_string .= "SELECT id, application_type_id, application_no from application ";
	$query_string .= " WHERE client = :client_binder AND matter = :matter_binder";

	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->bindValue(':client_binder', $client_val, PDO::PARAM_STR);
	$tmp_results->bindValue(':matter_binder', $matter_val, PDO::PARAM_STR);
	$exec_bool = $tmp_results->execute();

	if ($exec_bool) {
		$tmp_array = $tmp_results->fetchAll();
		$tmp_results->closeCursor();
		return $tmp_array;
	} else {
		return array();
	}
} /* dd_get_application_ids */

/***********************************************************************/

function
dd_get_OA_dashboard_rows($username_val)
{
	global $dd_db_handle;
	global $globals;
	$query_string = "";

	$query_string .= "SELECT *, file_by_date - current_date AS file_by_date_diff ";
	$query_string .= " FROM office_action WHERE ";
	$query_string .= " (filing_date IS null AND (filed IS null OR filed = FALSE)) ";
	$query_string .= " AND application_id IN ";
	$query_string .= " (SELECT id FROM application WHERE application_assigned_to = ";
	$query_string .= " :username_binder AND abandoned = FALSE) ORDER BY file_by_date_diff ASC";
	//$query_string .= " :username_binder AND abandoned = FALSE) ORDER BY oa_date ASC";
/*
	$query_string .= "SELECT * FROM office_action WHERE  filing_date IS null AND application_id ";
	$query_string .= " IN (SELECT id FROM application WHERE application_assigned_to = ";
	$query_string .= " :username_binder AND abandoned = FALSE) ORDER BY oa_date ASC";
*/

	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->bindValue(':username_binder', $username_val, PDO::PARAM_STR);
	$exec_bool = $tmp_results->execute();

	if ($exec_bool) {
		$tmp_array = $tmp_results->fetchALL();
		$tmp_results->closeCursor();
		return $tmp_array;
	} else {
		return array();
	}
} // dd_get_OA_rows

/***********************************************************************/

function
dd_get_application_dashboard_rows($username_val)
{
	global $dd_db_handle;
	global $globals;
	$query_string = "";

	$query_string .= "SELECT *, file_by_date - current_date AS file_by_date_diff FROM application WHERE filing_date IS null AND ";
	$query_string .= " application_assigned_to = :username_binder ORDER BY file_by_date_diff ASC";
//	$query_string .= "SELECT * FROM application WHERE filing_date IS null AND ";
//	$query_string .= " application_assigned_to = :username_binder ORDER BY file_by_date ASC";

	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->bindValue(':username_binder', $username_val, PDO::PARAM_STR);
	$exec_bool = $tmp_results->execute();

	if ($exec_bool) {
		$tmp_array = $tmp_results->fetchALL();
		$tmp_results->closeCursor();
		return $tmp_array;
	} else {
		return array();
	}
} // dd_get_application_dashboard_rows

/***********************************************************************/

function
dd_OA_dashboard($username_val, $tick_tock)
{
	global $dd_db_handle;
	global $globals;
	global $dd_time_zones;
	$interval_offset = 1;
	$ret_string = "";
	$query_string = "";

	date_default_timezone_set($dd_time_zones[$globals->current_user->timezone]);
	// HRHR
/*
	$ret_string .= "<!-- " . $globals->current_user->username . " -->\n";
	$ret_string .= "<!-- " . $dd_time_zones[$globals->current_user->timezone] . " -->\n";

	if (empty($globals->current_user)) {
		$ret_string .= "<!-- current_user is empty -->\n";
	}
*/


	$OA_rows = dd_get_OA_dashboard_rows($username_val);

	// $num_OA_rows = 0;
	$num_OA_rows = count($OA_rows);

	$ret_string .= "<DIV id=\"id_OA_dashboard_id\">\n";
	$ret_string .= "<TABLE>\n";



	$ret_string .= "\t<TR>\n";
	$ret_string .= "<TD colspan=\"8\" align=\"center\">";
	$ret_string .= "<B>Office Actions<BR>Assigned to: " . $username_val . "</B></TD>\n";
	$ret_string .= "</TR>\n";

	$ret_string .= dd_OA_dashboar_table_header($tick_tock);

	$row_is_odd = TRUE;

	foreach ($OA_rows as $row) {
/*
		// this should not be needed with the proper query and will screw up the OA row count
		if ($row['filed'] == TRUE) {
			continue;
		}
*/
		$num_columns = 0;
// TRY DATE
//		$the_oa_date = $row['oa_date'];
		$file_by_date_diff = $row['file_by_date_diff'];
		$the_oa_date = new DateTime($row['oa_date']);
		if (!empty($row['file_by_date'])) {
			$the_three_month_date = new DateTime($row['file_by_date']);
		} else {
			$the_three_month_date = dd_date_add_months($the_oa_date, 3);
		}
		$ret_string .= "\t<TR class=\"" . ($row_is_odd ? "dd_tr_odd" : "dd_tr_even") . "\">";
		$row_is_odd = !$row_is_odd;
		$ret_string .= dd_td_default($row['client']);
//		$the_url = sprintf("find_application?client=%s&matter=%s", urlencode($row['client']),
//			urlencode($row['matter']));
		$the_url = sprintf("update_office_action?form_id=%s", urlencode($row['id']));
		$ret_string .= dd_td_default(
			dd_tooltip( dd_anchor($row['matter'], $the_url), "View/Change OA Data"));
//		$ret_string .= dd_td_default($row['matter']);
		if ($row['allowable_subjectmatter']) {
			$ret_string .= dd_td_with_bg_color(
			dd_tooltip($row['oa_type'], "Allowable Subject Matter"), '#008000');
		} else if (!strcmp($row['oa_type'], "Restriction")) {
			// $ret_string .= dd_td_with_bg_color($row['oa_type'], '#995C00');
			$ret_string .= dd_td_with_bg_color($row['oa_type'], '#004D1A');
		} else {
			$ret_string .= dd_td_default($row['oa_type']); 
		}
//		$ret_string .= dd_td_default($row['oa_type']);
		$ret_string .= dd_td_default(empty($row['reply_type']) ? "UNDETERMINED" : $row['reply_type']);
		// OA Date
		$ret_string .= dd_td_default(dd_date_to_DD_Month_YYYY($the_oa_date));
//		$ret_string .= dd_td_default($the_oa_date->format("d F Y") . " " . $row['oa_date']);

		// Due Date
//		$little_date = new DateTime("2019-12-12"); /* for testing */
		$little_date = new DateTime();
		$little_date->setTimezone($globals->current_user->timezone_object);
//		$big_date = new DateTime(dd_date_add_months($the_oa_date, 3));
		if (!empty($row['file_by_date'])) {
			$big_date = new DateTime($row['file_by_date']);
		} else {
			$big_date = dd_date_add_months($the_oa_date, 3);
		}
		$big_date->setTimezone($globals->current_user->timezone_object);
		$interval = $little_date->diff($big_date);
//		$interval_days = $interval->d + $interval_offset;
		$interval_days = intval($interval->format("%R%a")); // NO interval_offset
//		$interval_days = intval($interval->format("%R%a")) + $interval_offset;
//		$ret_string .= dd_td_default(dd_date_to_DD_Month_YYYY(dd_date_add_months($the_oa_date, 3)));

/* work in stored procedure:  oa_reply_info_fn(oa_date)
 * returns "(<days left>,<next filing date>,<number of extensions>)"
 */

		$overdue = !strcmp(substr($file_by_date_diff, 0, 1), "-");
		$the_sign = $interval_days  < 0 ? "-" : "+";
//		if (!strcmp("-", $the_sign)) {
//		if ($interval_days < 0) {
		if ($overdue) {
			for ($i=4; $i<=6; $i++) {
				$next_deadline = dd_date_add_months($the_oa_date, $i);
//				$big_date = new DateTime($next_deadline);
				$big_date = clone $next_deadline;
				$interval = $little_date->diff($big_date);
//				$interval_days = $interval->d + $interval_offset;
				$interval_days = intval($interval->format("%R%a")) + $interval_offset;
				$the_sign = $interval_days < 0 ? "-" : "+";
//				$the_sign = $interval->format('%R');
				if (!strcmp("+", $the_sign)) {
					break;
				}
			}
			$ret_string .= dd_td_default(sprintf("%s <FONT color=\"#FF0000\">(Ext. %d)</FONT>",
				dd_date_to_DD_Month_YYYY(dd_date_add_months($the_oa_date, $i)), $i-3));
			$ret_string .= dd_td_default(sprintf("%d %s", $interval_days + $interval_offset, 
				($interval_days + $interval_offset) != 1 ? "days" : "day"));
		} else {
		/*
			$ret_string .= dd_td_default(dd_date_to_DD_Month_YYYY(
				dd_date_add_months($the_oa_date, 3)));
		*/
			$ret_string .= dd_td_default(dd_date_to_DD_Month_YYYY($big_date));
			if (0 == $interval_days) {
//			if (0 == ($interval_days - $interval_offset)) {
				$ret_string .= dd_td_default("<FONT color=\"#FF0000\">TODAY</FONT>");
			} else {
				$ret_string .= dd_td_default(sprintf("%d %s", $interval_days + $interval_offset,
					($interval_days + $interval_offset != 1 ? "days" : "day"))); 
			}
		}

//		$ret_string .= dd_td_default($row['oa_status']);
		$ret_string .= dd_td_default(
			dd_tooltip(
			dd_anchor($row['oa_status'],  
			sprintf("matter_update?form_client=%s&form_matter=%s&form_oa_id=%s",
			urlencode($row['client']), urlencode($row['matter']), urlencode($row['id'])))
			, "Update/Change Status")
			);
		$ret_string .= "\n</TR>\n";
	}

	$ret_string .= "\t<TR>\n";
	$ret_string .= "<TD colspan=\"8\" align=\"center\">";
	$ret_string .= "Number of OAs is <B>" . strval($num_OA_rows) . "</B></TD>\n";
	$ret_string .= "</TR>\n";

	$ret_string .= "</TABLE>\n";
	$ret_string .= "</DIV>\n";

	return $ret_string;
} /* dd_OA_dashboard */

/***********************************************************************/

function
dd_OA_dashboar_table_header($tick_tock)
{
	$ret_string = "";

	$ret_string .= "\t<TR class=\"dd_tr_header\">\n";
	$ret_string .= dd_td_default("Client");
	$ret_string .= dd_td_default("Matter");
	$ret_string .= dd_td_default("OA Type");
	$ret_string .= dd_td_default("Reply Type");
	$ret_string .= dd_td_default("OA Date");
	$ret_string .= dd_td_default("Due Date");
	$ret_string .= dd_td_default("Days to Due Date");
	$ret_string .= dd_td_default("Status");
	if ($tick_tock) {
		$ret_string .= dd_td_default("Updates");
	}

$ret_string .= "</TR>\n";

	return $ret_string;
} /* dd_OA_dashboar */

/***********************************************************************/

function
dd_application_dashboard($username_val, $tick_tock)
{
	global $dd_db_handle;
	global $globals;
	$ret_string = "";
	$query_string = "";
//	$postgres_date_diff = "select *, file_by_date - current_date AS time_diff FROM application";

	$application_rows = dd_get_application_dashboard_rows($username_val);
	$num_application_rows = count($application_rows);
	$application_type_array = dd_get_application_type_array();

	$ret_string .= "<DIV id=\"id_application_dashboard_id\">\n";
	$ret_string .= "<TABLE>\n";
	$ret_string .= "\t<TR><TD colspan=\"7\" align=\"center\">";
	$ret_string .= "<B>Applications<BR>Assigned to: " . $username_val . "</B></TD></TR>\n";
	$ret_string .= dd_application_dashboar_table_header($tick_tock);

	$row_is_odd = TRUE;

	foreach ($application_rows as $row) {
		$num_columns = 0;
		$the_oa_date = $row['oa_date'];
		$file_by_date_diff = $row['file_by_date_diff'];
		$the_three_month_date = dd_date_add_months($the_oa_date, 3);
		$ret_string .= "\t<TR class=\"" . ($row_is_odd ? "dd_tr_odd" : "dd_tr_even") . "\">";
		$row_is_odd = !$row_is_odd;
		$ret_string .= dd_td_default($row['client']);
		$the_url = sprintf("find_application?client=%s&matter=%s", urlencode($row['client']),
			urlencode($row['matter']));
		$ret_string .= dd_td_default(
			dd_tooltip( dd_anchor($row['matter'], $the_url), "View/Udpate Application Data"));
		$ret_string .= dd_td_default($application_type_array[$row['application_type_id']]);
		$ret_string .= dd_td_default(empty($row['file_by_date']) ? "UNKNOWN" :
			dd_date_to_DD_Month_YYYY($row['file_by_date']));
		$ret_string .= dd_td_default(empty($row['bar_date']) ? "NONE" :
			dd_date_to_DD_Month_YYYY($row['bar_date']));
		$ret_string .= dd_td_default(empty($row['foreign_filing_license']) ? "No" :
			($row['foreign_filing_license'] ? "Yes" : "No"));

		/* due date */
		$file_by_str = "";
		if (!empty($row['bar_date']) || !empty($row['file_by_date'])) {
//			$little_date = new DateTime("2020-02-14"); /* for testing */
			$little_date = new DateTime();
			$little_date->setTimezone($globals->current_user->timezone_object);
			if (empty($row['file_by_date'])) {
				$big_date_file_by = "";
			} else {
				$big_date_file_by = new DateTime($row['file_by_date']);
				$big_date_file_by->setTimezone($globals->current_user->timezone_object);
			}
			if (empty($row['bar_date'])) {
				$big_date_bar_date = "";
			} else {
				$big_date_bar_date = new DateTime($row['bar_date']);
				$big_date_bar_date->setTimezone($globals->current_user->timezone_object);
			}
			$interval_file_by_date = empty($big_date_file_by) ? "" : 
				$little_date->diff($big_date_file_by);
			$interval_file_bar_date = empty($big_date_bar_date) ? "" : 
				$little_date->diff($big_date_bar_date);
//			$ret_string .= dd_td_default(dd_date_to_DD_Month_YYYY(dd_date_add_months($the_oa_date, 3)));

				// if (!empty($interval_file_by_date)) {
				// if (!empty($row['file_by_date_diff']) || $row['file_by_date_diff'] == 0) {
				if (!empty($row['file_by_date_diff']) || !strcmp($row['file_by_date_diff'], "0")) {
					// $the_sign = $interval_file_by_date->format('%R');
					$overdue = !strcmp(substr($row['file_by_date_diff'], 0, 1), "-");
					//	$file_by_str .= "<FONT color=\"#FF0000\">TODAY</FONT>";
					if ($overdue) {
						$file_by_str .= sprintf("%s days <SPAN class=\"dd_red_font\">overdue</SPAN>",
								//$interval_file_by_date->format('%a'));
								$row['file_by_date_diff']);
					} else {
					// file_by_date_diff
						if (!strcmp("0", $row['file_by_date_diff'])) {
					//	if (!strcmp(substr($row['file_by_date_diff'], 0, 1), "0")) {
					//	if (!strcmp("0", $interval_file_by_date->format('%a'))) {
							$file_by_str .= "<FONT color=\"#FF0000\">TODAY</FONT>";
						} else {
							// $file_by_str .= $interval_file_by_date->format('%a days');
							$file_by_str .= $row['file_by_date_diff'] . " days";
						}
					}

				if (!empty($interval_file_bar_date)) {
					$file_by_str .= "<BR>";
					$file_by_str .= sprintf("<SPAN class=\"dd_red_font\">%s days to BAR DATE</SPAN>",
						$interval_file_bar_date->format('%a'));
				}

//				$ret_string .= "<!-- HERE -->";
			}
		} /* */

		if (empty($file_by_str)) {
			$ret_string .= dd_td_default("UNKNOWN");
		} else {
			$ret_string .= dd_td_default($file_by_str);
		}
		$ret_string .= dd_td_default(
			dd_tooltip(
			dd_anchor($row['application_status'], 
			sprintf("matter_update?form_client=%s&form_matter=%s&form_application_id=%s",
				urlencode($row['client']), urlencode($row['matter']), urlencode($row['id'])))
			, "Update/Change Status")
			);
		$ret_string .= "\n</TR>\n";
	}

	$ret_string .= "\t<TR>\n";
	$ret_string .= "<TD colspan=\"7\" align=\"center\">";
	$ret_string .= "Number of Applications is <B>" . strval($num_application_rows) . "</B></TD>\n";
	$ret_string .= "</TR>\n";

	$ret_string .= "</TABLE>\n";
	$ret_string .= "</DIV>\n";

	return $ret_string;
} /* dd_application_dashboard */

/***********************************************************************/

function
dd_application_dashboar_table_header($tick_tock)
{
	$ret_string = "";

	$ret_string .= "\t<TR class=\"dd_tr_header\">\n";
	$ret_string .= dd_td_default("Client");
	$ret_string .= dd_td_default("Matter");
	$ret_string .= dd_td_default("Application Type");
	$ret_string .= dd_td_default("File by date:");
	$ret_string .= dd_td_default("Bar date:");
	$ret_string .= dd_td_default("FFL:");
	$ret_string .= dd_td_default("Days to file by");
	$ret_string .= dd_td_default("Status");
	if ($tick_tock) {
		$ret_string .= dd_td_default("Updates");
	}
	$ret_string .= "\t</TR>\n";

	return $ret_string;
} /* dd_application_dashboar */

/***********************************************************************/

function
dd_get_all_matter_updates($client_val, $matter_val, $only_status_changes = FALSE)
{
	global $dd_db_handle;
	global $globals;
	$ret_string = "";
	$query_string = "";
//	$only_status_changes = TRUE;

	$query_string .= "SELECT * FROM matter_updates WHERE ";
	$query_string .= " client = :client_binder AND matter = :matter_binder ";

	if ($only_status_changes == TRUE) {
//		$query_string .= " AND status_update = TRUE";
		$query_string .= " AND (the_update LIKE 'Application status changed%' OR the_update LIKE 'OA Status changed%')";
//	$query_string .= " AND the_update LIKE 'Application status changed%'"; 
	}

	$query_string .= " ORDER BY the_update_timestamp";

	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->bindValue(':client_binder', $client_val, PDO::PARAM_STR);
	$tmp_results->bindValue(':matter_binder', $matter_val, PDO::PARAM_STR);
	$exec_bool = $tmp_results->execute();

	if ($exec_bool) {
		$tmp_array = $tmp_results->fetchALL();
		$tmp_results->closeCursor();
		return $tmp_array;
	} else {
		return array();
	}
} // dd_get_all_matter_updates

/***********************************************************************/

function
dd_process_print_dashboard_rows(&$rows, $the_type, $only_status_changes = FALSE)
{
	$ret_string = "";

	foreach ($rows as $row) {
		if (!strcmp($the_type, "Applicaitons") && !empty($row['bar_date'])) {
			$ret_string .= sprintf("<B>%s %s -- BAR DATE: %s </B><BR>\n", $row['client'],
				$row['matter'], dd_date_to_DD_Month_YYYY($row['bar_date']));
		} else {
			$ret_string .= sprintf("<B>%s %s</B><BR>\n", $row['client'], $row['matter']);
		}
		$all_updates = dd_get_all_matter_updates($row['client'], $row['matter'], 
			$only_status_changes);
		$all_updates_count = count($all_updates);
		if (0 == $all_updates_count) {
			$ret_string .= "&nbsp;&nbsp;No Updates\n";
		} else {
			foreach ($all_updates as $the_update) {
				list($date_str, $time_str) = explode(" ", $the_update['the_update_timestamp']);
				$the_date = new DateTime($date_str);
				// $ret_string .= sprintf("&nbsp;&nbsp;%s %s (%s): \"%s\"<BR>\n",
				$ret_string .= sprintf("&nbsp;&nbsp;%s %s (%s): %s<BR>\n",
					dd_date_to_DD_Month_YYYY($the_date),
					$time_str,
					$the_update['username'],
					htmlentities($the_update['the_update']));
			}
		}
		$ret_string .= "<BR><BR>\n";
	}

	return $ret_string;
} // dd_process_print_dashboard_rows

/***********************************************************************/

function
dd_print_dashboard($username_val, $only_status_changes = FALSE)
{
	$ret_string = "";

	$OA_rows = dd_get_OA_dashboard_rows($username_val);

	$ret_string .= "<B>Office Actions:</B><BR><BR>\n";

	$ret_string .= dd_process_print_dashboard_rows($OA_rows, "OAs", $only_status_changes);

	$ret_string .= "<B>Applications:</B><BR><BR>\n";

	$applications_rows = dd_get_application_dashboard_rows($username_val);

	$ret_string .= dd_process_print_dashboard_rows($applications_rows, "Applications", $only_status_changes);

	return $ret_string;
} // dd_print_dashboard

/***********************************************************************/

function
dd_get_js_file($the_desired_js_file)
{
	global	$dd_php_files;
	
	$the_js_file = $dd_php_files . "/js/" . $the_desired_js_file;

	return file_get_contents($the_js_file);
} /* dd_get_js_file */

/***********************************************************************/

function
dd_formatted_application_no($application_no, $the_country)
{
	$ret_string = "";

	if (!strcmp($the_country, "US")) {
		$ret_string .= dd_format_US_app_no($application_no);
	} else {
		$ret_string .= $application_no;
	}

	return $ret_string;
} /* dd_formatted_application_no */

/***********************************************************************/

function
dd_get_all_updates($form_client, $form_matter)
{
	global	$dd_db_handle;
	$query_string = "";

	$query_string .= "SELECT * FROM matter_updates WHERE client = :client_binder AND ";
	$query_string .= " matter = :matter_binder ORDER BY the_update_timestamp ASC";
	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->bindValue(':client_binder', $form_client, PDO::PARAM_STR);
	$tmp_results->bindValue(':matter_binder', $form_matter, PDO::PARAM_STR);
	$exec_bool = $tmp_results->execute();

	$tmp_array = $tmp_results->fetchALL();
	$tmp_results->closeCursor();

	return $tmp_array;
} // dd_get_all_updates

/***********************************************************************/

function
dd_update_matter()
{

} // dd_update_matter

/***********************************************************************/

function
dd_update_matter_oa_form($form_client, $form_matter, $form_oa_id)
{
	global	$globals;
	$form_string = "";
	$possible_oa_statuses = dd_get_oa_status_array();
	$oa_row = dd_get_table_row_by_id("office_action", $form_oa_id);
	$form_string .= dd_form_start("matter_update", "POST");
	$form_string .= sprintf("<INPUT type=\"hidden\" name=\"form_client\" value=\"%s\">", $form_client);
	$form_string .= sprintf("<INPUT type=\"hidden\" name=\"form_matter\" value=\"%s\">", $form_matter);
	$form_string .= sprintf("<INPUT type=\"hidden\" name=\"form_oa_id\" value=\"%s\">", $form_oa_id);
	$form_string .= "<INPUT type=\"hidden\" name=\"form_submit_it\" value=\"X\">";
	$form_string .= "<TABLE>\n";
	$form_string .= "\t<TR class=\"dd_tr_header\"><TD align=\"center\" colspan=\"2\">";
	$form_string .= sprintf("Client: %s Matter: %s<BR>\n", $form_client, $form_matter);
	$form_string .= "Office Action Update:</TD></TR>\n";
	$form_string .= "\t<TR><TD align=\"right\" valign=\"center\">Update text:<BR> (if none leave blank)</TD>";
	$form_string .= "<TD align=\"left\"><TEXTAREA name=\"form_update_text\" rows=\"4\" cols=\"60\"></TEXTAREA></TD>";
	$form_string .= "</TD></TR>\n";
	$form_string .= "\t<TR><TD align=\"right\">Status:</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_form_dropdown_selected("form_oa_status", $possible_oa_statuses, $oa_row['oa_status']);

	$form_string .= "</TD></TR>\n";
	$form_string .= "<TD align=\"right\">Update timestamp:</TD>";
//	$form_string .= "<INPUT TYPE=\"text\" NAME=\"form_timestamp\" REQUIRED>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= sprintf("<INPUT TYPE=\"text\" NAME=\"form_timestamp_tz\" VALUE=\"%s%03d\" REQUIRED>",
		$globals->date_now->format("Y-m-d H:i:s.0"), $globals->date_now->getOffset() / 3600);
	$form_string .= "</TD></TR>\n";
	$form_string .= "\t<TR><TD align=\"center\" colspan=\"2\">";
	$form_string .= "<INPUT TYPE=\"submit\" value=\"Update Matter\">";
	$form_string .= "</TD></TR>\n";
	$form_string .= "</TABLE>\n";
	$form_string .= "</FORM>\n";

	return $form_string;
} // dd_update_matter_oa_form

/***********************************************************************/ 

function
dd_process_update_matter_oa_form($form_oa_id)
{
	global	$globals;
	global	$dd_db_handle;
	$ret_string = "SUCCESS";
	$oa_row = dd_get_table_row_by_id("office_action", $form_oa_id);
	$form_oa_status = $_REQUEST['form_oa_status'];
	$form_timestamp_tz = $_REQUEST['form_timestamp_tz'];
	$form_update_text = $_REQUEST['form_update_text'];

	if (empty($oa_row)) {
		return "ERROR: oa_row is empty.";
	}

	$oa_status_change = 0 != strcmp($oa_row['oa_status'], $form_oa_status);
	$oa_text_update = !empty($form_update_text);

	$sql_status_update_stmt = "UPDATE office_action SET oa_status = :oa_status_binder ";
	$sql_status_update_stmt .= " WHERE id = :id_binder";

	$sql_matter_status_update_stmt = "INSERT INTO matter_updates ";
	$sql_matter_status_update_stmt .= " (client, matter, the_update, username, the_update_timestamp) ";
	$sql_matter_status_update_stmt .= " VALUES (:client_binder, :matter_binder, :the_update_binder, :username_binder, :the_update_timestamp_binder)";
	$status_change_text = sprintf("OA Status changed from \"%s\" to \"%s\".", $oa_row['oa_status'],
		$form_oa_status);
	

	$sql_matter_text_update_stmt = "INSERT INTO matter_updates ";
	$sql_matter_text_update_stmt .= " (client, matter, the_update, username, the_update_timestamp) ";
	$sql_matter_text_update_stmt .= " VALUES (:client_binder, :matter_binder, :the_update_binder, :username_binder, :the_update_timestamp_binder)";

	try {
		$dd_db_handle->beginTransaction();

		if ($oa_status_change) {
			$tmp_results = $dd_db_handle->prepare($sql_status_update_stmt);
			$tmp_results->bindValue(':oa_status_binder', $form_oa_status, PDO::PARAM_STR);
			$tmp_results->bindValue(':id_binder', $oa_row['id'], PDO::PARAM_INT);
			$exec_bool = $tmp_results->execute();

			$tmp_results = $dd_db_handle->prepare($sql_matter_status_update_stmt);
			$tmp_results->bindValue(':client_binder', $oa_row['client'], PDO::PARAM_STR);
			$tmp_results->bindValue(':matter_binder', $oa_row['matter'], PDO::PARAM_STR);
			$tmp_results->bindValue(':the_update_binder', $status_change_text, PDO::PARAM_STR);
			$tmp_results->bindValue(':username_binder', $globals->current_user->username,
				PDO::PARAM_STR);
			$tmp_results->bindValue(':the_update_timestamp_binder', $_REQUEST['form_timestamp_tz'],
				PDO::PARAM_STR);
			$exec_bool = $tmp_results->execute();
		}

		if ($oa_text_update) {
			$tmp_results = $dd_db_handle->prepare($sql_matter_text_update_stmt);
			$tmp_results->bindValue(':client_binder', $oa_row['client'], PDO::PARAM_STR);
			$tmp_results->bindValue(':matter_binder', $oa_row['matter'], PDO::PARAM_STR);
			$tmp_results->bindValue(':the_update_binder', $form_update_text, PDO::PARAM_STR);
			$tmp_results->bindValue(':username_binder', $globals->current_user->username,
				PDO::PARAM_STR);
			$tmp_results->bindValue(':the_update_timestamp_binder', $_REQUEST['form_timestamp_tz'], PDO::PARAM_STR);
			$exec_bool = $tmp_results->execute();
		}

		$dd_db_handle->commit();
	} catch(Exception $e) {
		$ret_string = $e->getMessage();
		$dd_db_handle->rollBack();
	}


	return $ret_string;

} // dd_process_update_matter_oa_form

/***********************************************************************/

function
dd_get_table_row_by_id($the_table, $the_id)
{
	global	$dd_db_handle;
	$query_string = "";

	$query_string .= sprintf("SELECT * FROM %s WHERE id = :id_binder", $the_table);
	$tmp_results = $dd_db_handle->prepare($query_string);
	$tmp_results->bindValue(':id_binder', $the_id, PDO::PARAM_INT);
	$exec_bool = $tmp_results->execute();

	$rows = $tmp_results->fetchAll();
	$tmp_results->closeCursor();

	return $rows[0];
} // dd_get_table_row_by_id

/***********************************************************************/

function
dd_update_matter_application_status_form($form_client, $form_matter, $form_application_id)
{
	global	$globals;
	$form_string = "";
	$possible_application_statuses = dd_get_application_status_array();
	$application_row = dd_get_table_row_by_id("application", $form_application_id);
	$form_string .= dd_form_start("matter_update", "POST");
	$form_string .= sprintf("<INPUT type=\"hidden\" name=\"form_client\" value=\"%s\">\n",
		$form_client);
	$form_string .= sprintf("<INPUT type=\"hidden\" name=\"form_matter\" value=\"%s\">\n",
		$form_matter);
	$form_string .= sprintf("<INPUT type=\"hidden\" name=\"form_application_id\" value=\"%s\">\n",
		$form_application_id);
	$form_string .= "<INPUT type=\"hidden\" name=\"form_submit_it\" value=\"X\">\n";
	$form_string .= "<TABLE>\n";
	$form_string .= "\t<TR class=\"dd_tr_header\"><TD align=\"center\" colspan=\"2\">";
	$form_string .= sprintf("Client: %s Matter: %s<BR>\n", $form_client, 
		dd_anchor($form_matter, sprintf("find_application?client=%s&matter=%s", 
			urlencode($form_client), urlencode($form_matter))));
	$form_string .= "Application Status Update:</TD></TR>\n";
	$form_string .= "\t<TR><TD align=\"right\" valign=\"center\">Update text:<BR>(if none leave blank)</TD>";
	$form_string .= "<TD align=\"left\"><TEXTAREA name=\"form_update_text\" rows=\"4\" cols=\"60\"></TEXTAREA></TD>";
	$form_string .= "</TD></TR>\n";
	$form_string .= "\t<TR><TD align=\"right\">Status:</TD>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= dd_form_dropdown_selected("form_application_status", 
		$possible_application_statuses, $application_row['application_status']);
	$form_string .= "</TD></TR>\n";
	$form_string .= "<TD align=\"right\">Update timestamp:</TD>";
//	$form_string .= "<INPUT TYPE=\"text\" NAME=\"form_timestamp\" REQUIRED>";
	$form_string .= "<TD align=\"left\">";
	$form_string .= sprintf("<INPUT TYPE=\"text\" NAME=\"form_timestamp_tz\" VALUE=\"%s%03d\" REQUIRED>",
		$globals->date_now->format("Y-m-d H:i:s.0"), $globals->date_now->getOffset() / 3600);
	$form_string .= "</TD></TR>\n";
	$form_string .= "\t<TR><TD align=\"center\" colspan=\"2\">";
	$form_string .= "<INPUT TYPE=\"submit\" value=\"Update Application Status\">";
	$form_string .= "</TD></TR>\n";
	$form_string .= "</TABLE>\n";
	$form_string .= "</FORM>\n";

	return $form_string;
} // dd_update_matter_application_status_form

/***********************************************************************/

function
dd_process_update_matter_application_status_form($form_application_id)
{
	global	$globals;
	global	$dd_db_handle;
	$ret_string = "SUCCESS";
	$application_row = dd_get_table_row_by_id("application", $form_application_id);
	$form_application_status = $_REQUEST['form_application_status'];
	$form_timestamp_tz = $_REQUEST['form_timestamp_tz'];
	$form_update_text = $_REQUEST['form_update_text'];

	if (empty($application_row)) {
		return "ERROR: application_row is empty.";
	}

	$application_status_change = 0 != strcmp($application_row['application_status'],
		$form_application_status);
	$application_text_update = !empty($form_update_text);

	$sql_status_update_stmt = "UPDATE application SET application_status = :application_status_binder ";
	$sql_status_update_stmt .= " WHERE id = :id_binder";

	$sql_matter_status_update_stmt = "INSERT INTO matter_updates ";
	$sql_matter_status_update_stmt .= " (client, matter, the_update, username, the_update_timestamp) ";
	$sql_matter_status_update_stmt .= " VALUES (:client_binder, :matter_binder, :the_update_binder, :username_binder, :the_update_timestamp_binder)";
	$status_change_text = sprintf("Application status changed from \"%s\" to \"%s\".",
		$application_row['application_status'], $form_application_status);
	

	$sql_matter_text_update_stmt = "INSERT INTO matter_updates ";
	$sql_matter_text_update_stmt .= " (client, matter, the_update, username, the_update_timestamp) ";
	$sql_matter_text_update_stmt .= " VALUES (:client_binder, :matter_binder, :the_update_binder, :username_binder, :the_update_timestamp_binder)";

	try {
		$dd_db_handle->beginTransaction();

		if ($application_status_change) {
			$tmp_results = $dd_db_handle->prepare($sql_status_update_stmt);
			$tmp_results->bindValue(':application_status_binder', $form_application_status,
				PDO::PARAM_STR);
			$tmp_results->bindValue(':id_binder', $application_row['id'], PDO::PARAM_INT);
			$exec_bool = $tmp_results->execute();

			$tmp_results = $dd_db_handle->prepare($sql_matter_status_update_stmt);
			$tmp_results->bindValue(':client_binder', $application_row['client'], PDO::PARAM_STR);
			$tmp_results->bindValue(':matter_binder', $application_row['matter'], PDO::PARAM_STR);
			$tmp_results->bindValue(':the_update_binder', $status_change_text, PDO::PARAM_STR);
			$tmp_results->bindValue(':username_binder', $globals->current_user->username,
				PDO::PARAM_STR);
			$tmp_results->bindValue(':the_update_timestamp_binder', $_REQUEST['form_timestamp_tz'],
				PDO::PARAM_STR);
			$exec_bool = $tmp_results->execute();
		}

		if ($application_text_update) {
			$tmp_results = $dd_db_handle->prepare($sql_matter_text_update_stmt);
			$tmp_results->bindValue(':client_binder', $application_row['client'], PDO::PARAM_STR);
			$tmp_results->bindValue(':matter_binder', $application_row['matter'], PDO::PARAM_STR);
			$tmp_results->bindValue(':the_update_binder', $form_update_text, PDO::PARAM_STR);
			$tmp_results->bindValue(':username_binder', $globals->current_user->username,
				PDO::PARAM_STR);
			$tmp_results->bindValue(':the_update_timestamp_binder', $_REQUEST['form_timestamp_tz'],
				PDO::PARAM_STR);
			$exec_bool = $tmp_results->execute();
		}

		$dd_db_handle->commit();
	} catch(Exception $e) {
		$ret_string = $e->getMessage();
		$dd_db_handle->rollBack();
	}


	return $ret_string;

} // dd_process_update_matter_application_status_form

/***********************************************************************/

function
dd_dropdown_button($button_text, $menu_items)
{
	$ret_string = "";

	$ret_string .= "<div class=\"dd_button_dropdown\">\n";
	$ret_string .= "\t<button class=\"dd_dropbtn\">" . $button_text . "</button>\n";
	$ret_string .= "\t<div class=\"dd_button_dropdown-content\">\n";
	foreach ($menu_items as $atext => $the_url) {
		$ret_string .= "\t" . dd_anchor($atext, $the_url);

	}
	$ret_string .= "\t</div>\n";
	$ret_string .= "</div>\n";

	return $ret_string;
} // dd_dropdown_button

/***********************************************************************/

function
dd_tooltip($main_text, $tooltip_text)
{
	$tmp_string = "";

	$tmp_string .= "<div class=\"dd_tooltip\">" . $main_text;
	$tmp_string .= "<span class=\"dd_tooltiptext\">" . $tooltip_text;
	$tmp_string .= "</span></div>";

	return $tmp_string;
} /* dd_tooltip */

/***********************************************************************/

function
dd_show_inventor_result_table(&$inventor_array)
{
	$ret_string = "";

	$ret_string .= "<TABLE>\n";
	$ret_string .= "\t<TR>";
	$ret_string .= dd_th_default("First Name");
	$ret_string .= dd_th_default("Middle Name");
	$ret_string .= dd_th_default("Last Name");
	$ret_string .= dd_th_default("Suffix (e.g., Jr., III, etc.)");
	$ret_string .= dd_th_default("Preferred Name");
	$ret_string .= dd_th_default("Street Address");
	$ret_string .= dd_th_default("Appartment ID/Unit/No.");
	$ret_string .= dd_th_default("City");
	$ret_string .= dd_th_default("State");
	$ret_string .= dd_th_default("Zip Code (five)");
	$ret_string .= dd_th_default("Zip Code (four)");
	$ret_string .= dd_th_default("Time Zone");
	$ret_string .= dd_th_default("Home Phone");
	$ret_string .= dd_th_default("Work Phone");
	$ret_string .= dd_th_default("Cell Phone");
	$ret_string .= dd_th_default("Email Address");
	$ret_string .= dd_th_default("Citizenship");
	$ret_string .= dd_th_default("Client");
	$ret_string .= "</TR>\n";

	$table_columns = array("first_name", "middle_name", "last_name", "suffix",
		"preferred_name", "street_address", "apartment_id", "city", "state",
		"zipcode_seven", "zipcode_four", "timezone", "home_phone", "work_phone",
		"cell_phone", "email", "citizenship", "client");

	$row_is_odd = FALSE;
	foreach ($inventor_array as $the_inventor) {
//		$ret_string .= "\t<TR>";
		$ret_string .= "\t<TR class=\"" . ($row_is_odd ? "dd_tr_odd" : "dd_tr_even") . "\">";
		$row_is_odd = !$row_is_odd;
		foreach ($table_columns as $the_column) {
			if (strcmp("first_name", $the_column) == 0 || strcmp("last_name", $the_column) == 0) {
				$ret_string .= dd_td_default(dd_anchor($the_inventor[$the_column],
					sprintf("show_inventor?inventor_id=%d", $the_inventor['id'])));
			} else {
				$ret_string .= dd_td_default($the_inventor[$the_column]);
			}
		}
		$ret_string .= "</TR>\n";
	}

	$ret_string .= "</TABLE>\n";

	return $ret_string;
} // dd_show_inventor_result_table

/***********************************************************************/

/*
 * End of file:  dd_funcs.php
 */
?>
