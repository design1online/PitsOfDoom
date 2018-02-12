<?php
//turn on error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**************
* File: Logout.php (Lesson 7)
* 
* Assignment:
*		1) Allow the member to logout when they're done playing
*
* Techniques Taught This Lesson:
*		1) Destroying sessions
*   2) Calling class methods
**************/
include('classfiles.php'); //let's condense all our class files into one place
include('functions.php'); //include the functions
session_start();

//they haven't logged in yet, so there's no reason to see this page
if (!isset($_SESSION['member'])) {
	header("Location: login.php"); //redirect them to the login page
	exit; //use this so the rest of the page won't load
}

// reconnect to the database
$_SESSION['database']->connect();

// call our login function on the memberobj
$_SESSION['member']->logout();

// destroy the session
session_destroy();

// put them back to the login page
header("Location: login.php");
