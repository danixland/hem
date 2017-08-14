<?php

class hemBanking extends hemUsers {

	public function echoing() {
		$userid = parent::getID();

		return $userid;
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

	public function createAccount( $name, $type, $main, $aval_blnc, $count_blnc ) {

		$userid = parent::getID();
		if ( $aval_blnc == NULL )
			$aval_blnc = 0;

		if ( $count_blnc == NULL )
			$count_blnc = 0;

		$main_acc = ( $main ) ? 1 : 0;

		if ( ! $this->_account_exists($name, $userid) ) {
			$sql = "INSERT INTO accounts VALUES (NULL, ?, ?, ?, ?, ?, ?)";
			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->bind_param("issidd", $userid, $name, $type, $main_acc, $aval_blnc, $count_blnc);
			if( $this->stmt->execute() ) {
				$this->update_account_count($userid); # l'errore è qui!!
#					return $this->stmt->insert_id;
				return true;
			} else {
				return false;
			}
		} else {
			throw new Exception("Account \"" . $name .  "\" already exists");
		}
	}

	/**
	* Retrieve one single bank account by name owned by the current user.
	*
	*	@param	name	The name of the account you want to retrieve
	*	@param	id		Can be used if administrative control is needed
	*	@return 		String with a given account value or (bool) false
	*/

	public function getAccount( $name, $id = null ) {

		if ( $id == NULL )
			$id = parent::getID();

		$sql = "SELECT * FROM accounts WHERE owner=? AND account_name=? LIMIT 1";
		if( !$this->stmt = $this->mysqli->prepare($sql) )
			throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

		$this->stmt->bind_param("is", $id, $name);
		$this->stmt->execute();
		$this->stmt->store_result();

		if( $this->stmt->num_rows == 0)
			return "";

		$this->stmt->bind_result($value);
		$this->stmt->fetch();

		return $value;
	}

	/**
	* Use this function to permanently remove an account owned by the user. An optional id
	* can be passed as argument to this function for an admin to operate on another user account.
	*
	*	@param	name	The name of the account you want to delete
	*	@param	id		Can be used if administrative control is needed
	*
	*	@return			(bool) true on success or (bool) false otherwise
	*/

	public function deleteAccount( $accId, $id = null ) {

		if ( $id == NULL )
			$id = parent::getID();

		$sql = "DELETE FROM accounts WHERE owner=? AND id=? LIMIT 1";
		if( !$this->stmt = $this->mysqli->prepare($sql) )
			throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

		$this->stmt->bind_param("ii", $id, $accId);
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

		if ( $id == NULL )
			$id = parent::getID();

		$sql = "SELECT * FROM accounts WHERE owner=? ORDER BY id ASC";
		if( !$this->stmt = $this->mysqli->prepare($sql) )
			throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

		$this->stmt->bind_param("i", $id);
		$this->stmt->execute();
		$this->stmt->store_result();

		if( $this->stmt->num_rows == 0 )
			return array();

		$this->stmt->bind_result($id, $owner, $account_name, $account_type, $main_account, $aval_balance, $counting_balance);

		$accounts = array();

		$i = 0;

		while( $this->stmt->fetch() ) {
			$accounts[$i]["id"] = $id;
			$accounts[$i]["owner"] = $owner;
			$accounts[$i]["account_name"] = $account_name;
			$accounts[$i]["account_type"] = $account_type;
			$accounts[$i]["main_account"] = $main_account;
			$accounts[$i]["aval_balance"] = $aval_balance;
			$accounts[$i]["counting_balance"] = $counting_balance;

			$i++;
		}

		return $accounts;
	}


	/**
	* Check if an account owned by the user with the same name exists
	*
	*	@param	name	The name of the account you want to check
	*	@param	id		Can be used if administrative control is needed
	*	@return 		(bool) true if account exists or (bool) false otherwise
	*/

	private function _account_exists( $name, $id = null) {

		if ( $id == NULL )
			$id = parent::getID();

		$sql = "SELECT * FROM accounts WHERE owner=? AND account_name=? LIMIT 1";

		if( !$this->stmt = $this->mysqli->prepare($sql) )
			throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

		$this->stmt->bind_param("is", $id, $name);
		$this->stmt->execute();
		$this->stmt->store_result();

		if( $this->stmt->num_rows == 0) {
			return false;
		} else {
			return true;
		}
	}

	/**
	* Check if a user has a main account
	*
	*	@param	id		Can be used if administrative control is needed
	*	@return 		(bool) true if user has main account or (bool) false otherwise
	*/

	public function user_has_main_account( $id = null) {

		if ( $id == NULL )
			$id = parent::getID();

		$sql = "SELECT * FROM accounts WHERE owner=? AND main_account=1 LIMIT 1";

		if( !$this->stmt = $this->mysqli->prepare($sql) )
			throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

		$this->stmt->bind_param("i", $id);
		$this->stmt->execute();
		$this->stmt->store_result();

		if( $this->stmt->num_rows == 0) {
			return false;
		} else {
			return true;
		}
	}

	/**
	* Update the accounts count for a defined user.
	*
	*	@param	id		Can be used if administrative control is needed
	*/

	public function update_account_count( $id = null ) {

		if ( $id == NULL )
			$id = parent::getID();

		$acc_nmbr = $udata["accounts"];
		$acc_nmbr++;
		$sql = "UPDATE users SET accounts=? WHERE id=? LIMIT 1";
		if( !$this->stmt = $this->mysqli->prepare($sql) )
			throw new Exception("MySQL Prepare statement failed: " . $this->mysqli->error);

		$this->stmt->bind_param("ii", $acc_nmbr, $id);

		if( $this->stmt->execute() ) {
			return true;
		} else {
			return false;
		}
	}


}

?>