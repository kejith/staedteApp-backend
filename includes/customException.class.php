<?php 

error_reporting(E_ALL-E_NOTICE); 


// custom Exception
class customException extends Exception 
{ 
    
    public function __construct($message, $code=0) { 
        parent::__construct($message,$code); 
    } 
    
	public function errorMessage() {
	
		$errorMsg = '
		<style type="text/css">
			div.php-error { border: 1px solid red; }
			div.php-error-type, div.php-error-information { background-color: #ddd; padding: 2px 10px; }
			div.php-error-message { background-color: #fefefe; padding: 10px; }
		</style>
		</head>
		<body>
		<div class="php-error">
		<div class="php-error-type"><h2 style="margin-bottom: 0px;">Error</h2></div>
		<div class="php-error-information">
		<table>
			<tr>
				<th align="left" width="200">Type</th>
				<th align="left">Information</th>
			</tr>
			<tr>
				<td>Where:</td>
				<td><i>on line '.$this->getLine().' in '.$this->getFile() . '</i></td>
			</tr>
			<tr>
				<td>Class:</td>
				<td><i>'. __CLASS__ . '</i></td>
			</tr>
			<tr>
				<td>Error Description</td>
				<td><b>'.$this->getMessage().'</b></td>
			</tr>
		</table>
		</div>
		<div class="php-error-message">'. $this->code. '</div>
		<div class="php-error-message">'. $this->getTraceAsString() .'</div>
		';
		
		return $errorMsg;
	}
    
} 

?>
