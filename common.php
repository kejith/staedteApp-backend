<?php
// === Activate Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

$pathinfo = pathinfo(__FILE__);
define('ROOT', $_SERVER['HTTP_HOST']);

session_start();

// normalize files upload

$files = array();
$fix = function (&$files, $values, $prop) use (&$fix) {
    foreach ($values as $key => $value) {
        if (is_array($value)) {
            $fix($files[$key], $value, $prop);
        } else {
            $files[$key][$prop] = $value;
        }
    }
};

foreach ($_FILES as $name => $props) {
    foreach ($props as $prop => $value) {
        if (is_array($value)) {
            $fix($files[$name], $value, $prop);
        } else {
            $files[$name][$prop] = $value;
        }
    }
}

foreach($_POST as $key => $value){

}

// === Constants
if ( ! defined( 'MAX_MEMORY_LIMIT' ) ) {
	define( 'MAX_MEMORY_LIMIT', '256M' );
}

// === Includes
include 'includes/tables/Tables.class.php';
include 'includes/ioc.class.php';
include 'config.php';
include 'includes/database.class.php';
include 'includes/customException.class.php';
include 'includes/ImageManipulation.class.php';
include 'includes/entryapi.class.php';
include 'includes/getCategoryApi.class.php';
include 'includes/getLabelsXML.class.php';
include 'includes/login.class.php';
include 'includes/User.class.php';
include 'includes/InsertEntry.class.php';

// === Register IoC Objects
IoC::register('database', function(){
	global $mysql;

	$database = new DB(
	$mysql['host'],
	$mysql['database'],
	$mysql['user'],
	$mysql['password']
	);
	
	return $database;
});

IoC::register('GetEntryApi', function(){
	$entryApi = new GetEntryApi();
	$entryApi->setDB(IoC::resolve('database'));	
	return $entryApi;
});

IoC::register('GetCategoryApi', function(){
	$categoryApi = new GetCategoryApi();
	$categoryApi->setDB(IoC::resolve('database'));
	return $categoryApi;
});

IoC::register('GetLabelsApi', function(){
	$labelsApi = new GetLabelsApi();
	$labelsApi->setDB(IoC::resolve('database'));
	return $labelsApi;
});

IoC::register('Login', function(){
	$login = new Login();
	$login->setDB(IoC::resolve('database'));
	return $login;
});

IoC::register('User', function(){
	$_SESSION['session_id'] = session_id();
	$user = new User($_SESSION);
	$user->setDB(IoC::resolve('database'));
	return $user;
});

IoC::register('ImageManipulation', function(){
	$imageManipulation = new ImageManipulation();
	return $imageManipulation;
});

IoC::register('Media', function(){
	$media = new Media();
	$media->setDB(IoC::resolve('database'));
	return $media;
});

$user = IoC::resolve('User');
$db = IoC::resolve('database');


include 'includes/Media.class.php';
include 'includes/Image.class.php';
include 'includes/Entry.class.php';
include 'includes/Address.class.php';
include 'functions.php';

?>