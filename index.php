<?php

	/**
	* Make sure you started your'e sessions!
	* You need to include su.inc.php to make SimpleUsers Work
	* After that, create an instance of SimpleUsers and your'e all set!
	*/

	session_start();
	require_once(dirname(__FILE__)."/hem.inc.php");

	$hemUsers = new hemUsers();

get_header();
?>
	<body>

		<h1>Home Economy Manager</h1>

		<p>
			<?php
			// This is a simple way of validating if a user is logged in or not.
			// If the user is logged in, the value is (bool)true - otherwise (bool)false.
			if( !$hemUsers->logged_in ) {
				header("Location: login.php");
				exit;
			}

			$id = $_SESSION[$this->sessionName]["id"];
			$user = $hemUsers->getSingleUser($id);
			if( !$user )
				die("The user could not be found...");

			?>
		</p>
		<div>
			<table>
				<tr>
					<td>ID</td>
					<td>login</td>
					<td>email</td>
					<td>reg. date</td>
					<td>status</td>
					<td>display name</td>
				</tr>
				<tr>
					<td><?php echo $user["id"]; ?></td>
					<td><?php echo $user["user_login"]; ?></td>
					<td><?php echo $user["user_email"]; ?></td>
					<td><?php echo $user["user_registered"]; ?></td>
					<td><?php ( $user["user_status"] == 1 ? echo "user" : echo "admin"); ?></td>
					<td><?php ( !empty($user["display_name"]) ? echo $user["display_name"] : echo $user["user_login"]); ?></td>
				</tr>
			</table>
		</div>

	</body>
</html>