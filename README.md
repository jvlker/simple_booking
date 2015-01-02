simple_booking
==============

Simple php page for booking-processes.

Structure
---------

- . : one php-file foreach site (no mvc)
- _dev: todo-list and database-setup-script
- admin: little administration-panel with the booking-list and log-book
- backend: little object-relational-mapper for mysql & sqlite databases
	- iDatabase: interfaces for the databases
	- objects: one class for each table
- config: data json and config file

Setup
-----

- Copy config/config.example.php to config/config.php and enter constants.
- Copy config/datafields.example.json to config/datafields.json.
- Create admin/.htpasswd, Uncomment admin/.htaccess, change path to .htpasswd