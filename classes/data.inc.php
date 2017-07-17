<?php

	class hemBanking {

		private $mysqli, $stmt;
		public $banking;

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

		/**
		* Returns a (int)account id, if the account was created succesfully.
		* If not, it returns (bool)false.
		*
		*	@param	name		The name of the account
		*	@param	type		The account type
		*
		*	@return	The account id or (bool)false
		*/

		public function createAccount( $name, $type, $aval_blnc, $count_blnc ) {

			$userid = $_SESSION[$hemUsers->sessionName]["id"];
			if ( $aval_blnc == NULL )
				$aval_blnc = 0;

			if ( $count_blnc == NULL )
				$count_blnc = 0;

			$sql = "INSERT INTO accounts VALUES (NULL, ?, ?, ?, ?, ?)";
			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->bind_param("sssii", $userid, $name, $type, $aval_blnc, $count_blnc);
			if( $this->stmt->execute() )
				return $this->stmt->insert_id;
				
			return false;
		}

		/**
		* Retrieve one single bank account owned by the current user.
		*
		*	@param	name	The name of the account you want to retrieve
		*	@param	id		Can be used if administrative control is needed
		*	@return 		String with a given account value or (bool) false
		*/

		public function getAccount( $name, $id = null ) {

			if ( $id == NULL )
				$id = $_SESSION[$hemUsers->sessionName]["id"];

			$sql = "SELECT * FROM accounts WHERE id=? AND account_name=? LIMIT 1";
			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->bind_param("is", $id, $key);
			$this->stmt->execute();
			$this->stmt->store_result();

			if( $this->stmt->num_rows == 0)
				return "";

			$this->stmt->bind_result($value);
			$this->stmt->fetch();

			return $value;
		}

		/**
		* Use this function to permanently remove information attached to a certain user
		* that has been set by using this objects setInfo() method.
		*
		*	@param	name	The name of the account you want to delete
		*	@param	id		Can be used if administrative control is needed
		*
		*	@return			(bool) true on success or (bool) false otherwise
		*/

		public function deleteAccount( $name, $id = null ) {

			if( $id == null ) {
				$id = $_SESSION[$hemUsers->sessionName]["id"];
			}

			$sql = "DELETE FROM accounts WHERE id=? AND account_name=? LIMIT 1";
			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->bind_param("is", $id, $name);
			$this->stmt->execute();

			if( $this->stmt->affected_rows > 0)
				return true;

			return false;
		}


		/**
		* Use this function to retrieve all bank accounts attached to a certain user
		*
		*	@param		id	Can be used if administrative control is needed
		* 	@return		An associative array with all stored information
		*/

		public function getAccounts( $id = null ) {
			if( $id == null )
				$id = $_SESSION[$hemUsers->sessionName]["id"];

			$sql = "SELECT * FROM accounts WHERE id=? ORDER BY id ASC";
			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->bind_param("i", $id);
			$this->stmt->execute();
			$this->stmt->store_result();

			$accounts = array();
			if( $this->stmt->num_rows > 0)
			{
				$this->stmt->bind_result($key, $value);
				while( $this->stmt->fetch() )
					$accounts[$key] = $value;
			}

			return $accounts;
		}
}

?>