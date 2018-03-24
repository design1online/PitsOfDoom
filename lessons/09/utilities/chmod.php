<?php
/****************
* File: chmod.php
* Date: 3.25.2009
* Author: jade@design1online.com
* Purpose: chmod directories 0755 for viewsource.php
*****************/
include('../oop/mysqlobj.php'); //database object
include('../inc/functions.php'); //include the functions						   
include('../inc/dbconnect.php'); //connect to the database
include('inc/source.php'); //include functions for this file
?>
<html>
<head>
<title><?php echo $_sitetitle; ?></title>
<link rel="stylesheet" type="text/css" href="../css/default.css" />
</head>
<body>
<form action="#" method="post">
	<div id="main">
		<div id="mainbanner"></div>
		<div id="content">
		<h2>Pits of Doom - CHMOD 0755</h2>
		<?php include('inc/links.php'); ?>
		<?php
			chmod0755("../");
			
			echo successMsg("Done. How easy was that");
		?>
	</div>
</form>
</body>
</html>