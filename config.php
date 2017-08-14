<?php

/**
* Configuration file
*/


/**
* Please provide your MySQL login information below.
*/
define(DB_HOST, "localhost");
define(DB_USER, "test");
define(DB_PASS, "test");
define(DB_NAME, "test");


define(ACCOUNT_TYPES, array(
	'bank' => 0,
	'credit card' => 1,
	'prepaid card' => 2
	)
;)
?>