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

		<?php $install_file = "admin/install.php";
		if ( file_exists($install_file) ) : ?>
		<div>
			<h2>CAREFUL, THE FILE INSTALL.PHP IS STILL AVAILABLE!!</h2>
			<p>in a production environment it is advised to remove or rename the file install.php in order to avoid wiping the database by mistake.</p>
		</div>
		<?php endif; ?>

	</body>
</html>