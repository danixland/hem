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

		<div>
			<a href="admin/login.php">login</a>
		</div>

		<div>
			<a href="admin/login.php?action=newuser">new user</a>
		</div>

	</body>
</html>