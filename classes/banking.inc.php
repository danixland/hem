<?php

class hemBanking extends hemUsers {

	public function echoing() {
		$userid = parent::getID();

		return $userid;
	}

}

?>