<?php

require_once 'LinkedList.class.php';

class DB {
  private $connection = NULL;
  private $result     = NULL;
  private $counter    = NULL;
  public  $insert_id  = 0;

  private $errors;

  const TABLE_USER = 'app_user';
  const TABLE_CATEGORY = 'app_category';
  const TABLE_SOURCES = 'app_sources';
  const TABLE_ENTRY = 'app_entry';
  const TABLE_LABEL = 'app_labels';
  const TABLE_ADDRESSES = 'app_addresses';
  const TABLE_COUNTRIES = 'app_countries';

  public function __construct($host=NULL, $database=NULL, $user=NULL, $pass=NULL){
    $this->errors = new LinkedList();

    $this->connection = new mysqli($host,$user,$pass,$database);
    if ($mysqli->connect_errno) {
      $errors->add("Failed to connect to MySQL: " . $mysqli->connect_error);
    }
  }
 
  public function disconnect() {
    if ($this->connection != NULL)				
        $this->connection->close();
  }
 
  public function query($query) {
  	$this->result = $this->connection->query($query);
    $this->insert_id = $this->connection->insert_id;
  	$this->counter = NULL;
    return $this->result;
  }

  public function updateRow($table, $column, $where){
    if(is_array($column)){
      $set = "";
      foreach($column as $column => $value):
        $set .= $column ." = ". ((is_numeric($value)) ? $value : "'".$value."'") .", ";
      endforeach;
      $set .= substr($set, 0, strlen($set) - 2);
    }

    $sql = '
      UPDATE
        '. $table .'
      SET 
        '. $set .'
      WHERE
        '. $where .';';

    $this->query($sql);
    return $this->getResult();
  }
 
  public function fetchRow($resultmode = "fetch_assoc") {
    if($this->result != NULL)
    	return $this->result->{$resultmode}();
  }
 
  public function count() {
    if($this->counter == NULL) {
      $this->counter = $this->result->num_rows;
    }
    return $this->counter;
  }

  public function getResult(){
    return $this->result;
  }

  public function getErrors(){
    return $this->errors;
  }

  public function getLastError(){
    return $this->errors->getLast();
  }
}

?>
