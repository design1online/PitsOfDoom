<?php
/****************
* File: dbconnect.php
* Date: 12.10.2008
* Author: design1online.com
* Purpose: connect to the database, setup session and debugger
*
* Note: You'll need to add your own password and username info.
*****************/
session_start(); //start the sessions
$_SESSION['debug'] = 0; //turn the debugger on

$_host = "localhost"; //usually localhost or 127.0.0.1
$_username = "root"; //database username
$_password = ""; //database password
$_db = "lesson8"; //database name

//create the database connection if we don't already have one
if (!isset($_SESSION['database'])) {
	$_SESSION['database'] = new database($_host, $_username, $_password, $_db);
} else {
	$database = $_SESSION['database'];
}