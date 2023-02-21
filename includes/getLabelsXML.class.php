<?php

class GetLabelsApi extends GetApiXML
{

	protected $filepath = './cache/labelsapi.xml';
	
	function getData(){
		$this->query = $this->db->query('
			SELECT 
				*
			FROM 
				app_labels
			');	
	}

	function createXML(){
	
		$this->getData();

		$rootEle = $this->xml->createElement('labels');
		$this->xml->appendChild($rootEle);	
		while( $entry = $this->db->fetchRow() ){

			$entryEle = $this->xml->createElement('label');
			foreach( $entry as $key => $value ){
				$ele = $this->xml->createElement($key,htmlspecialchars_decode(strip_tags(stripslashes(stripslashes($value)))));
				$entryEle->appendChild($ele);
			}

			$rootEle->appendChild($entryEle);
		}		
	}

}


?>
