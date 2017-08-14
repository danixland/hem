<?php

	/**
	* Configuration file
	*/

return array(
	// Fill up your database details
	"mysql_hostname"	=> "localhost",
	"mysql_username"	=> "test",
	"mysql_password"	=> "test",
	"mysql_database"	=> "test",
	// Add other account types in this array here if you want to support more than the standard ones
	"account_types"		=> array(
		"bank"				=> 0,
		"credit card"		=> 1,
		"prepaid card"		=> 2
	)
);
?>