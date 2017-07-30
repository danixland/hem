<?php
	session_start();
	require_once(dirname(__FILE__)."/hem.inc.php");

	$hemUsers = new hemUsers();

get_header();
?>
	<body>

		<h1>Home Economy Manager</h1>

		<div>
			<ul>
				<li>
					<a href="admin/login.php">login</a>
				</li>
				<li>
					<a href="admin/login.php?action=newuser">new user</a>
				</li>
				<?php if ( $hemUsers->logged_in ) : ?>
				<ul>
					<li>
						<a href="transactions.php">transactions</a>
					</li>
					<li>
						<a href="accounts.php">accounts</a>
					</li>
				</ul>
				<?php endif; ?>
			</ul>
		</div>


	</body>
</html>