/*
 * Copyright (C) 2025 Ian M. Fink.
 * All rights reserved.
 * 
 * For licensing information, please contact the copyright holder.
 *
 * File:	docket_table_create_postgres.sql
 *
 * Tabstops are four spaces.
 */

/* PostgreSQL create tables */


CREATE TABLE
user_t (
	username			TEXT NOT NULL
	, password			TEXT NOT NULL
	, salt				TEXT NOT NULL
	, first_name		TEXT NOT NULL
	, middle_name		TEXT
	, last_name			TEXT NOT NULL
	, preferred_name	TEXT
	, firm_username		TEXT
	, position			TEXT
	, timekeeper_id		TEXT 
	, timezone			TEXT 
	, user_level		INTEGER NOT NULL
	, PRIMARY KEY (username)
);

CREATE TABLE
user_rate (
	id					SERIAL PRIMARY KEY
	, username			TEXT NOT NULL
	, rate				INTEGER		-- whole dollars per hour
	, start_date		INTEGER		-- start date of rate
	, FOREIGN KEY (username) REFERENCES user_t(username) ON UPDATE CASCADE
);


CREATE TABLE
client (
	client			TEXT 
	, alt_client_id	TEXT
	, description	TEXT
	, PRIMARY KEY (client)
	, UNIQUE (client)
);

CREATE TABLE
next_action (
	id				SERIAL PRIMARY KEY
	, next_action	TEXT NOT NULL
);	

CREATE TABLE
matter (
	client					TEXT NOT NULL
	, matter				TEXT NOT NULL 
	, client_matter_id		TEXT 	-- ID that client knows matter as
	, PRIMARY KEY (client, matter) 
);

CREATE TABLE
country (
	, long_name			TEXT
	, short_name		TEXT NOT NULL
	, PRIMARY KEY (short_name)
); -- TABLE country


/******************* Application Type Table **********************/

CREATE TABLE
application_type (
	id		SERIAL PRIMARY KEY
	, type	TEXT NOT NULL
); -- application_type


/******************* Application Table **********************/

CREATE TABLE
application (
	id							SERIAL PRIMARY KEY
	, client					TEXT NOT NULL
	, matter					TEXT NOT NULL
	, application_type_id		INTEGER NOT NULL
	, description				TEXT
	, client_matter_title		TEXT  -- Title given by client
	, client_matter_id			TEXT  -- Client's ID of application
	, application_title			TEXT
	, file_by_date				DATE
	, filing_date				DATE
	, priority_date				DATE
	, bar_date					DATE
	, next_action_due			DATE
	, total_budget				INTEGER		-- in dollars
	, next_action_id			INTEGER	
	, application_assigned_to	TEXT
	, country_id				TEXT NOT NULL
	, application_no			TEXT
	, confirmation_no			TEXT
	, pat_app_ser_no			TEXT
	, pat_app_pub_no			TEXT
	, pat_no					TEXT
	, application_status		TEXT
	, abandoned					BOOLEAN
	, foreign_filing_license	BOOLEAN
	, FOREIGN KEY (application_type_id) REFERENCES application_type(id) ON UPDATE CASCADE
	, FOREIGN KEY (next_action_id) REFERENCES next_action(id) ON UPDATE CASCADE
	, FOREIGN KEY (application_assigned_to) REFERENCES user_t(username) ON UPDATE CASCADE
	, FOREIGN KEY (country_id) REFERENCES country(short_name) ON UPDATE CASCADE
	, FOREIGN KEY (client, matter) REFERENCES matter(client, matter) ON UPDATE CASCADE
); -- application

/******************* Application Assignment Table **********************/

/*
 * Table:		application_assignment
 *
 * Purpose:		store history of application assignment to a user
 *				
 * Columns:
 */

CREATE TABLE
application_assignment (
	id							SERIAL PRIMARY KEY 
	, client					TEXT NOT NULL
	, matter					TEXT NOT NULL
	, assigned_to				TEXT NOT NULL
	, assigned_by				TEXT NOT NULL
	, assignment_tstz			TIMESTAMPTZ
	, assignment_accepted_tstz	TIMESTAMPTZ
	, FOREIGN KEY (client, matter) REFERENCES matter(client, matter) ON UPDATE CASCADE
	, FOREIGN KEY (assigned_to) REFERENCES user_t(username) ON UPDATE CASCADE
	, FOREIGN KEY (assigned_by) REFERENCES user_t(username) ON UPDATE CASCADE
); /* application_assignment */

/******************* Application Assignment Table **********************/

/*
 * Table:		wall_access
 *
 * Purpose:		give permission to access information associated with a client.
 *				if a user is on this list, the user has access to the client information
 *				
 * Columns:
 */

CREATE TABLE
wall_access (
	username			TEXT NOT NULL
	, client				TEXT NOT NULL
	, FOREIGN KEY (username) REFERENCES user_t(username) ON UPDATE CASCADE
	, FOREIGN KEY (client) REFERENCES client(client) ON UPDATE CASCADE
	, PRIMARY KEY (username, client)
); -- wall_access

CREATE TABLE
supervisor (
	id					SERIAL PRIMARY KEY
	, username			TEXT NOT NULL
	, note				TEXT
	, application_id	INTEGER
	, FOREIGN KEY (username) REFERENCES user_t(username)
	, FOREIGN KEY (application_id) REFERENCES application(id)
); -- TABLE supervisor

/******************* Inventor Citizenship **********************/

CREATE TABLE
inventor_citizenship (
	short_name		TEXT NOT NULL
	, long_name		TEXT NOT NULL
	, PRIMARY KEY (short_name)
	, UNIQUE (short_name)
); -- inventor_citizenship

/******************* Inventor **********************/

/*
 * Table:		inventor
 *
 * Purpose:		inventor information
 *				
 * Columns:
 */


CREATE TABLE
inventor (
	id					SERIAL PRIMARY KEY
	, first_name		TEXT NOT NULL
	, middle_name		TEXT
	, last_name			TEXT NOT NULL
	, suffix			TEXT
	, preferred_name	TEXT
	, street_address	TEXT
	, apartment_id		TEXT
	, city				TEXT
	, state				TEXT
	, zipcode_seven		INTEGER
	, zipcode_four		INTEGER
	, timezone			TEXT
	, home_phone		TEXT
	, work_phone		TEXT
	, cell_phone		TEXT
	, email				TEXT
	, client			TEXT NOT NULL
	, citizenship		TEXT
	, FOREIGN KEY (client) REFERENCES client(client)
	, FOREIGN KEY (citizenship) REFERENCES country(short_name)
);	-- inventor

/******************* Inventor Matter Relationship **********************/

/*
 * Table:		inventor_application_relationship
 *
 * Purpose:		link inventors to matters
 *				
 * Columns:
 */

CREATE TABLE
inventor_application_relationship (
	inventor_id			INTEGER
	, application_id	INTEGER
	, FOREIGN KEY (inventor_id) REFERENCES inventor(id) ON UPDATE CASCADE
	, FOREIGN KEY (application_id) REFERENCES application(id) ON UPDATE CASCADE
	, PRIMARY KEY (inventor_id, application_id)
); -- inventor_application_relationship

/*****************************************/

CREATE TABLE
office_action_type (
--	id					SERIAL PRIMARY KEY 
	oa_type			TEXT UNIQUE NOT NULL
	, PRIMARY KEY (oa_type)
);

CREATE TABLE
reply_type (
--	id					SERIAL PRIMARY KEY 
	reply_type		TEXT UNIQUE NOT NULL
	, PRIMARY KEY (reply_type)
);

CREATE TABLE
office_action (
	id							BIGSERIAL PRIMARY KEY 
	, matter					TEXT NOT NULL
	, client					TEXT NOT NULL
	, application_id			INTEGER NOT NULL
	, oa_date					DATE
	, due_to_client_date		DATE
	, due_to_supervisor_date	DATE
	, file_by_date				DATE
	, hard_date					DATE
	, filing_date				DATE
	, oa_type					TEXT NOT NULL
	, reply_type				TEXT
	, oa_status					TEXT
	, allowable_subjectmatter	BOOLEAN
	, filed						BOOLEAN
	, total_budget				INTEGER		-- in dollars
	, FOREIGN KEY (client, matter)	REFERENCES matter(client, matter) ON UPDATE CASCADE
	, FOREIGN KEY (application_id)	REFERENCES application(id) ON UPDATE CASCADE
	, FOREIGN KEY (oa_type)			REFERENCES office_action_type(oa_type) ON UPDATE CASCADE
	, FOREIGN KEY (reply_type)		REFERENCES reply_type(reply_type) ON UPDATE CASCADE
	, FOREIGN KEY (OA_status)		REFERENCES possible_OA_statuses(OA_status) ON UPDATE CASCADE
); -- office_action

CREATE TABLE
office_action_client_preferences (
	id						SERIAL PRIMARY KEY 
	, client				TEXT NOT NULL
	, oa_days_to_review		INTEGER		-- number of days to review before statutory deadline
	, FOREIGN KEY (client)	REFERENCES client(client) ON UPDATE CASCADE
);


/******************* Possible Office Action Statuses **********************/

/*
 * Table:		possible_oa_statuses
 *
 * Purpose:		correlate inventors with patent applications
 *				
 * Columns:
 */

CREATE TABLE
possible_oa_statuses (
	OA_status			TEXT NOT NULL
	, PRIMARY KEY (OA_status)
	, UNIQUE (OA_status)
); -- inventor_application_relationship

/******************* oa_instructions **********************/

/*
 * Table:		oa_instructions
 *
 * Purpose:		track OA instructions from client
 *				
 * Columns:
 */

CREATE TABLE
oa_instructions (
	id							BIGSERIAL PRIMARY KEY 
	, oa_id						BIGINT
	, instructions				TEXT NOT NULL
	, updated_by				TEXT NOT NULL
	, updated_date				DATE
	, FOREIGN KEY (oa_id)		REFERENCES office_action(id) ON UPDATE CASCADE
	, FOREIGN KEY (updated_by)	REFERENCES user_t(username) ON UPDATE CASCADE
); -- oa_instructions

/******************* page_access **********************/

/*
 * Table:		page_access
 *
 * Purpose:		track access of pages
 *				
 * Columns:
 */

CREATE TABLE
page_access (
	id							BIGSERIAL PRIMARY KEY 
	, username					TEXT NOT NULL
	, access_timestamp			TIMESTAMPTZ NOT NULL
	, http_user_agent			TEXT NOT NULL
	, remote_address			TEXT NOT NULL
	, http_referer				TEXT
	, query_string				TEXT
	, FOREIGN KEY (username) REFERENCES user_t(username)
	, UNIQUE (id)
); -- page_access

/******************* Possible Application Statuses **********************/

/*
 * Table:		possible_application_statuses
 *
 * Purpose:		
 *				
 * Columns:
 */

CREATE TABLE
possible_application_statuses (
	application_status			TEXT NOT NULL
	, PRIMARY KEY (application_status)
	, UNIQUE (application_status)
); -- possible_application_statuses

/******************* Child Application Type **********************/

/*
 * Table:		child_application_type
 *
 * Purpose:		enumerate possible types of child applications
 *				
 * Columns:
 */

CREATE TABLE
child_application_type (
	child_type				TEXT NOT NULL
	, UNIQUE (child_type)
	, PRIMARY KEY (child_type)
); -- child_application_type

/******************* Appplication Parent/Child Relationship **********************/

/*
 * Table:		application_parent_child_relationship
 *
 * Purpose:		link applications by parent-child relations
 *				
 * Columns:
 */

CREATE TABLE
application_parent_child_relationship (
	parent_application_id	INTEGER
	, child_application_id	INTEGER
	, child_type			TEXT NOT NULL
	, FOREIGN KEY (parent_application_id) REFERENCES application(id) ON UPDATE CASCADE
	, FOREIGN KEY (child_application_id) REFERENCES application(id) ON UPDATE CASCADE
	, FOREIGN KEY (child_type) REFERENCES child_application_type(child_type) ON UPDATE CASCADE
	, PRIMARY KEY (parent_application_id, child_application_id)
	, UNIQUE (parent_application_id, child_application_id)
); -- application_parent_child_relationship

/******************* TIME TABLES **********************/

/******************* time_task **********************/

/*
 * Table:		time_task
 *
 * Purpose:		task that may have multiple summable time
 *				entries associated with it.
 *
 * Columns:
 */

CREATE TABLE
time_task (
	id					BIGINT PRIMARY KEY AUTOINCREMENT
	, username			TEXT NOT NULL
	, client			TEXT
	, matter			TEXT
	, description		TEXT
	, total_time		INTEGER -- in tenths of hours
	, open				BOOLEAN
	, converted			BOOLEAN
	, time_entry_date	DATE
	, total_time		INTEGER -- in tenths of hours
	, FOREIGN KEY (username) REFERENCES user_t(username) ON UPDATE CASCADE
	, FOREIGN KEY (client, matter) REFERENCES matter(client, matter) ON UPDATE CASCADE
); -- time_task

/******************* summabe_time_entry **********************/

/*
 * Table:		summabe_time_entry
 *
 * Purpose:		a single time entry for a single day
 *
 *				multiple single of these time entries are
 *				typically utilized in a single day
 *				
 * Columns:
 */

CREATE TABLE
summabe_time_entry (
	id				BIGINT PRIMARY KEY AUTOINCREMENT
	, time_task_id	BIGINT
	, username		TEXT NOT NULL
	, client		TEXT
	, matter		TEXT
	, start_time	TIMESTAMPTZ
	, end_time		TIMESTAMPTZ
	, total_time	INTEGER -- in tenths of hours
	, open			BOOLEAN
	, FOREIGN KEY (time_task_id) REFERENCES time_task(id) ON UPDATE CASCADE
	, FOREIGN KEY (username) REFERENCES user_t(username) ON UPDATE CASCADE
	, FOREIGN KEY (client, matter) REFERENCES matter(client, matter) ON UPDATE CASCADE
); -- summabe_time_entry

/******************* time_entry **********************/

/*
 * Table:		time_entry
 *
 * Purpose:		a time entry for a single day
 *
 * Columns:
 */

CREATE TABLE
time_entry (
	id					BIGINT PRIMARY KEY AUTOINCREMENT
	, username			TEXT NOT NULL
	, client			TEXT
	, matter			TEXT
	, time_entry_date	DATE
	, total_time		INTEGER -- in tenths of hours
	, description		TEXT
	, converted			BOOLEAN

	, FOREIGN KEY (username) REFERENCES user_t(username) ON UPDATE CASCADE
	, FOREIGN KEY (client, matter) REFERENCES matter(client, matter) ON UPDATE CASCADE
); -- summabe_time_entry

/** XXXXXXX **/

/******************* matter_updates **********************/

CREATE TABLE
matter_updates (
	id							BIGSERIAL PRIMARY KEY 
	, client					TEXT NOT NULL
	, matter					TEXT NOT NULL
	, the_update				TEXT NOT NULL
	, username					TEXT NOT NULL
	, the_update_timestamp		TIMESTAMPTZ
	, FOREIGN KEY (client, matter)	REFERENCES matter(client, matter) ON UPDATE CASCADE
	, FOREIGN KEY (username)		REFERENCES user_t(username) ON UPDATE CASCADE
	, UNIQUE (id)
); -- matter_updates

/******************* todo **********************/

/*
 * Table:		todo
 *
 * Purpose:		A list of to-do's or a check-list of things
 *				for an application
 * Columns:
 *		setup_date:		date of creation of todo row
 *		do_by_date:		date ##  if -1; do whenever;
 *		description:	what to do.
 *		completed_data:	date that the todo was completed.
 */

CREATE TABLE
todo (
	id					BIGSERIAL PRIMARY KEY
	, creator_of_todo	TEXT NOT NULL
	, assigned_to		TEXT NOT NULL
	, setup_date		DATE
	, do_by_date		DATE
	, description		TEXT NOT NULL
	, completed_date	DATE
	, client			TEXT NOT NULL
	, matter			TEXT NOT NULL
	, FOREIGN KEY (client)			REFERENCES matter(client) ON UPDATE CASCADE
	, FOREIGN KEY (matter)			REFERENCES matter(matter) ON UPDATE CASCADE
	, FOREIGN KEY (creator_of_todo)	REFERENCES user_t(username) ON UPDATE CASCADE
	, FOREIGN KEY (assigned_to)		REFERENCES user_t(username) ON UPDATE CASCADE
);


CREATE TABLE
matter_state (
	id						INTEGER PRIMARY KEY AUTOINCREMENT 
	, client				TEXT NOT NULL
	, matter				TEXT NOT NULL
	, user_name				TEXT NOT NULL	-- user that instantiated this state
	, current				INTEGER	NOT NUL	-- is this state the current state? 0/1
	, begin_date_stamp		INTEGER		-- Unix time in UTC
	, end_date_stamp		INTEGER		-- Unix time in UTC
	, change_reason			TEXT
	, row_creation_time		INTEGER		-- Unix time in UTC
	, row_conclusion_time	INTEGER		-- Unix time in UTC
	, statutory_deadline	INTEGER		-- Unix time in UTC
	, review_by_deadline	INTEGER		-- Unix time in UTC
	, state_type_id			INTEGER	
	, FOREIGN KEY (state_type_id)	REFERENCES state_type(id)
	, FOREIGN KEY (change_reason)	REFERENCES change_reason(change_reason)
	, FOREIGN KEY (client)			REFERENCES client(client)
	, FOREIGN KEY (matter)			REFERENCES matter(matter)
	, FOREIGN KEY (user_name)		REFERENCES user(matter)
	, CONSTRAINT matter_state_unique UNIQUE (client, matter, date_stamp) ON CONFLICT ABORT
	, CONSTRAINT matter_state_unique UNIQUE (id) ON CONFLICT ABORT
);

CREATE TABLE
matter_substate (
	id						INTEGER PRIMARY KEY AUTOINCREMENT 
	, matter_state_id		INTEGER
	, client				TEXT NOT NULL
	, matter				TEXT NOT NULL
	, user_name				TEXT NOT NULL	-- user that instantiated this state
	, current				INTEGER	NOT NUL	-- is this state the current state? 0/1
	, begin_date_stamp		INTEGER		-- Unix time in UTC
	, end_date_stamp		INTEGER		-- Unix time in UTC
	, change_reason			TEXT
	, row_creation_time		INTEGER		-- Unix time in UTC
	, row_conclusion_time	INTEGER		-- Unix time in UTC
	, statutory_deadline	INTEGER		-- Unix time in UTC
	, review_by_deadline	INTEGER		-- Unix time in UTC
	, substate_type_id		INTEGER	
	, FOREIGN KEY (matter_state_id)	REFERENCES matter_state(id)
	, FOREIGN KEY (state_type_id)	REFERENCES state_type(id)
	, FOREIGN KEY (change_reason)	REFERENCES change_reason(change_reason)
	, FOREIGN KEY (client)			REFERENCES client(client)
	, FOREIGN KEY (matter)			REFERENCES matter(matter)
	, FOREIGN KEY (user_name)		REFERENCES user(matter)
	, CONSTRAINT matter_state_unique UNIQUE (client, matter, date_stamp) ON CONFLICT ABORT
	, CONSTRAINT matter_state_unique UNIQUE (id) ON CONFLICT ABORT
); -- matter_substate

CREATE TABLE
change_reason (
	change_reason		TEXT NOT NULL
	, PRIMARY KEY change_reason
	, CONSTRAINT change_reason_unique UNIQUE (change_reason) ON CONFLICT ABORT
); -- TABLE change_reason

-- Note to self:  not sure I need this table: matter_state_update

CREATE TABLE
matter_state_update (
	id					INTEGER PRIMARY KEY AUTOINCREMENT
	, change_reason		TEXT
	, date_stamp		INTEGER		-- Unix time in UTC
	, row_update_time	INTEGER		-- Unix time in UTC
	, the_update		TEXT NOT NULL
	, matter_state_id	INTEGER	
	, user_name			TEXT NOT NULL
	, FOREIGN KEY (matter_state_id)	REFERENCES matter_state(id)
	, FOREIGN KEY (user_name)		REFERENCES user(matter)
	, CONSTRAINT matter_state_update_id_unique UNIQUE (id) ON CONFLICT ABORT
);


/******************* state table **********************/

/*
 * Table:		state
 *
 * Purpose:		reflect the state of the matter
 *				
 * Columns:
 *
 * EX:			Provisional Application Preparation;
 *				Provisional Design Application Preparation;
 * 				Nonprovisional Application Preparation;
 *				Nonprovisional Design Application Preparation;
 *				Waiting for filing receipt; Waiting for first OA;
 *				Application NOA; Patented; Interparty Review; Non-Final OA Received;
 *				Final OA Received; Preparing Reply to Non-Final OA;
 *				Preparing Reply to Final OA;
 *				Preparing Appeal Brief; Appeal Brief Filed;
 *				Preparing Appeal Brief Conference Review;
 *				Preparing Appeal Brief Conference Review Filed;
 */

CREATE TABLE
state (
	id				INTEGER PRIMARY KEY AUTOINCREMENT
	, client		TEXT NOT NULL
	, matter		TEXT NOT NULL
	, state_type_id	INTEGER
	, start_date	INTEGER
	, end_date		INTEGER
	, note			TEXT
	, FOREIGN KEY (client) REFERENCES matter(client)
	, FOREIGN KEY (matter) REFERENCES matter(matter)
	, FOREIGN KEY (state_type_id) REFERENCES state_type(id)
	, CONSTRAINT state_id_unique UNIQUE (id) ON CONFLICT ABORT
);

/******************* substate table **********************/

/*
 * Table:		substate
 *
 * Purpose:		reflect the substate of the state of the matter
 *				
 * Columns:
 *
 * EX:			Sent inventors invitation;
 *				Scheduled inventor interview;
 * 				Drafting patent application;
 *				Drafting Reply;
 *				Drafting recommendation for Client;
 *				Sent first draft to inventor(s);
 *				Sent second draft to inventor(s);
 *				Sent drawings to draftsman;
 *				Received comments from inventors;
 *				Preparing Appeal Brief; Appeal Brief Filed;
 *				Preparing Appeal Brief Conference Review;
 *				Preparing Appeal Brief Conference Review Filed;
 */

CREATE TABLE
substate (
	id				INTEGER PRIMARY KEY AUTOINCREMENT
	, client		TEXT NOT NULL
	, matter		TEXT NOT NULL
	, state_type_id	INTEGER
	, start_date	INTEGER
	, end_date		INTEGER
	, note			TEXT
	, FOREIGN KEY (client) REFERENCES matter(client)
	, FOREIGN KEY (matter) REFERENCES matter(matter)
	, FOREIGN KEY (state_type_id) REFERENCES state_type(id)
	, CONSTRAINT state_id_unique UNIQUE (id) ON CONFLICT ABORT
);

/******************* state_type table **********************/

/*
 * Table:		state_type
 *
 * Purpose:		possible states and transitions
 *				
 * Columns:
 *
/******************* state_type table **********************/

/*
 * Table:		state_type
 *
 * Purpose:		possible states and transitions
 *				
 * Columns:
 *
 * EX:			Provisional Application Preparation;
 *				Provisional Design Application Preparation;
 * 				Nonprovisional Application Preparation;
 *				Nonprovisional Design Application Preparation;
 *				Waiting for filing receipt; Waiting for first OA;
 *				Application NOA; Patented; Interparty Review; Non-Final OA Received;
 *				Final OA Received; Preparing Reply to Non-Final OA;
 *				Preparing Reply to Final OA;
 *				Preparing Appeal Brief; Appeal Brief Filed;
 *				Preparing Appeal Brief Conference Review;
 *				Preparing Appeal Brief Conference Review Filed;
 */

CREATE TABLE
state_type (
	id				INTEGER PRIMARY KEY AUTOINCREMENT
	, type			TEXT NOT NULL
	, CONSTRAINT state_type_id_unique UNIQUE (id) ON CONFLICT ABORT
	, CONSTRAINT state_type_type_unique UNIQUE (type) ON CONFLICT ABORT
);



/*
CREATE TABLE
user_level (
	username			TEXT NOT NULL
	, level				INTEGER
	, FOREIGN KEY (username) REFERENCES user_t(username)
	, PRIMARY KEY (username)
); -- TABLE user_level
*/

CREATE TABLE
event (
	id					INTEGER PRIMARY KEY AUTOINCREMENT 
	, client				TEXT NOT NULL
	, matter				TEXT NOT NULL
	, setup_date		INTEGER
	, due_date			INTEGER
	, description		TEXT NOT NULL
--	, PRIMARY KEY (id)
);

CREATE TABLE
standard_comment (
	id					INTEGER PRIMARY KEY AUTOINCREMENT
	, creation_date		INTEGER
	, comment			TEXT NOT NULL
); -- TABLE stored_comments

CREATE TABLE
matter_comment (
	id						INTEGER PRIMARY KEY AUTOINCREMENT
	, standard_comment_id	INTEGER
	, custom				INTEGER
	, comment				TEXT
	, client				TEXT NOT NULL
	, matter				TEXT NOT NULL
	, FOREIGN KEY (standard_comment_id) REFERENCES standard_comment(id)
	, FOREIGN KEY (client) REFERENCES matter(client)
	, FOREIGN KEY (matter) REFERENCES matter(matter)
); -- TABLE comment

/*
 * Table:		notes
 *
 * Purpose:		Side notes for a matter
 *				
 * Columns:
 */

CREATE TABLE
notes (
	id					INTEGER PRIMARY KEY AUTOINCREMENT
	, author_of_note	TEXT NOT NULL
	, note				TEXT NOT NULL
	, note_date			INTEGER NOT NULL
	, client			TEXT NOT NULL
	, matter			TEXT NOT NULL
	, FOREIGN KEY (author_of_note)	REFERENCES user(user_name)
	, FOREIGN KEY (client)			REFERENCES matter(client)
	, FOREIGN KEY (matter)			REFERENCES matter(matter)
--	, CONSTRAINT note_unique UNIQUE (client_id, matter_id) ON CONFLICT IGNORE
	, CONSTRAINT note_unique_id UNIQUE (id) ON CONFLICT REPLACE
);


/*
 * Table:		matter_time_entry
 *
 * Purpose:		time entry for a matter
 *				
 * Columns:
 */

CREATE TABLE
matter_time_entry (
	id					INTEGER PRIMARY KEY AUTOINCREMENT 
	, user_name			TEXT NOT NULL
	, timekeeper_id		TEXT NOT NULL
	, time_entry		INTEGER		-- hours in tenths of hours
	, time_entry_time	INTEGER		-- Unix time in UTC of the time entry
	, client			TEXT NOT NULL
	, matter			TEXT NOT NULL
	, FOREIGN KEY (user_name)		REFERENCES user(user_name)
	, FOREIGN KEY (timekeeper_id)	REFERENCES user(timekeeper_id)
	, FOREIGN KEY (client)			REFERENCES matter(client)
	, FOREIGN KEY (matter)			REFERENCES matter(matter)
	, CONSTRAINT matter_time_entry_unique UNIQUE (id) ON CONFLICT REPLACE
);	

/******************* Inventor **********************/

/*
 * Table:		inhouse_counsel
 *
 * Purpose:		in-house counsel information
 *				
 * Columns:
 */


CREATE TABLE
inhouse_counsel (
	id					SERIAL PRIMARY KEY
	, first_name		TEXT NOT NULL
	, middle_name		TEXT
	, last_name			TEXT NOT NULL
	, suffix			TEXT
	, preferred_name	TEXT
	, street_address	TEXT
	, apartment_id		TEXT
	, city				TEXT
	, state				TEXT
	, zipcode_seven		TEXT
	, zipcode_four		TEXT
	, timezone			TEXT
	, home_phone		TEXT
	, work_phone		TEXT
	, cell_phone		TEXT
	, email				TEXT
	, client			TEXT NOT NULL
	, FOREIGN KEY (client) REFERENCES client(client)
);	-- inhouse_counsel

/******************* Inhouse Counsel Application Relationship **********************/

/*
 * Table:		inhouse_counsel_application_relationship
 *
 * Purpose:		link inhouse cousel(s) to applications
 *				
 * Columns:
 */

CREATE TABLE
inhouse_counsel_application_relationship (
	inhouse_counsel_id			INTEGER
	, application_id			INTEGER
	, FOREIGN KEY (inhouse_counsel_id) REFERENCES inhouse_counsel(id) ON UPDATE CASCADE
	, FOREIGN KEY (application_id) REFERENCES application(id) ON UPDATE CASCADE
	, PRIMARY KEY (inhouse_counsel_id, application_id)
); -- inhouse_counsel_application_relationship

/******************* PHP Time Zones **********************/

/*
 * Table:		php_time_zone
 *
 * Purpose:		available PHP time zones
 *				
 * Columns:
 */

CREATE TABLE
php_time_zone (
	id					INTEGER PRIMARY KEY AUTOINCREMENT 
	, short_tz_name		TEXT NOT NULL
	, php_tz_name		TEXT NOT NULL
	, CONSTRAINT php_time_zone_unique UNIQUE (id) ON CONFLICT ABORT
	, CONSTRAINT php_time_zone_short_unique UNIQUE (short_tz_name) ON CONFLICT ABORT
); -- php_time_zone

/*
 * End of file:	docket_table_create_postgres.sql
 */


