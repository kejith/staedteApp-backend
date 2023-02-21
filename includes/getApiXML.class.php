<?php
abstract class GetApiXML
{

    protected $xml;
    protected $db;
    protected $query;

    protected $FIVE_MINUTES = '5';
    protected $filepath;

    function __construct(){
        $this->xml = new DOMDocument();
		if(!isset($this->filepath))
			  throw new LogicException(get_class($this) . ' must have a $filepath');
    }

    function setDB($db){
        if( $db == NULL )
            throw new Exception('Database-Object is not an Object');
    
        $this->db = $db;
    }

    function loadXML(){     

        if( file_exists($this->filepath) && 
            (time() - filemtime($this->filepath)) < $this->FIVE_MINUTES ){ 
            $this->xml->load($this->filepath);
        } else {
            $this->createXML();
            $this->xml->save($this->filepath);
        }

        Header('Content-type: text/xml');
        echo $this->xml->saveXML();
    }

    protected abstract function getData();
    protected abstract function createXML();

}
?>
