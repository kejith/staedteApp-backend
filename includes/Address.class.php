<?php

class Address {

	public $id 				= 0;
	public $title 			= "";

	public $street 			= "";
	public $street_number 	= "";

	public $zipcode 		= 0;
	public $city 			= "";

	public $country 		= "";

	public function __construct($a){
		$this->id 				= $a->{AddressesTable::FLD_ID};
		$this->title 			= $a->{AddressesTable::FLD_TITLE};
		$this->street 			= $a->{AddressesTable::FLD_STREET};
		$this->street_number 	= $a->{AddressesTable::FLD_STREET_NUMBER};
		$this->zipcode 			= $a->{AddressesTable::FLD_ZIPCODE};
		$this->city 			= $a->{AddressesTable::FLD_CITY};
		$this->country 			= $a->{AddressesTable::FLD_COUNTRY};
	}

	public function update($columns){
		$this->db->updateRow(
			DB::TABLE_ADDRESSES, // Table
			$columns, // Columns
			AddressesTable::FLD_ID." = ". $this->id // Where in SQL
		);
	}
}