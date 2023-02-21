<?php

class ImageManipulation {
	
	private $imagePath;
	private $image;

	const cacheDir = 'cache/pictures';

	public function setImagePath($imagePath){
		$this->imagePath = $imagePath;
		$this->image = $this->imagecreatefrom($this->imagePath);
	}

	public function resizeImage($width, $height, $crop = false){
		if($this->imageResizedExists($width, $height))
			return $this->getImagePath($width, $height);
		else
			return $this->resize_then_crop($width, $height);
	}

	public function getImagePath($width, $height, $crop = false){
		$fileExt = $this->getFileExtension();
		$fileName = $this->getFileName();

		return ImageManipulation::cacheDir . '/' . $fileName . '-' . $width . 'x' . $height . '.' . $fileExt;
	}

	public function getCacheDir(){
		return $this->cacheDir;
	}

	private function imageResizedExists($width, $height){
		$path = $this->getImagePath($width, $height);
		return file_exists($path);
	}

	private function getFileExtension(){
		$pathinfo = pathinfo($this->imagePath);
		return $pathinfo['extension'];
	}

	private function getFileName(){
		$pathinfo = pathinfo($this->imagePath);
		return $pathinfo['filename'];
	}

	public function imagecreatefrom(){
		switch($this->getFileExtension())
		{
			case 'jpg':
			case 'jpeg':
				return imagecreatefromjpeg($this->imagePath);
				break;
			case 'gif':
				return imagecreatefromgif($this->imagePath);
				break;
			case 'png':
				return imagecreatefrompng($this->imagePath);
				break;
		}
	}

	private function saveImage($image, $filename){
		switch($this->getFileExtension())
		{
			case 'jpg':
			case 'jpeg':
				return imagejpeg($image, $filename);
				break;
			case 'gif':
				return imagegif($image, $filename);
				break;
			case 'png':
				return imagepng($image, $filename);
				break;
		}
	}

	private function resize_then_crop( $imagethumbsize_w , $imagethumbsize_h, $red = 255, $green = 255, $blue = 255) {
		// Get new dimensions
		list($width, $height) = getimagesize($this->imagePath);
		$new_width = $width * $percent;
		$new_height = $height * $percent;

		if(preg_match("/.jpg/i", "$this->image"))
			$format = 'image/jpeg';

		if (preg_match("/.gif/i", "$this->image"))
			$format = 'image/gif';

		if(preg_match("/.png/i", "$this->image"))
			$format = 'image/png';

		switch($format)
		{
		case 'image/jpeg':
			$image = imagecreatefromjpeg($this->image);
			break;
		case 'image/gif';
			$image = imagecreatefromgif($this->image);
			break;
		case 'image/png':
			$image = imagecreatefrompng($this->image);
			break;
		}

		$width = $imagethumbsize_w ;
		$height = $imagethumbsize_h ;
		list($width_orig, $height_orig) = getimagesize($this->imagePath);

		if ($width_orig < $height_orig)
		  $height = ($imagethumbsize_w / $width_orig) * $height_orig;
		else
		    $width = ($imagethumbsize_h / $height_orig) * $width_orig;
		
		if ($width < $imagethumbsize_w) { //if the width is smaller than supplied thumbnail size 
			$width = $imagethumbsize_w;
			$height = ($imagethumbsize_w/ $width_orig) * $height_orig;;
		}

		if ($height < $imagethumbsize_h){ //if the height is smaller than supplied thumbnail size 
			$height = $imagethumbsize_h;
			$width = ($imagethumbsize_h / $height_orig) * $width_orig;
		}

		$thumb = imagecreatetruecolor($width , $height);  
		$bgcolor = imagecolorallocate($thumb, $red, $green, $blue);   
		ImageFilledRectangle($thumb, 0, 0, $width, $height, $bgcolor);
		imagealphablending($thumb, true);

		imagecopyresampled($thumb, $this->image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
		$thumb2 = imagecreatetruecolor($imagethumbsize_w , $imagethumbsize_h);
		// true color for best quality
		$bgcolor = imagecolorallocate($thumb2, $red, $green, $blue);   
		ImageFilledRectangle($thumb2, 0, 0, $imagethumbsize_w , $imagethumbsize_h , $white);
		imagealphablending($thumb2, true);

		$w1 =($width/2) - ($imagethumbsize_w/2);
		$h1 = ($height/2) - ($imagethumbsize_h/2);

		imagecopyresampled($thumb2, $thumb, 0,0, $w1, $h1, $imagethumbsize_w , $imagethumbsize_h ,$imagethumbsize_w, $imagethumbsize_h);
		$this->saveImage($thumb2, $this->getImagePath($imagethumbsize_w, $imagethumbsize_h));

		return $this->getImagePath($imagethumbsize_w, $imagethumbsize_h);
	}

}

/*class ImageManipulation {
	
	private $imagePath;
	private $image;

	private static $cacheDir = '/app/cache/pictures';

	function setImagePath($imagePath){
		$this->imagePath = $imagePath;
		$this->image = new Imagick($this->imagePath);
	}

	private function resizeImage($width, $height, $crop = false){
		$iHeight = $this->image->getImageHeight();
		$iWidth  = $this->image->getImageWidth();		

		$iAspect = (int) (($iWidth / $iHeight) * 100);
		$aspect  = (int) (($width / $height) * 100);

		if($iAspect < $aspect):
			$this->image->resizeImage($width, 0, Imagick::FILTER_CATROM, 1);
			if($crop) $this->image->cropImage($width, $height, $width, (int) (($this->image->getImageHeight() - $height) / 2));
		endif;

		if($iAspect > $aspect):			
			$this->image->resizeImage(0, $height, Imagick::FILTER_CATROM, 1);
			if($crop) $this->image->cropImage($width, $height, (int) (($this->image->getImageWidth() - $width) / 2), $height);
		endif;

		if($iAspect == $aspect)			
			$this->image->resizeImage($width, $height, Imagick::FILTER_CATROM, 1);

		return $this->image;
	}

	public function getImagePath($width, $height, $crop = false){
		$fileExt = $this->getFileExtension();
		$fileName = $this->getFileName();

		$file = ImageManipulation::cacheDir . '/' . $fileName . '-' . $width . 'x' . $height . '.' . $fileExt;

		if(!file_exists($file)){
			$this->resizeImage($width, $height, $crop);
			$this->writeImage($file);  	
		}

		return $file;
	}

	private function getFileExtension(){
		$pathinfo = pathinfo($imagePath);
		return $pathinfo['extension'];
	}

	private function getFileName(){
		$pathinfo = pathinfo($imagePath);
		return $pathinfo['filename'];
	}
}*/