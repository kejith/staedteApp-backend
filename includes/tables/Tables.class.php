<?php

class Table {
	public function getFormats(){
		return self::$FORMATS;
	}	

	public function getColumns(){
		return self::$COLUMNS;
	}
}

class EntryTable extends Table {
	const FLD_ID 			= 'entry_id';
	const FLD_TITLE 		= 'entry_title';
	const FLD_DESCRIPTION 	= 'entry_description';
	const FLD_LATITUDE 		= 'entry_latitude';
	const FLD_LONGITUDE 	= 'entry_longitude';
	const FLD_CATEGORY 		= 'entry_category';
	const FLD_ADDRESS 		= 'entry_address';

	private static $FORMATS = array(
			FLD_ID 			=> 'd',
			FLD_TITLE 		=> 's',
			FLD_DESCRIPTION => 's',
			FLD_LATITUDE 	=> 's',
			FLD_LONGITUDE 	=> 's',
			FLD_CATEGORY 	=> 'd',
			FLD_ADDRESS 	=> 's'
	);

	private static $COLUMNS = array('entry_id', 'entry_title', 'entry_description', 'entry_latitude', 'entry_longitude', 'entry_category', 'entry_address' );
}

class UserTable extends Table {
	const FLD_ID 			= 'user_id';
	const FLD_EMAIL			= 'user_email';
	const FLD_NAME 			= 'user_name';
	const FLD_PASSWORD 		= 'user_pwd';
	const FLD_SID 			= 'user_sid';

	public static $FORMATS = array(
			FLD_ID 			=> 'd',
			FLD_EMAIL 		=> 's',
			FLD_NAME 		=> 's',
			FLD_PASSWORD 	=> 's',
			FLD_SID 		=> 's'
	);

	private static $COLUMNS = array('user_id', 'user_email', 'user_name', 'user_pwd', 'user_sid');
}

class LabelTable extends Table {
	const FLD_ID 			= 'label_id';
	const FLD_CATEGORY_ID	= 'label_category_id';
	const FLD_ENTRY_ID		= 'label_entry_id';

	public static $FORMATS = array(
			FLD_ID => 'd',
			FLD_ID => 'd',
			FLD_ID => 'd',
	);

	private static $COLUMNS = array('label_id', 'label_category_id', 'label_entry_id');
}

class CategoryTable extends Table {
	const FLD_ID 			= 'category_id';
	const FLD_NAME			= 'category_name';
	const FLD_PARENT		= 'category_parent';

	public static $FORMATS = array(
			FLD_ID 		=> 'd',
			FLD_NAME 	=> 's',
			FLD_PARENT  => 'd',
	);

	private static $COLUMNS = array('category_id', 'category_name', 'category_parent');
}

class SourcesTable extends Table {
	const FLD_ID 			= 'sources_id';
	const FLD_LINK			= 'sources_link';
	const FLD_PARENT		= 'sources_parent';
	const FLD_TYPE 			= 'type';

	public static $FORMATS = array(
			FLD_ID 		=> 'd',
			FLD_LINK 	=> 's',
			FLD_PARENT 	=> 'd',
			FLD_TYPE 	=> 'd'
	);

	private static $COLUMNS = array('category_id', 'category_name', 'category_parent');
}

class AddressesTable extends Table {
	const FLD_ID = 'addresses_id';
	const FLD_TITLE = 'addresses_title';
	const FLD_STREET = 'addresses_street';
	const FLD_STREET_NUMBER = 'addresses_street_number';
	const FLD_ZIPCODE = 'addresses_zipcode';
	const FLD_CITY = 'addresses_city';
	const FLD_COUNTRY = 'addresses_country';
	const FLD_ENTRY_ID = 'addresses_entry_id';

	private static $COLUMNS = array('addresses_id', 'addresses_title', 'addresses_street', 'addresses_street_number', 'addresses_zipcode', 'addresses_city', 'addresses_country', 'addresses_entry_id');
}

class CountriesTable extends Table {
	const FLD_ID = 'countries_id';
	const FLD_TITLE = 'countries_name';
	const FLD_STREET = 'countries_country_code';
	const FLD_STREET_NUMBER = 'countries_language_name';

	private static $COLUMNS = array('countries_id', 'countries_name', 'countries_country_code', 'countries_language_name');
}

?>