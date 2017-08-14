<?php

class hemBanking extends hemUsers {

	private $bank_mysqli, $bank_stmt;

	/**
	* Object construct verifies that a session has been started and that a MySQL connection can be established.
	* It takes no parameters.
	*
	* @exception	Exception	If a session id can't be returned.
	*/

	public function __construct() {
		$this->bank_mysqli = new mysqli($GLOBALS["mysql_hostname"], $GLOBALS["mysql_username"], $GLOBALS["mysql_password"], $GLOBALS["mysql_database"]);
		if( $this->bank_mysqli->connect_error )
			throw new Exception("MySQL connection could not be established: ".$this->bank_mysqli->connect_error);

	}

	public function echoing() {
		$id = parent::getID();

		return $id;
	}

}

?>