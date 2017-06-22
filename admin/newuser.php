<?php

	/**
	* Make sure you started your'e sessions!
	* You need to include su.inc.php to make SimpleUsers Work
	* After that, create an instance of SimpleUsers and your'e all set!
	*/

	session_start();
	require_once(dirname(__FILE__)."/hem.inc.php");

	$hemUsers = new hemUsers();

	// Validation of input
	if( isset($_POST["username"]) )
	{
		if( empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["email"]) )
			$error = "You have to choose a username and a password and provide a valid email address";
    else
    {
    	if( empty($_POST["display_name"])) {
    		$display_name = $_POST["username"];
    	} else {
    		$display_name = $_POST["display_name"];
    	}
    	// All fields have input - now try to create the user.
    	// If $res is (bool)false, the username is already taken.
    	// Otherwise, the user has been added, and we can redirect to some other page.
			$res = $hemUsers->createUser($_POST["username"], $_POST["password"], $_POST["email"], $display_name);

			if(!$res)
				$error = "Username already taken.";
			else
			{
				header("Location: install.php");
				exit;
			}
		}

	} // Validation end

get_header();
?>
	<body>

		<h1>Register new user</h1>

		<?php if( isset($error) ): ?>
		<p>
			<?php echo $error; ?>
		</p>
		<?php endif; ?>

		<form method="post" action="">
			<p>
				<label for="username">Username:</label><br />
				<input type="text" name="username" id="username" />
			</p>

			<p>
				<label for="password">Password:</label><br />
				<input type="password" name="password" id="password" />
			</p>

			<p>
				<label for="email">email:</label><br />
				<input type="email" name="email" id="email" />
			</p>

			<p>
				<label for="display_name">display name:</label><br />
				<input type="text" name="display_name" id="display_name" />
			</p>

			<p>
				<input type="submit" name="submit" value="Register" />
			</p>

		</form>

	</body>
</html>