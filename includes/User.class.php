<?php

class User {
	private $session;
	private $username;
	private $pwd;
	private $db;

	public function __construct($session){
		$this->username = $session['user_name'];
		$this->session = $session['session_id'];
		$this->pwd = $session['user_pwd'];
	}

	private function is_session_alright(){
		$this->db->query('SELECT * FROM '. DB::TABLE_USER .' WHERE user_sid = \''. $this->session .'\';');
		$user = $this->db->fetchRow('fetch_object');

		if($user->user_pwd == $this->pwd && $user->user_name == $this->username){
			$this->db->query("
				UPDATE 
					". DB::TABLE_USER ." 
				SET 
					user_sid = '". $this->session ."' 
				WHERE 
					user_name = '". $this->username ."'"
			);

			return true;
		}

		return false;
	}

	public function is_logged_in(){
		return $this->is_session_alright();
	}

	public function getUsername(){
		return $this->username;
	}

	public function getPassword(){
		return $this->pwd;
	}

	public function getSessionId(){
		return $this->session;
	}

	public function setDB($db){
		$this->db = $db;
	}
	
}

?>