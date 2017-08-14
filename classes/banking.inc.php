<?php

class hemBanking extends hemUsers {

	public function echoing() {
		$data = parent::getID();

		return $data["id"];
	}

}

?>