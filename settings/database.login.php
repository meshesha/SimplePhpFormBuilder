<?php
$server = 'localhost';
$username = 'root';
$password = 'koll34ll';
$database = 'formbuilder';

try{
	$conn = new PDO("mysql:host=$server;dbname=$database;", $username, $password);
} catch(PDOException $e){
	die( "Connection failed: " . $e->getMessage());
}