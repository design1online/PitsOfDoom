<?php
/****************
* File: dbconnect.php
* Date: 12.10.2008
* Author: design1online.com
* Purpose: connect to the database, setup session and debugger
*****************/
session_start(); //start the sessions
$_SESSION['debug'] = 0; //turn the debugger on

$_host = "localhost";
$_username = "your_login_goes_here";
$_password = "your_password_goes_here";
$_db = "your_database_name_goes_here";

//create the database connection if we don't already have one
if (!$_SESSION['database'])
	$database = $_SESSION['database'] = new database($_host, $_username, $_password, $_db);
else
	$database = $_SESSION['database'];

//connect to the database and remove possible SQL injections from the $_POST data
$database->connect();
$_POST = makeClean($_POST);
?>