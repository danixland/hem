<?php

	/**
	* Make sure you started your'e sessions!
	* You need to include hem.inc.php to make SimpleUsers Work
	* After that, create an instance of SimpleUsers and your'e all set!
	*/

	session_start();
	require_once(dirname(__FILE__)."/hem.inc.php");

	$hemUsers = new hemUsers();

	// Login from post data
	if( isset($_POST["username"]) )
	{

		// Attempt to login the user - if credentials are valid, it returns the users id, otherwise (bool)false.
		$res = $hemUsers->loginUser($_POST["username"], $_POST["password"]);
		if(!$res)
			$error = "You supplied the wrong credentials.";
		else
		{
				header("Location: index.php");
				exit;
		}

	} // Validation end

get_header();
?>
	<body>

		<h1>Login</h1>

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
				<input type="text" name="password" id="password" />
			</p>

			<p>
				<input type="submit" name="submit" value="Login" />
			</p>

		</form>

	</body>
</html>
