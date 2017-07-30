<?php

	/**
   * This file is part of hem.
   *
   */

	/**
	* Installation script for hem
	* Did you remember to edit config.php? Otherwise, this script might fail
	* This script is fairly simple to use - open it in your browser, press the
	* install button and watch the magic...
	*/

	require_once(dirname(__FILE__)."/hem.inc.php");

	/* You shouldn't edit anything below this comment */

	$mysqli = new mysqli($GLOBALS["mysql_hostname"], $GLOBALS["mysql_username"], $GLOBALS["mysql_password"], $GLOBALS["mysql_database"]);
	if( $mysqli->connect_error )
	{
		$error = true;
		$title = "MySQL Connection failed!";
		$message = "Did you remember to edit config.php? MySQL said: ".$mysqli->connect_error;
	}

	if(isset($_POST["step"]))
		$install = true;


	$tables["users"] = "CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_login` varchar(128) NOT NULL,
  `user_pass` varchar(40) NOT NULL,
  `salt` varchar(128) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_registered` datetime NOT NULL,
  `activation_key` varchar(255) NOT NULL,
  `user_status` int(11) NOT NULL,
  `display_name` varchar(250) NOT NULL,
  `accounts` bigint(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_login` (`user_login`),
  UNIQUE KEY `user_email` (`user_email`)
	) ENGINE=MyISAM AUTO_INCREMENT=1 ;";

	$tables["accounts"] = "CREATE TABLE IF NOT EXISTS `accounts` (
  `id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `owner` bigint(11) UNSIGNED NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_type` int(11) NOT NULL,
  `main_account` int(1) NOT NULL,
  `aval_balance` decimal(15,2) NOT NULL,
  `counting_balance` decimal(15,2) NOT NULL,
  PRIMARY KEY `id` (`id`),
  UNIQUE KEY `account_name` (`account_name`)
	) ENGINE=MyISAM;";

	$tables["account_types"] = "CREATE TABLE IF NOT EXISTS `account_types` (
  `id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  PRIMARY KEY `id` (`id`),
  UNIQUE KEY `name` (`name`),
	) ENGINE=MyISAM;";

get_header();
?>
	<body>
		<h1>Installation of Home Economy Manager</h1>
			<?php if( isset($error) ): ?>

				<p>
					<strong><?php echo $title; ?></strong>
				</p>
				<p class="faded">
					<?php echo $message; ?>
				</p>

			<?php else: ?>
				<form method="post" action="">
					<?php if( !isset($install) ): ?>
					<p>
						<strong>Ready to install</strong>
					</p>
					<p>
						<input type="hidden" name="step" value="1" />
						When ready to proceed, press `Install!` - when your'e done, consider deleting, moving or renaming this install script for security purposes.<br />
					<p>
						<input type="submit" name="submit" value="Install!" />
					</p>
					<?php else: ?>

					<p>
						<strong>Creating database tables</strong>
					</p>

					<?php foreach( $tables as $table => $query ): ?>
					<p class="faded">
						<?php
							if( $stmt = $mysqli->prepare($query) )
							{
								if( $stmt->execute() )
									$status = "done...";
								else
									$status = "failed! MySQL returned: ".$stmt->error;
							}
							else
								$status = "failed! MySQL returned: ".$mysqli->error;
						?>
						Table `<?php echo $table; ?>`: <?php echo $status; ?>
					</p>
					<?php endforeach; ?>

					<?php endif; ?>
				</form>
			<?php endif; ?>
		<hr />
	</body>
</html>