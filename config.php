<?php

/**
* Configuration file
*/


/**
* Please provide your MySQL login information below.
*/
define($mysql_hostname, "localhost");
define($mysql_username, "test");
define($mysql_password, "test");
define($mysql_database, "test");


define($account_types, array(
	'bank' => 0,
	'credit card' => 1,
	'prepaid card' => 2
	)
;)
?>