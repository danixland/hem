<?php
	session_start();
	require_once(dirname(__FILE__)."/hem.inc.php");

	$hemUsers = new hemUsers();
	$hemBanking = new hemBanking();

$pagetitle = "main page";

get_header($pagetitle);
?>

		<section>
			<article>
				<p>what are you doing today?</p>
				<ul>
					<li><a href="transactions.php">transactions</a></li>
					<li><a href="accounts.php">accounts</a></li>
				</ul>
			</article>

			<article>
				<pre>
					<?php print_r($_SESSION) ?>
				</pre>
			</article>

			<article>
				<pre>
					<?php print_r($hemUsers->userdata); ?>
				</pre>
			</article>

			<article>
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
			</article>

			<article>
				<pre>
					<?php
					$users = $hemUsers->getUsers();
					print_r($users);
					?>
				</pre>
			</article>

			<article>
				<h2>testing ID</h2>
				<pre><?php $idd = $hemBanking->echoing();
				var_dump( $idd ); ?></pre>
			</article>

		</section>

<?php get_footer(); ?>
