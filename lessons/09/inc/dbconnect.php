<?php
/****************
* File: dbconnect.php
* Date: 12.10.2008
* Author: design1online.com
* Purpose: connect to the database, setup session and debugger
*****************/
session_start(); //start the sessions
$_SESSION['debug'] = 0; //turn the debugger on

$_host = "localhost"; //typically localhost or 127.0.0.1
$_username = "root"; //database username
$_password = ""; //database password
$_db = "lesson9"; //database name

//create the database connection if we don't already have one
if (!$_SESSION['database']) {
	$_SESSION['database'] = new database($_host, $_username, $_password, $_db);
}

//connect to the database
$_SESSION['database']->connect();

// sanatize inputs from possible SQL injections
$_POST = $_SESSION['database']->makeClean($_POST);
$_GET = $_SESSION['database']->makeClean($_GET);