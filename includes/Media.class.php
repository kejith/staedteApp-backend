<?php

class Media {
	private $errors = array();
	private $db = NULL;
	private $im = NULL;

	public function saveUploadedImages($id, $images){
		$this->errors = array();

		$picDir = './pictures/';
		foreach($images as $image):						
			$pathinfo = pathinfo($image['name']);
			$filePath = $this->generateFilename($picDir).".".$pathinfo['extension'];
			if(move_uploaded_file($image['tmp_name'], $filePath)){
				$sql = "
					INSERT INTO 
						". DB::TABLE_SOURCES ." 
					(". SourcesTable::FLD_LINK .", ". SourcesTable::FLD_PARENT .", ". SourcesTable::FLD_TYPE .")
					VALUES('". basename($filePath) ."', ". $id .",0);";

				$result = $this->db->query($sql);
			} else {
				echo "Image ". $image['name'] ." could not be uploaded.";
			}

			return $this->error;
		endforeach;
	}

	private function generateRandomString($length = 24) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, strlen($characters) - 1)];
	    }
	    return $randomString;
	}

	private function generateFilename($path){
		while(true){
			$filename = $this->generateRandomString();
			$filePath = $path . $filename;

			if(!file_exists($filePath))
				return $filePath;
		}
	}

	public function deleteSource($imageID){
		$sql = "DELETE FROM ". DB::TABLE_SOURCES ." WHERE ". SourcesTable::FLD_ID ." = ". $imageID .";";
		$result = $this->db->query($sql);

		if($result == false || $result == NULL)
			return false;

		return true;
	}

	public function setDB($db){
		$this->db = $db;
	}

	public function setImageManipulation($im){
		$this->im = $im;
	}

	public function printImage($filename, $width = "", $height = ""){
		$im = new ImageManipulation();

		$link = "./pictures/". $filename;
		$im->setImagePath($link);
		if($width != "" && $height != ""){
			$link = $im->resizeImage($width, $height);
		}


		$im->setImagePath($link);
		$image = $im->imagecreatefrom($link);

		$pi = pathinfo($link);

	    switch($pi['extension'])
		{
			case 'jpg':
			case 'jpeg':
				header('Content-type: image/jpeg');
				imagejpeg($image);
				break;
			case 'gif':
				header('Content-type: image/gif');
				imagegif($image);
				break;
			case 'png':
				header('Content-type: image/png');
				imagepng($image);
				break;
		}
	}

	public function getLink($filename, $width = "", $height = ""){
		$pictures = "pictures/";
		$resize = "";
		if($width != "" && $height != ""){
			$pictures = "cache/".$pictures;
			$resize = "&width=".$width ."&height=". $height;
		}
		return "http://www.lp-together.de/app/getImage.php?image_name=".$filename . $resize;
	}

}

?>