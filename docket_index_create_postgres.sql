/*
 * Copyright (C) 2025 Ian M. Fink.
 * All rights reserved.
 * 
 * For licensing information, please contact the copyright holder.
 *
 * File:	docket_index_create_postgres.sql
 *
 * Tabstops are four spaces.
 */


-- INDEXES

-- CREATE [UNIQUE] INDEX index_name 
-- ON table_name(column_list);

CREATE UNIQUE INDEX client_index ON client(client);

CREATE UNIQUE INDEX matter_index ON matter(client, matter);

CREATE UNIQUE INDEX user_index ON user(user_name);

CREATE UNIQUE INDEX state_index_client_matter ON state(client, matter);
CREATE UNIQUE INDEX state_index_id ON state(id);

CREATE UNIQUE INDEX matter_assignment_client_matterindex ON matter_assignment(client, matter);
CREATE UNIQUE INDEX matter_assignment_id_index ON matter_assignment(id);


CREATE INDEX inventor_application_relationship_inventor_index ON inventor_application_relationship(inventor_id);
CREATE INDEX inventor_application_relationship_application_index ON inventor_application_relationship(application_id);

/*
 * End of file:	docket_index_create_postgres.sql
 */

