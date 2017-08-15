<?php

/**
* Configuration file
*/


/**
* Please provide your MySQL login information below.
*/
define("DB_HOST", "localhost");
define("DB_USER", "test");
define("DB_PASS", "test");
define("DB_NAME", "test");


define("ACCOUNT_TYPES", serialize(array(
	'bank' => 0,
	'credit card' => 1,
	'prepaid card' => 2
	))
);

/**
 * Here are defined the pages that shouldn't be available if a user is not logged in.
 * DO NOT MODIFY THIS ARRAY UNLESS YOU KNOW WHAT YOU'RE DOING!
 */
define("RESTRICTED_PAGES", serialize(array(
    "main",
    "accounts",
    "new-account",
    "transactions",
    "new-transaction"
    ))
);
?>