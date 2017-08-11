<?php

class hemBanking {

	private $mysqli, $stmt;

	/**
	* Object construct verifies that a session has been started and that a MySQL connection can be established.
	* It takes no parameters.
	*
	* @exception	Exception	If a session id can't be returned.
	*/

	public function __construct() {
		$this->mysqli = new mysqli($GLOBALS["mysql_hostname"], $GLOBALS["mysql_username"], $GLOBALS["mysql_password"], $GLOBALS["mysql_database"]);
		if( $this->mysqli->connect_error )
			throw new Exception("MySQL connection could not be established: ".$this->mysqli->connect_error);

	}

	public function echoing() {
		return hemUsers::$id;
	}

}

?>