<?php

class EntryTable {
	const FLD_ID 			= 'entry_id';
	const FLD_TITLE 		= 'entry_title';
	const FLD_DESCRIPTION 	= 'entry_description';
	const FLD_LATITUDE 		= 'entry_latitude';
	const FLD_LONGITUDE 	= 'entry_longitude';
	const FLD_CATEGORY 		= 'entry_category';
	const FLD_ADDRESS 		= 'entry_address';

	public static final FORMATS = array(
			FLD_ID 		=> 'd',
			FLD_TITLE 		=> 's',
			FLD_DESCRIPTION => 's',
			FLD_LATITUDE 	=> 's',
			FLD_LONGITUDE 	=> 's',
			FLD_CATEGORY 	=> 'd',
			FLD_ADDRESS 	=> 's'
	);
}

class UserTable {
	const FLD_ID 			= 'user_id';
	const FLD_EMAIL			= 'user_email';
	const FLD_NAME 			= 'user_name';
	const FLD_PASSWORD 		= 'user_pwd';
	const FLD_SID 			= 'user_sid';

	public static final FORMATS = array(
			FLD_ID 			=> 'd',
			FLD_EMAIL 		=> 's',
			FLD_NAME 		=> 's',
			FLD_PASSWORD 	=> 's',
			FLD_SID 		=> 's'
	);
}

class LabelTable {
	const FLD_ID 			= 'label_id';
	const FLD_CATEGORY_ID	= 'label_category_id';
	const FLD_ENTRY_ID		= 'label_entry_id';

	public static final FORMATS = array(
			FLD_ID => 'd',
			FLD_ID => 'd',
			FLD_ID => 'd',
	);
}

?>