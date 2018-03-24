<?php
/****************
* File: chattext.php
* Date: 6.17.2011
* Author: design1online.com, LLC
* Purpose: display chat text messages
*****************/
require_once('../oop/mysqlobj.php');
require_once('functions.php');
require_once('dbconnect.php');

$MAX_MESSAGES = 25;
$REFRESH_SECONDS = 5;

echo "
<html>
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html\" />
<link rel=\"stylesheet\" type=\"text/css\" href=\"../css/default.css\" />
<meta http-equiv=\"refresh\" content=\"$REFRESH_SECONDS\">
</head>
<body class=\"chat\">";

$loop = mysql_query("
	SELECT 
		C.mid, 
		M.username,
		C.message,
		C.date
	FROM
		chatroom C
	INNER JOIN members M ON M.id = C.mid
	ORDER BY
		C.id DESC
	LIMIT $MAX_MESSAGES")
	or die ('Cannot load chat room messages ' . mysql_error());

while ($row = mysql_fetch_assoc($loop))
	echo "<span class=\"datetime\">" . date('g:ia', strtotime($row['date'])) . "</span> " . htmlentities($row['username']) . ": " . htmlentities($row['message']) . "<br/>";
	
echo "</body>
	</html>";