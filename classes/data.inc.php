<?php

	/**
	 * This file is part of SimpleUsers.
	 *
	 */

	/**
	* SimpleUsers is a small-scale and flexible way of adding
	* user management to your website or web applications.
	*/

	class SimpleUsers
	{

		private $mysqli, $stmt;
		private $sessionName = "SimpleUsers";
		public $logged_in = false;
		public $userdata;

		/**
		* Object construct verifies that a session has been started and that a MySQL connection can be established.
		* It takes no parameters.
		*
		* @exception	Exception	If a session id can't be returned.
		*/

		public function __construct()
		{
			$sessionId = session_id();
			if( strlen($sessionId) == 0)
				throw new Exception("No session has been started.\n<br />Please add `session_start();` initially in your file before any output.");

			$this->mysqli = new mysqli($GLOBALS["mysql_hostname"], $GLOBALS["mysql_username"], $GLOBALS["mysql_password"], $GLOBALS["mysql_database"]);
			if( $this->mysqli->connect_error )
				throw new Exception("MySQL connection could not be established: ".$this->mysqli->connect_error);

			$this->_validateUser();
			$this->_populateUserdata();
			$this->_updateActivity();
		}

		/**
		* Returns a (int)user id, if the user was created succesfully.
		* If not, it returns (bool)false.
		*
		* @param	username	The desired username
		*	@param	password	The desired password
		*	@return	The user id or (bool)false (if the user already exists)
		*/

		public function createUser( $username, $password )
		{
			$salt = $this->_generateSalt();
			$password = $salt.$password;

			$sql = "INSERT INTO users VALUES (NULL, ?, SHA1(?), ?, NOW(), NOW())";
			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->bind_param("sss", $username, $password, $salt);
			if( $this->stmt->execute() )
				return $this->stmt->insert_id;
				
			return false;
		}

		/**
		* Pairs up username and password as registrered in the database.
		* If the username and password is correct, it will return (int)user id of
		* the user which credentials has been passed and set the session, for
		*	use by the user validating.
		*
		* @param	username	The username
		* @param	password	The password
		* @return	The (int)user id or (bool)false
		*/

		public function loginUser( $username, $password )
		{
			$sql = "SELECT id FROM users WHERE user_login=? AND SHA1(CONCAT(salt, ?))=user_pass LIMIT 1";
			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->bind_param("ss", $username, $password);
			$this->stmt->execute();
			$this->stmt->store_result();

			if( $this->stmt->num_rows == 0)
				return false;

			$this->stmt->bind_result($id);
			$this->stmt->fetch();

			$_SESSION[$this->sessionName]["id"] = $id;
			$this->logged_in = true;

			return $id;
		}

		/**
		* Sets an information pair, consisting of a key name and that keys value.
		* The information can be retrieved with this objects getInfo() method.
		*
		* @param	key	The name of the key
		* @param	value	The keys value
		* @param	id	Can be used if administrative control is needed
		* @return	This returns (bool)true or false.
		*/

		public function setInfo( $key, $value, $id = null)
		{
			if($id == null)
			{
				if( !$this->logged_in )
					return false;
			}

				$reservedKeys = array("id", "user_login", "uActivity", "user_registered", "uLevel");
				if( in_array($key, $reservedKeys) )
					throw new Exception("User information key \"".$key."\" is reserved for internal use!");

			if( $id == null )
				$id = $_SESSION[$this->sessionName]["id"];

			if( $this->getInfo($key, $id) )
			{
				$sql = "UPDATE users_information SET infoValue=? WHERE infoKey=? AND id=? LIMIT 1";
				if( !$this->stmt = $this->mysqli->prepare($sql) )
					throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

				$this->stmt->bind_param("ssi", $value, $key, $id);
				$this->stmt->execute();
			}
			else
			{
				$sql = "INSERT INTO users_information VALUES (?, ?, ?)";
				if( !$this->stmt = $this->mysqli->prepare($sql) )
					throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

				$this->stmt->bind_param("iss", $id, $key, $value);
				$this->stmt->execute();
			}

			return true;
		}

		/**
		* Use this function to retrieve user information attached to a certain user
		* that has been set by using this objects setInfo() method.
		*
		* @param	key	The name of the key you wan't the value from
		*	@param	id	Can be used if administrative control is needed
		* @return	String with a given keys value or (bool) false if the user isn't logged in.
		*/

		public function getInfo( $key, $id = null )
		{

			if( $id == null )
			{
				if( !$this->logged_in )
					return false;

				$id = $_SESSION[$this->sessionName]["id"];
			}

			$sql = "SELECT infoValue FROM users_information WHERE id=? AND infoKey=? LIMIT 1";
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
		* @param	key	The name of the key you wan't the value from
		*	@param	id	Can be used if administrative control is needed
		* @return	(bool) true on success or (bool) false if the user isn't logged in.
		*/

		public function removeInfo( $key, $id = null )
		{

			if( $id == null )
			{
				if( !$this->logged_in )
					return false;

				$id = $_SESSION[$this->sessionName]["id"];
			}

			$sql = "DELETE FROM users_information WHERE id=? AND infoKey=? LIMIT 1";
			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->bind_param("is", $id, $key);
			$this->stmt->execute();

			if( $this->stmt->affected_rows > 0)
				return true;

			return false;
		}				


		/**
		* Use this function to retrieve all user information attached to a certain user
		* that has been set by using this objects setInfo() method into an array.
		*
		*	@param	id	Can be used if administrative control is needed
		* @return	An associative array with all stored information
		*/

		public function getInfoArray( $id = null )
		{
			if( $id == null )
				$id = $_SESSION[$this->sessionName]["id"];

			$sql = "SELECT infoKey, infoValue FROM users_information WHERE id=? ORDER BY infoKey ASC";
			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->bind_param("i", $id);
			$this->stmt->execute();
			$this->stmt->store_result();

			$userInfo = array();
			if( $this->stmt->num_rows > 0)
			{
				$this->stmt->bind_result($key, $value);
				while( $this->stmt->fetch() )
					$userInfo[$key] = $value;
			}
			
			$user = $this->getSingleUser($id);
			$userInfo = array_merge($userInfo, $user);
			asort($userInfo);

			return $userInfo;
		}

		/**
		* Logout the active user, unsetting the id session.
		* This is a void function
		*/

		public function logoutUser()
		{
			if( isset($_SESSION[$this->sessionName]) )
				unset($_SESSION[$this->sessionName]);

			$this->logged_in = false;
		}

		/**
		* Update the users password with this function.
		* Generates a new salt and a sets the users password with the given parameter
		*
		* @param	password	The new password
		* @param	id	Can be used if administrative control is needed
		*/

		public function setPassword( $password, $id = null )
		{

			if( $id == null )
				$id = $_SESSION[$this->sessionName]["id"];

			$salt = $this->_generateSalt();
			$password = $salt.$password;

			$sql = "UPDATE users SET user_pass=SHA1(?), salt=? WHERE id=? LIMIT 1";
			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->bind_param("ssi", $password, $salt, $id);
			return $this->stmt->execute();
		}

		/**
		* Returns an array with each user in the database.
		*
		* @return	An array with user information
		*/

		public function getUsers()
		{
			
			$sql = "SELECT DISTINCT id, user_login, uActivity, user_registered FROM users ORDER BY user_login ASC";

			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->execute();
			$this->stmt->store_result();

			if( $this->stmt->num_rows == 0)
				return array();

			$this->stmt->bind_result($id, $username, $activity, $created);

			$users = array();

			$i = 0;
			while( $this->stmt->fetch() )
			{		
				$users[$i]["id"] = $id;
				$users[$i]["user_login"] = $username;
				$users[$i]["uActivity"] = $activity;
				$users[$i]["user_registered"] = $created;

				$i++;
			}

			return $users;

		}

		/**
		* Gets the basic info for a single user based on the id
		*
		* @param	id	The users id
		* @return	An array with the result or (bool)false.
		*/

		public function getSingleUser( $id = null )
		{

			if( $id == null )
				$id = $_SESSION[$this->sessionName]["id"];

			$sql = "SELECT id, user_login, uActivity, user_registered FROM users WHERE id=? LIMIT 1";
			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->bind_param("i", $id);
			$this->stmt->execute();
			$this->stmt->store_result();

			if( $this->stmt->num_rows == 0)
				return false;

			$this->stmt->bind_result($id, $username, $activity, $created);
			$this->stmt->fetch();

			$user["id"] = $id;
			$user["user_login"] = $username;
			$user["uActivity"] = $activity;
			$user["user_registered"] = $created;

			return $user;

		}

		/**
		* Deletes all information regarding a user.
		* This is a void function.
		*
		* @param	id	The id of the user you wan't to delete
		*/

		public function deleteUser( $id )
		{
			$sql = "DELETE FROM users WHERE id=?";
			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->bind_param("i", $id);
			$this->stmt->execute();

			$sql = "DELETE FROM users_information WHERE id=?";
			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->bind_param("i", $id);
			$this->stmt->execute();

			return;
		}

		/**
		* Returns a hidden input field with a unique token value
		* for CSRF to be used with post data.
		* The token is saved in a session for later validation.
		* 
		* @param	xhtml	set to (bool) true for xhtml output
		* @return Returns a string with a HTML element and attributes
		*/
		
		public function getToken( $xhtml = true )
		{
			$token = $this->_generateSalt();
			$name = "token_".md5($token);
			
			$_SESSION[$this->sessionName]["csrf_name"] = $name;
			$_SESSION[$this->sessionName]["csrf_token"] = $token;
			
			$string = "<input type=\"hidden\" name=\"".$name."\" value=\"".$token."\"";
			if($xhtml)
				$string .= " />";
			else
				$string .= ">";
			
			return $string;
		}		
		
		/**
		* Use this method when you wish to validate the CSRF token from your post data.
		* The method returns true upon validation, otherwise false. 
		*
		* @return bool true or false
		*/
		
		public function validateToken()
		{
			$name = $_SESSION[$this->sessionName]["csrf_name"];
			$token = $_SESSION[$this->sessionName]["csrf_token"];
			unset($_SESSION[$this->sessionName]["csrf_token"]);
			unset($_SESSION[$this->sessionName]["csrf_name"]);
			
			if($_POST[$name] == $token)
				return true;
				
			return false;
		}		

		/**
		* This function updates the users last activity time
		* This is a void function.
		*/

		private function _updateActivity()
		{
			if( !$this->logged_in )
				return;

			$id = $_SESSION[$this->sessionName]["id"];

			$sql = "UPDATE users SET uActivity=NOW() WHERE id=? LIMIT 1";
			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->bind_param("i", $id);
			$this->stmt->execute();
			return;
		}

		/**
		* Validates if the user is logged in or not.
		* This is a void function.
		*/

		private function _validateUser()
		{
			if( !isset($_SESSION[$this->sessionName]["id"]) )
				return;

			if( !$this->_validateid() )
				return;

			$this->logged_in = true;
		}

		/**
		* Validates if the user id, in the session is still valid.
		*
		* @return	Returns (bool)true or false
		*/

		private function _validateid()
		{
			$id = $_SESSION[$this->sessionName]["id"];

			$sql = "SELECT id FROM users WHERE id=? LIMIT 1";
			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->bind_param("i", $id);
			$this->stmt->execute();
			$this->stmt->store_result();

			if( $this->stmt->num_rows == 1)
				return true;

			$this->logoutUser();

			return false;
		}
		
		/**
		* Populates the current users data information for 
		* quick access as an object.
		*
		* @return void
		*/	
		
		private function _populateUserdata()
		{
			$this->userdata = array();
			
			if( $this->logged_in )
			{
				$id = $_SESSION[$this->sessionName]["id"];
				$data = $this->getInfoArray();
				foreach($data as $key => $value)
					$this->userdata[$key] = $value;

			}
		}

		/**
		* Generates a 128 len string used as a random salt for
		* securing you oneway encrypted password
		*
		* @return String with 128 characters
		*/

		private function _generateSalt()
		{
			$salt = null;

			while( strlen($salt) < 128 )
				$salt = $salt.uniqid(null, true);

			return substr($salt, 0, 128);
		}


	}
	
?>