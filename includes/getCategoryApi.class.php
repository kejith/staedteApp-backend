<?php

class GetCategoryApi extends GetApiXML
{

	protected $filepath = './cache/categoryapi.xml';
	
	function getData(){
		$this->query = $this->db->query('
			SELECT 
				category_id,
				category_name,
				category_parent
			FROM 
				app_category
			LEFT JOIN
				app_sources
			ON 
				app_category.category_id = app_sources.sources_parent
			');	
	}

	function createXML(){
	
		$this->getData();

		$rootEle = $this->xml->createElement('categories');
		$this->xml->appendChild($rootEle);	
		while( $entry = $this->db->fetchRow() ){

			$entryEle = $this->xml->createElement('category');
			$id = -1;
			foreach( $entry as $key => $value ){
				$ele = $this->xml->createElement($key,htmlspecialchars_decode(strip_tags(stripslashes(stripslashes($value)))));
				$entryEle->appendChild($ele);
			}

			$rootEle->appendChild($entryEle);
		}		
	}

}


?>
