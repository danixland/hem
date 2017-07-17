<?php
	session_start();
	require_once(dirname(__FILE__)."/hem.inc.php");

	$hemUsers = new hemUsers();

get_header();

// This is a simple way of validating if a user is logged in or not.
// If the user is logged in, the value is (bool)true - otherwise (bool)false.
if( !$hemUsers->logged_in ) {
	header("Location: admin/login.php");
	exit;
}

$user = $hemUsers->getSingleUser();
if( !$user )
	die("The user could not be found...");

$secret = $hemUsers->genToken( "logout" );
$gravatar = $hemUsers->get_gravatar( $user["user_email"], 100, "identicon", "x", false );
?>
	<body>

		<h1>Home Economy Manager</h1>

		<img src="<?php echo $gravatar; ?>" alt="<?php echo $user['display_name']; ?>'s gravatar">
		<h2>Hello <strong><?php echo $user["display_name"]; ?></strong></h2>

		<div>
			<form action="admin/login.php?action=logout" method="post">
                <p>
                    <input type="hidden" name="<?php echo $secret["name"]; ?>" value="<?php echo $secret["token"]; ?>">
                    <input type="submit" name="submit" value="logout" />
                </p>
			</form>
		</div>
		<div>
			<ul>
				<li><a href="transactions.php">transactions</a></li>
			</ul>
		</div>

		<div>
			<pre>
				<?php print_r($_SESSION) ?>
			</pre>
		</div>

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
					<td><?php echo $user["user_status"]; ?></td>
					<td><?php echo $user["display_name"]; ?></td>
				</tr>
			</table>
		</div>

		<div>
			<pre>
				<?php
				$users = $hemUsers->getUsers();
				print_r($users);
				?>
			</pre>
		</div>

	</body>
</html>