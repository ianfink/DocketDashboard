/**
 * @author		Ian M. Fink
 *
 * @file		get_inventor_update_form.js
 *
 * @copyright	Copyright (C) 2025 Ian M. Fink.  All rights reserved.
 * 				A commercial license can be made available.
 *
 * Contact:		www.linkedin.com/in/ianfink
 *
 * Tabstop:		4
 *
 * Purpose:		gets html for update inventor form
 * 
 */

function
get_inventor_update_form(the_inventor_id, the_div_id, the_auth_nvp)
{
	var xhr = new XMLHttpRequest();

//	alert("The div id: " + the_div_id);

//	var e = document.getElementById(the_div_id);
//	var the_chosen_one = e.options[e.selectedIndex].value;

//	xhr.open("GET", "get_matter_select?form_client=" + the_chosen_one);

// This will change to:
	xhr.open("GET", "show_inventor?form_id=" + the_inventor_id + "&form_change_info=TRUE&" + the_auth_nvp);
// but for now:
//	xhr.open("GET", "show_inventor?form_id=" + the_inventor_id + "&form_auth=" + the_auth_nvp);

	xhr.send();

	xhr.onload = function() {
		if (xhr.status != 200) { // analyze HTTP status of the response
			alert("Error " + xhr.status + ":" + xhr.statusText);
  		} else {
//			var responseObj = xhr.response;
			document.getElementById(the_div_id).innerHTML = xhr.responseText;
//			alert(xhr.responseText);
//			alert("Hellow, World 2");
  		}
	};

	xhr.onprogress = function(event) {
		if (event.lengthComputable) {
//			alert(`Received ${event.loaded} of ${event.total}`);
		} else {
//			alert("Received ${event.loaded} bytes"); // no Content-Length
		}
	};

	xhr.onerror = function() {
		alert("Request failed");
	};
	return false;
}

/*
 * End of file:	get_inventor_update_form.js
 */

