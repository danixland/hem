<?php

	/**
	 * This file is part of hem.
	 *
	 */

	/**
	* hemUsers is the class responsible of managing users inside hem
	*/

	class hemUsers
	{

		private $mysqli, $stmt;
		protected $sessionName = "hemUsers";
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
		}

		/**
		* Returns a (int)user id, if the user was created succesfully.
		* If not, it returns (bool)false.
		*
		* @param	username		The desired username
		* @param	password		The desired password
		* @param    email 			The user email address
		* @param 	display_name	The name to be displayed for that user
		*
		* @return	The user id or (bool)false (if the user already exists)
		*/

		public function createUser( $username, $password, $email, $display_name )
		{
			$salt = $this->_generateSalt(128);
			$password = $salt.$password;
			$activation_key = md5($salt.$email);
			$status = intval(1); # 1 is a simple user. 10 is admin
			# id, user_login, user_pass, salt, user_email, user_registered, activation_key, user_status, display_name
			$sql = "INSERT INTO users VALUES (NULL, ?, SHA1(?), ?, ?, NOW(), ?, ?, ?)";
			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->bind_param("sssssis", $username, $password, $salt, $email, $activation_key, $status, $display_name);
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

			$sql = "SELECT user_login, user_email FROM users WHERE id=? ORDER BY user_login ASC";
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

			$salt = $this->_generateSalt(128);
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
			
			$sql = "SELECT DISTINCT id, user_login, user_email, user_registered, user_status, display_name FROM users ORDER BY user_login ASC";

			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->execute();
			$this->stmt->store_result();

			if( $this->stmt->num_rows == 0)
				return array();

			$this->stmt->bind_result($id, $username, $email, $created, $status, $display_name);

			$users = array();

			$i = 0;
			while( $this->stmt->fetch() )
			{		
				$users[$i]["id"] = $id;
				$users[$i]["user_login"] = $username;
				$users[$i]["user_email"] = $email;
				$users[$i]["user_registered"] = $created;
				$users[$i]["user_status"] = $status;
				$users[$i]["display_name"] = $display_name;

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

			$sql = "SELECT id, user_login, user_email, user_registered, user_status, display_name FROM users WHERE id=? LIMIT 1";
			if( !$this->stmt = $this->mysqli->prepare($sql) )
				throw new Exception("MySQL Prepare statement failed: ".$this->mysqli->error);

			$this->stmt->bind_param("i", $id);
			$this->stmt->execute();
			$this->stmt->store_result();

			if( $this->stmt->num_rows == 0)
				return false;

			$this->stmt->bind_result($id, $username, $email, $created, $status, $display_name);
			$this->stmt->fetch();

			$user["id"] = $id;
			$user["user_login"] = $username;
			$user["user_email"] = $email;
			$user["user_registered"] = $created;
			$user["user_status"] = $status;
			$user["display_name"] = $display_name;

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

			$sql = "DELETE FROM accounts WHERE owner=?";
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
		
		public function genToken( $secret ) {
			$salt = $this->_generateSalt();
			$name = $secret . "_" . md5($salt);
			$token = sha1($secret . $salt);
			
			$_SESSION[$this->sessionName]["hem_csrf_name"] = $name;
			$_SESSION[$this->sessionName]["hem_csrf_token"] = $token;
			
			$string = array(
				"name" => $name,
				"token" => $token
			);

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
			$name = $_SESSION[$this->sessionName]["hem_csrf_name"];
			$token = $_SESSION[$this->sessionName]["hem_csrf_token"];
			unset($_SESSION[$this->sessionName]["hem_csrf_token"]);
			unset($_SESSION[$this->sessionName]["hem_csrf_name"]);
			
			if($_POST[$name] == $token)
				return true;
				
			return false;
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
		 * Get either a Gravatar URL or complete image tag for a specified email address.
		 *
		 * @param string $email The email address
		 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
		 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
		 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
		 * @param boole $img True to return a complete IMG tag False for just the URL
		 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
		 * @return String containing either just a URL or a complete image tag
		 * @source https://gravatar.com/site/implement/images/php/
		 */
		public function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
			$url = 'https://www.gravatar.com/avatar/';
			$url .= md5( strtolower( trim( $email ) ) );
			$url .= "?s=$s&d=$d&r=$r";
			if ( $img ) {
				$url = '<img src="' . $url . '"';
				foreach ( $atts as $key => $val )
					$url .= ' ' . $key . '="' . $val . '"';
				$url .= ' />';
			}
			return $url;
		}

		/**
		* Generates a 128 len string used as a random salt for
		* securing you oneway encrypted password
		*
		* @return String with 128 characters
		*/

		private function _generateSalt( $lenght = null )
		{
			$salt = null;

			if ( !$lenght ) {
				$lenght = 10;
			}

			while( strlen($salt) < $lenght )
				$salt = $salt.uniqid(null, true);

			return substr($salt, 0, 128);
		}


	}

?>
