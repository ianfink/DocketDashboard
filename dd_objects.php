<?
/**
 * @author		Ian M. Fink
 *
 * @file		dd_objects.php
 *
 * @copyright	Copyright (C) 2025 Ian M. Fink.  All rights reserved.
 * 				A commercial license can be made available.
 *
 * Contact:		www.linkedin.com/in/ianfink
 *
 * Tabstop:		4
 * 
 */

/***********************************************************************/

class
form_table_object {
	public $form_columns;
	public $the_update_string;
	public $the_insert_string;
	public $the_query_string;
	public $all_columns;
	public $nvps;
	public $int_columns;


	private $date_suffixes = array("_year", "_month", "_day");
	private $table_name;
	private $update_string;
	private $db_handle;
	private $tmp_results;

	/*---------------------------------------------------------------------*/

	public function
	__construct($the_db_handle, $the_table_name)
	{
		$this->db_handle = $the_db_handle;
		$this->table_name = $the_table_name;
		$this->the_update_string = "";
		$this->the_insert_string = "";
		$this->nvps = array();
		$this->int_columns = array();
		$this->get_all_columns();
		$this->get_nvps();
	} /* __construct */

	/*---------------------------------------------------------------------*/

	private function
	get_all_columns()
	{
		$query_string = "SELECT * FROM " . $this->table_name . " LIMIT 1";
		$this->tmp_results = $this->db_handle->prepare($query_string);
		$exec_bool = $this->tmp_results->execute();

		$rows = $this->tmp_results->fetchAll();
		$this->tmp_results->closeCursor();

		$this->all_columns = array_keys($rows[0]);

		return;
	} // get_all_columns

	/*---------------------------------------------------------------------*/

	private function
	get_nvps()
	{
		foreach ($this->all_columns as $column) {
			if (!empty($_REQUEST["form_" . $column])) {
				$this->nvps[$column] = $_REQUEST["form_" . $column];
			} else {
				$this->nvps[$column] = "";
			}
		}

		return;
	} // get_nvps

	/*---------------------------------------------------------------------*/

	public function
	prepare_update_string_on_id()
	{
		$set_updates = array();
		$sql_stmt = "UPDATE inventor SET ";
		foreach ($this->nvps as $key => $value) {
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

		$this->tmp_results = $this->db_handle->prepare($sql_stmt);

		return $sql_stmt;
	} // prepare_update_string_on_id

	/*---------------------------------------------------------------------*/

	public function
	add_int_column($the_column)
	{
		array_push($this->int_columns, $the_column);

		return;
	} // add_int_column

	/*---------------------------------------------------------------------*/

	public function
	update_table()
	{
		$ret_string = "";
		foreach ($this->nvps as $key => $value) {
			if (empty($value)) {
				continue;
			} 
			$tmp_binder = ":" . $key . "_binder";
			if (in_array($key, $this->int_columns)) {
				$this->tmp_results->bindValue($tmp_binder, $value, PDO::PARAM_INT);
			} else {
				$this->tmp_results->bindValue($tmp_binder, $value, PDO::PARAM_STR);
			}
		}

		$this->tmp_results->bindValue(':id_binder', $_REQUEST['form_id'], PDO::PARAM_INT);

		return $this->tmp_results->execute();
	} // update_table

	/*---------------------------------------------------------------------*/

	private function
	date_dropdown_exists($the_date_column)
	{
		$it_exists = TRUE;
		$ret_val = "";

		foreach ($this->date_suffixes as $date_suffix) {
			if (empty($_REQUEST["form_" . $the_date_column . $date_suffix])) {
				$it_exists = $it_exists && FALSE;
				break;
			}
		}

		if ($it_exists) {
			$ret_val .= sprintf("%s-%s-%s",
				$_REQUEST["form_" . $the_date_column . "_year"],
				$_REQUEST["form_" . $the_date_column . "_month"],
				$_REQUEST["form_" . $the_date_column . "_day"]);
		}

		return $ret_val;
	} /* date_dropdown_exists */

	/*---------------------------------------------------------------------*/

	public function
	get_form_entries($all_columns, $date_columns)
	{
		foreach ($all_columns as $the_column) {
			if (!empty($_REQUEST["form_" . $the_column])) {
				$this->form_columns[$the_column] = $_REQUEST["form_" . $the_column];
			}
		}

		foreach ($date_columns as $the_date_column) {
			if (array_key_exists($the_date_column, $this->form_columns)) {
				continue;
			} else {
				$tmp_date = $this->date_dropdown_exists($the_date_column);
				if (!empty($tmp_date)) {
					$this->form_columns[$the_date_column] = $tmp_date;
				}
			}
		}
	} /* get_form_entries */

	/*---------------------------------------------------------------------*/

	public function
	prepare_update_string($additional_constraints)
	{
		$temp_array = array();
		$update_string = "";

		$update_string .= "UPDATE " . $this->table_name . " SET ";
		foreach ($this->form_columns as $key => $value) {
			if (!strcmp("id", $key)) {
				continue;
			}
			$temp_string = $key . " = :" . $key . "_binder";
			array_push($temp_array, $temp_string);
		}

		$update_string .= implode(", ", $temp_array);

		if (!empty($this->form_columns['id'])) {
			$update_string .= " WHERE id = :id_binder";
		}

		if (!empty($this->form_columns['id']) && !empty($additional_constraints)) {
			$update_string .= " AND " . $additional_constraints;
		} else if (empty($this->form_columns['id']) && !empty($additional_constraints)) {
			$update_string .= " WHERE " . $additional_constraints;
		}


		$this->the_update_string = $update_string;
	} /* prepare_update_string */

	/*---------------------------------------------------------------------*/

	public function
	prepare_insert_string($additional_constraints)
	{
		$left_array = array();
		$right_array = array();


		foreach ($this->form_columns as $key => $value) {
			if (!strcmp("id", $key)) {
				continue;
			}
			array_push($left_array, $key);
			array_push($right_array, ":" . $key . "_binder");
		}

		$temp_string .= "INSERT INTO " . $this->table_name . " (";
		$temp_string .= implode(", ", $left_array);
		$temp_string .= ") VALUES (";
		$temp_string .= implode(", ", $right_array);
		$temp_string .= ")";

		$this->the_insert_string = $temp_string;
	} /* prepare_insert_string */
} /* form_table_object */

/***********************************************************************/

class
use_table_object {
	public $nvp_array;
	public $all_rows;
	public $all_rows_count;
	public $the_update_string;
	public $the_insert_string;

	private $table_name;

	/*---------------------------------------------------------------------*/

	public function
	__construct($the_table_name, $the_nvp_array)
	{
		$this->table_name = $the_table_name;
		$this->nvp_array = $the_nvp_array;
		$this->the_update_string = "";
		$this->the_insert_string = "";
		$this->the_query_string = "";
		$this->all_rows_count = 0;
	} /* __construct */

	/*---------------------------------------------------------------------*/

	public function
	pre_prepare_query_string($ignore_id)
	{

	} /* pre_prepare_query_string */

	/*---------------------------------------------------------------------*/

	public function
	get_all_columns_no_constraint($the_db_handle)
	{
		$this->the_query_string = "";

		$this->the_query_string .= "SELECT * FROM " . $this->table_name;

		$tmp_results = $the_db_handle->prepare($this->the_query_string);
		$exec_bool = $tmp_results->execute();

		if ($exec_bool) {
			$this->all_rows = $tmp_results->fetchAll();
			$this->all_rows_count = count($this->all_rows);
		}

		return $exec_bool;
	}

	/*---------------------------------------------------------------------*/

	public function
	pre_prepare_insert_string($ignore_id)
	{
		$left_array = array();
		$right_array = array();

		foreach ($this->nvp_array as $key => $value) {
			if (!strcmp("id", $key) && $ignore_id) {
				continue;
			}
			array_push($left_array, $key);
			array_push($right_array, ":" . $key . "_binder");
		}

		$temp_string .= "INSERT INTO " . $this->table_name . " (";
		$temp_string .= implode(", ", $left_array);
		$temp_string .= ") VALUES (";
		$temp_string .= implode(", ", $right_array);
		$temp_string .= ")";

		$this->the_insert_string = $temp_string;
	} /* pre_prepare_insert_string */

	/*---------------------------------------------------------------------*/

	public function
	execute_insert_string($the_db_handle, $int_array, $ignore_id)
	{
		$tmp_results = $the_db_handle->prepare($this->the_insert_string);

		foreach ($this->nvp_array as $key => $value) {
			if (!strcmp("id", $key) && $ignore_id) {
				continue;
			}
			$tmp_binder = ":" . $key . "_binder";
			if (in_array($key, $int_array)) {
				$tmp_results->bindValue($tmp_binder, $value, PDO::PARAM_INT);
			} else {
				$tmp_results->bindValue($tmp_binder, $value, PDO::PARAM_STR);
			}
		}

		$exec_bool = $tmp_results->execute();

		if ($exec_bool) {
			return "SUCCESS";
		} else {
			$err_array =  $tmp_results->errorInfo();
			return(sprintf("Error: SQLSTATE=\"%s\" Error Message=\"%s\"",
				$err_array[0], $err_array[2]));
		}
	} /* prepare_insert_string */

	/*---------------------------------------------------------------------*/
} /* execute_insert_string */

/***********************************************************************/

class
web_page_debug_class {
	public $page_debug;

	//---------------------------------------------------------------------

	public function
	__construct()
	{
		$this->page_debug = "";
	} // __construct

	//---------------------------------------------------------------------

	public function
	add_to_page_debug($the_addition)
	{
		$this->page_debug .= $the_addition;
	} // add_to_page_debug

	//---------------------------------------------------------------------

} // web_page_debug_class

/***********************************************************************/

class
web_page_class {
	public $page_title;
	public $page_head;
	public $page_scripts;
	public $page_style;
	public $body_tag;
	public $page_body;
	public $page_leader;
	public $page_trailer;
	public $page_debug_object;
	public $page;
	public $xhr_html;


	private $root_source;
	private $set_body_called;

	/*---------------------------------------------------------------------*/

	public function
	__construct($the_title, $the_root_source)
	{
		$this->page_title = $the_title;
		$this->page_head = "<HEAD>\n";
		$this->page_style = "";
		$this->body_tag = "<BODY";
		$this->page_body = "";
		$this->page_leader = "";
		$this->page_trailer = "";
		$this->page_scripts = "";
		$this->page_debug_object = new web_page_debug_class();
		$this->xhr_html = "";
		$this->page = "<HTML>\n";
		$this->set_body_tag_called = FALSE;
		$this->root_source = $the_root_source;

	} // __construct

	//---------------------------------------------------------------------

	public function
	include_js_file($the_desired_js_file)
	{
		$the_js_file = $this->root_source . "/js/" . $the_desired_js_file;

		$this->page_scripts .= "\n<SCRIPT type=\"text/javascript\">\n";
		$this->page_scripts .= file_get_contents($the_js_file);
		$this->page_scripts .= "\n</SCRIPT>\n";
	} // get_js_file

	//---------------------------------------------------------------------

	public function
	conclude_body_tag()
	{
		if (!$this->set_body_tag_called) {
			$this->body_tag .= ">\n";
		}
	} // conclude_body_tag

	//---------------------------------------------------------------------
	
	public function
	conclude_body()
	{
		$this->page_body = $this->body_tag . $this->page_body;
		$this->page_body .= "</BODY>\n";
	} // conclude_body

	//---------------------------------------------------------------------

	public function
	conclude_page_head()
	{
		$this->page_head .= "<TITLE>" . $this->page_title . "</TITLE>\n";
		$this->page_head .= $this->page_style;
		$this->page_head .= "\n";
		if (!empty($this->page_scripts)) {
			$this->page_head .= $this->page_scripts;
			$this->page_head .= "\n";
		}
		$this->page_head .= "</HEAD>\n";
	} // conclude_page_head

	//---------------------------------------------------------------------

	public function
	conclude_page_body()
	{
		$this->conclude_body_tag();
		$this->page_body = $this->body_tag . $this->page_leader .  $this->page_body . $this->page_trailer;
		$this->page_body .= "\n</BODY>\n";
	} // conclude_page_body

	//---------------------------------------------------------------------

	public function
	conclude_page()
	{
		$this->conclude_page_head();
		$this->conclude_page_body();
		$this->page .= $this->page_head;
		$this->page .= $this->page_body;
		$this->page .= "</HTML>\n";
	} // conclude_page

	//---------------------------------------------------------------------

	public function
	redirect_it($the_url)
	{
		exit(0);
	} // redirect_it

	//---------------------------------------------------------------------

	public function
	refresh_it($second_to_refresh, $the_url)
	{
		$tmp_str = sprintf("Refresh: %d; %s", $second_to_refresh, $the_url);
		header($tmp_str);
	} // redirect_it

	//---------------------------------------------------------------------

	public function
	add_leader($the_leader)
	{
		$this->page_leader .= $the_leader;
	} // add_leader

	//---------------------------------------------------------------------

	public function
	add_trailer($the_trailer)
	{
		$this->page_trailer .= $the_trailer;
	} // add_trailer

	//---------------------------------------------------------------------

	public function
	add_style($the_style)
	{
		$this->page_style .= $the_style;
	} // add_style

	//---------------------------------------------------------------------

	public function
	add_to_body($the_addition)
	{
		$this->page_body .= $the_addition;
	} // add_to_body

	//---------------------------------------------------------------------

	public function
	add_to_head($the_addition)
	{
		$this->page_head .= $the_addition;
	} // add_to_head

	//---------------------------------------------------------------------

	public function
	add_to_page_debug($the_addition)
	{
		$this->page_debug .= $the_addition;
	} // add_to_page_debug

	//---------------------------------------------------------------------

	public function
	add_to_xhr_html($the_addition)
	{
		$this->xhr_html .= $the_addition;
	} // add_to_xhr_html

	//---------------------------------------------------------------------

	public function
	set_body_tag($the_body_tag)
	{
		$this->set_body_tag_called = TRUE;
		$this->body_tag = $the_body_tag;
	} // set_body

	//---------------------------------------------------------------------

	public function
	set_page_icon($the_icon)
	{
		$tmp_str = sprintf("<link rel=\"icon\" type=\"image/png\" href=\"%s\">\n", $the_icon);
		$this->add_to_head($tmp_str);
	} // set_page_icon

	//---------------------------------------------------------------------

	public function
	write_page()
	{
		echo $this->page;
	} // write_page

	//---------------------------------------------------------------------

	public function
	write_xhr_html()
	{
		echo $this->xhr_html;
		exit(0);
	} // write_xhr_html

	//---------------------------------------------------------------------
} // web_page_class


/***********************************************************************/

/*
 * End of file:  dd_objects.php
 */

?>
