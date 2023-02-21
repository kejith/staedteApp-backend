<?php

include_once 'common.php';

$media = IoC::resolve("Media");

if(isset($_GET['image_name'])){
	if(isset($_GET['width']) && isset($_GET['height'])){
		$media->printImage($_GET['image_name'], $_GET['width'], $_GET['height']);
	} else {
		$media->printImage($_GET['image_name']);
	}
}

?>