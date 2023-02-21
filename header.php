<?php

include 'common.php';

// if(!$user->is_logged_in()){
//	header('Location: login.php');
// }
?>
<!DOCTYPE html>
<html>
<head>
	<title>NKN - Login</title>	
	<script src="includes/ckeditor/ckeditor.js"></script>
	<link rel="stylesheet" href="includes/ckeditor/sample.css">
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="admin-menu-back"></div>
<div class="admin-menu">
	<ul>
		<li>
			<a href="entries.php">
			<div class="icon page"></div>
			<div class="menu-item-name">Entries</div>
			</a>
		</li>
		<li>
			<a href="labels.php">
			<div class="icon page"></div>
			<div class="menu-item-name">Labels</div>
			</a>
		</li>
		<li>
			<a href="media.php">
			<div class="icon page"></div>
			<div class="menu-item-name">Media</div>
			</a>
		</li>
	</ul>
</div>
<div class="admin-content">