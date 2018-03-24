<?php
/****************
* File: logout.php
* Date: 10.14.2008
* Author: jade@design1online.com
* Purpose: clear their session information
*****************/
include('inc/classfiles.php'); //let's condense all our class files into one place
include('inc/functions.php'); //include the functions						   
include('inc/dbconnect.php'); //connect to the database

//make any changes we need to to the member object 
//now that we know they're leaving
$_SESSION['member']->logout();

//destroy all the session information
//so they can't access the members only pages
//anymore
session_destroy();

//redirect them back to the main page
//so they can login again if they'd like
header("Location: login.php");
exit;
?>