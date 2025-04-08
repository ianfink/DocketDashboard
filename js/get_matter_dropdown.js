/**
 * @author		Ian M. Fink
 *
 * @file		get_matter_dropdown.js
 *
 * @copyright	Copyright (C) 2025 Ian M. Fink.  All rights reserved.
 * 				A commercial license can be made available.
 *
 * Contact:		www.linkedin.com/in/ianfink
 *
 * Tabstop:		4
 * 
 */

function
get_matter_dropdown(the_client_val)
{
	var xhr = new XMLHttpRequest();

	var e = document.getElementById(the_client_val);
	var the_chosen_one = e.options[e.selectedIndex].value;

	xhr.open("GET", "get_matter_select?form_client=" + the_chosen_one);

	xhr.send();

	xhr.onload = function() {
		if (xhr.status != 200) { // analyze HTTP status of the response
			alert("Error " + xhr.status + ":" + xhr.statusText);
  		} else {
//			var responseObj = xhr.response;
			document.getElementById("id_matter_id").innerHTML = xhr.responseText;
//			alert(xhr.responseText);
//			alert("Hellow, World");
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
}

/*
 * End of file:	get_matter_dropdown.js
 */

