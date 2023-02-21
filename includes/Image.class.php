<?php

class Image {
	public $id	 		= 0;
	public $link 		= "";
	public $filename 	= "";

	public function __construct($id, $link){
		$this->id = $id;
		$this->link = $link;
		$pu = parse_url($link);
		$this->filename = str_replace("image_name=", "",  $pu["query"]);
	}
}

?>