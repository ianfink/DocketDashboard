Docket Dashboard is a “web application” that manages a patent application docket for a practitioner.  For what it was, it worked well for its author, and now, it shows how its author can (or cannot) solve a problem with PHP source code and some SQL.  A many-to-many relation is as complex as the SQL gets.  The future of Docket Dashboard includes a rewrite in Go, should the author continue to need a patent application docket.

Docket Dashboard is written in PHP and is designed to use PostgreSQL as its database.  Currently, Docket Dashboard runs on a single computer (specifically a laptop) as cgi-bin programs under the thttpd webserver (https://acme.com/software/thttpd/).  Of course, Docket Dashboard can run on any webserver that can run PHP files.

This is considered a work-in-progress, and there is much, much more to do with Docket Dashboard.  Various portions were written in an “as-needed” fashion, but they have been successfully utilized.  Docket Dashboard currently does not have multiple user access or login protection, but it does have those “hooks” within the source code for that functionality.  It is advisable to only bind the webserver to 127.0.0.1, since there is no login/password protection.

The author hopes to have some data to play with published soon and also hopes to have some instructions on configuring Docket Dashboard to work with thttpd via cgi-bin in the not-too-distant future.  That might be the easiest way to play with Docket Dashboard.

