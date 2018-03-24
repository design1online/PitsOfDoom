<?php
/****************
* File: online.php
* Date: 6.23.2011
* Author: design1online.com, LLC
* Purpose: logout inactive members and login active members
* Note: must already be connected to the database
*****************/

$INACTIVITY_PERIOD = 20; //minutes

mysql_query("UPDATE members SET online=0 WHERE online=1 AND 
		MINUTE(TIMEDIFF(lastlogin, NOW())) >= $INACTIVITY_PERIOD")
	or die ('cannot logout members who have been online more than $INACTIVITY_PERIOD minutes');

//if this member is currently playing online update their lastlogin time
if ($_SESSION['member']->id)
	mysql_query("UPDATE members SET lastlogin=NOW(), online=1 WHERE id='{$_SESSION['member']->id}'")
		or die ('cannot update this members lastlogin time');