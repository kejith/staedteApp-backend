<?php

class Login {
	private $db 				= null;
	private $username 			= "";
	private $pwd 				= "";

	private $is_user_logged_in 	= false;

	public $errors 				= array();
	public $messages			= array();

	public function __construct(){
		// start session
		session_start();
	}

	public function userExists(){
		$this->db->query("SELECT * FROM ". DB::TABLE_USER ." WHERE user_name = '". $this->username."'");
		return ($this->db->count() == 1);
	}

	public function passwordCorrect(){
		$this->db->query("SELECT * FROM ". DB::TABLE_USER ." WHERE user_name = '". $this->username ."' AND user_pwd = '". md5($this->pwd)."'");
		return ($this->db->count() == 1);
	}

	public function createLogin(){
		if(!$this->userExists()){
			$this->errors[] = "Benutzername existiert nicht";
			return false;
		}

		if(!$this->passwordCorrect()){
			$this->errors[] = "Passwort falsch";
			return false;
		}

        $_SESSION['user_name'] = $this->username;
        $_SESSION['user_pwd'] = md5($this->pwd);
        $_SESSION['user_logged_in'] = 1;

        echo strlen(session_id());

        // set the login status to true
        $this->user_is_logged_in = true; 

		$this->db->query("
			UPDATE ". DB::TABLE_USER ." SET user_sid = '". session_id() ."' WHERE user_name = '". $this->username ."';
			SELECT * FROM ". DB::TABLE_USER .";"
		);

		$this->db->fetchRow();

		return true;

	}

	public function setDB($db){
		$this->db = $db;
	}

	public function setUsername($name){
	    // is magic quotes accessable
	    if ( get_magic_quotes_gpc() )
	        $name = stripslashes($name);

	    // escape Backticks (`)
	    $name = preg_replace('/x60/', '\x60', $name);
	    // escape %, _
	    $name = str_replace('%', '%', $name);
	    $name = str_replace('_', '_', $name);

		$this->username = $name; 
	}

	public function setPassword($pwd){
	    // is magic quotes accessable
	    if ( get_magic_quotes_gpc() )
	        $pwd = stripslashes($pwd);

	    $this->pwd = $pwd; 
	}
}

?>