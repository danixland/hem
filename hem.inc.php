<?php

		/**
   * This file is part of hem.
   *
	 */


	/**
	* This file includes what is needed by the simpleusers system
	*/

	$path = dirname(__FILE__);

	$hem_conf = include($path."/config.php");

	include($path."/classes/users.inc.php");
	include($path."/classes/banking.inc.php");

	require_once($path."/includes/functions.php");

?>