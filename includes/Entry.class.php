<?php

class Entry {
	private $db 			= NULL;
	private $Media 			= NULL;

	public $id 				= 0;
	public $title 			= "";
	public $description 	= "";

	public $labels			= array();
	public $images 			= array();
	public $addresses 		= array();

	public $fetch 			= array();

	public function __construct($id, $refresh = true){
		$this->db = IoC::resolve('database');
		$this->media = IoC::resolve('Media');
		$this->id = $id;

		if($refresh) 
			$this->refresh();
	}

	public function refresh(){
		$echo = "";
		if(!$this->selectEntry()) 		$echo .= 'Error -> selectEntry<br>';
		if(!$this->selectLabels()) 		$echo .= 'Error -> selectLabels<br>';
		if(!$this->selectImages()) 		$echo .= 'Error -> selectImages<br>';
		if(!$this->selectAddresses()) 	$echo .= 'Error -> selectAddresses<br>';

	}

	private function selectEntry(){
		$sql = "
			SELECT
				*
			FROM
				". DB::TABLE_ENTRY ."
			WHERE
				". EntryTable::FLD_ID ." = ". $this->id; 

		$result = $this->db->query($sql);

		if($result === false || $result->num_rows != 1)
				return false;

		$entry = $result->fetch_object();

		$this->title = $entry->{EntryTable::FLD_TITLE};
		$this->description  = $entry->{EntryTable::FLD_DESCRIPTION};

		return true;
	}

	private function selectLabels(){
		$sql = "
			SELECT
				*
			FROM
				". DB::TABLE_LABEL ."
			JOIN ". DB::TABLE_CATEGORY ." ON ". CategoryTable::FLD_ID ." = ". LabelTable::FLD_CATEGORY_ID ."
			WHERE
				". LabelTable::FLD_ENTRY_ID ." = ". $this->id;

		$result = $this->db->query($sql);

		if($result == false && $result->num_rows == 0)
			return false;

		while($label = $result->fetch_object())
			$this->labels[] = array($label->label_id => $label->category_name);

		return true;
	}

	private function selectAddresses(){
		$sql = "
			SELECT
				*
			FROM
				". DB::TABLE_ADDRESSES ."
			JOIN ". DB::TABLE_COUNTRIES ." ON ". AddressesTable::FLD_ID ." = ". CountriesTable::FLD_ID ."
			WHERE
				". AddressesTable::FLD_ENTRY_ID ." = ". $this->id;

		$result = $this->db->query($sql);

		if($result == false && $result->num_rows == 0)
			return false;

		while($a = $result->fetch_object())
			$this->addresses[] = new Address($a);

		return true;
	}

	private function selectImages(){
		$sql = "SELECT 
					*
				FROM 
					". DB::TABLE_SOURCES ." 
				WHERE 
					type = 0 AND sources_parent = ". $this->id .";";

		$result = $this->db->query($sql);

		if($result == false && $result->num_rows <= 0)
			return false;

		while($image = $result->fetch_object()){
			$this->images[] = new Image($image->{SourcesTable::FLD_ID}, "http://www.lp-together.de/app/getImage.php?image_name=". basename($image->{SourcesTable::FLD_LINK}));
		}

		return true;
	}

	public function update($columns){
		$this->db->updateRow(DB::TABLE_ENTRY, $columns, "entry_id = ". $this->id);
	}

	public function delete(){
		// Through Constraints in the Database we only have to delete
		// the entry it will cascadingly delete referenced data
		// FOREIGN KEY FTW! If u dont know it, than learn it!!
		// Foreign Key: http://bit.ly/iXLCgH
		$sql = "
			DELETE FROM
				". DB::TABLE_ENTRY ."
			WHERE
				". EntryTable::FLD_ID ." = ". $this->id .";";

		$result = $this->db->query($sql);
		if($result == false || $result == NULL)
			return false;

		return true;
	}

	public function deleteLabel($labelID){
		$sql = "
			DELETE FROM
				". DB::TABLE_LABEL ."
			WHERE
				". LabelTable::FLD_ID ." = ". $labelID .";";

		$result = $this->db->query($sql);

		if($result == false || $result == NULL)
			return false;

		return true;
	}

	private function deleteImage($imageID){

	}

	public function getImages(){
		return $this->images;
	}

	public function getAddresses(){
		return $this->addresses;
	}

	public static function getEntries($category_id = 0){
		$db = IoC::resolve("database");

		if(isset($category_id) && $category_id != 0)
			$where .= " WHERE label_category_id = ". $_GET['category_id'] ." ";

		$sql = "
			SELECT
				*
			FROM 
				 ". DB::TABLE_LABEL ."
			JOIN ". DB::TABLE_ENTRY ." ON entry_id = label_entry_id
			JOIN ". DB::TABLE_CATEGORY ." ON category_id = label_category_id
			". $where ."
			GROUP BY
				entry_title;";

		$db->query($sql);
		$result = $db->getResult();

		$entries = array();
		while($entry = $result->fetch_object()){
			$entries[] = new Entry($entry->entry_id);
		}

		return $entries;
	}

	public function addLabel($category_id, $id){
		$sql = "INSERT INTO ". DB::TABLE_LABEL ." (". LabelTable::FLD_CATEGORY_ID .", ". LabelTable::FLD_ENTRY_ID .") VALUES(". $category_id .",". $this->id .");";
		echo $sql;
		return $this->db->query($sql) != false;		
	}

}