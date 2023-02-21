<?php

include_once 'getApiXML.class.php';

class GetEntryApi extends GetApiXML
{
	protected $filepath = './cache/entryapi.xml';
	
	function getData(){
		$this->query = $this->db->query('
			SELECT 
				entry_id, 
				entry_title,
				entry_description,
				entry_latitude,
				entry_longitude,
				entry_category,
				entry_address,
		       	app_category.category_name as category_name
			FROM 
				app_entry
			LEFT OUTER JOIN
				app_category
			ON 
				entry_category = app_category.category_id
		');	
	}



	function querySources(&$xmlNodeToAppend, &$database, $value){
		$database->query('SELECT * FROM app_sources WHERE sources_parent = '. $value .';');

		$sourceEle = $this->xml->createElement('sources');
		while( $source = $database->fetchRow() ){
			foreach($source as $sKey => $sValue){
				if($key == "sources_link")
					$value = "http://". $_SERVER['HTTP_HOST'] ."";
				$sourceEleItem = $this->xml->createElement($sKey,$sValue);
				$sourceEle->appendChild($sourceEleItem);
			}
		}
		$xmlNodeToAppend->appendChild($sourceEle);
	}

	function queryAddress(&$xmlNodeToAppend, &$database, $value){
		$result = $database->query('SELECT * FROM app_addresses WHERE addresses_entry_id = '. $value .';');

		if($result->num_rows > 0){
			$addressContainer = $this->xml->createElement('addresses');
			while( $address = $database->fetchRow() ){
				$addressEle = $this->xml->createElement('address');
				foreach($address as $sKey => $sValue){
					$addressEleItem = $this->xml->createElement($sKey,$sValue);
					$addressEle->appendChild($addressEleItem);
				}
				$addressContainer->appendChild($addressEle);
			}
			$xmlNodeToAppend->appendChild($addressContainer);
		}
	}

	function createXML(){	
		$this->getData();		
		$database = IoC::resolve('database');

		$rootEle = $this->xml->createElement('entries');
		$this->xml->appendChild($rootEle);	
		while( $entry = $this->db->fetchRow() ){

			$entryEle = $this->xml->createElement('entry');
			$id = -1;
			foreach( $entry as $key => $value ){
				$value = htmlentities($value);

				switch($key){
					case "entry_address":
						// ================= SELECT ADDRESS ==================================================================
					break;

					case "entry_id":
						// ================= SELECT SOURCES ==================================================================
						$this->querySources($entryEle, $database, $value);						
						$this->queryAddress($entryEle, $database, $value);

					default:
						// ================= FOR ALL OTHERS ==================================================================
						if($value != ""){
							$ele = $this->xml->createElement($key,htmlentities(strip_tags(stripslashes(stripslashes(strip_tags(html_entity_decode($value)))))));
							$entryEle->appendChild($ele);
						}
				}
			}

			$rootEle->appendChild($entryEle);
		}		
	}
}

?>
