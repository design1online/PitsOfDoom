<?php
/****************
* File: viewsource.php
* Date: 3.25.2009
* Author: jade@design1online.com
* Purpose: view the source of the given file
* WARNING: This will display the source code for all of your files. In order to 
* 	prevent someone from viewing passwords or confidential information
* 	make sure you leave in the security measures inside of the showFileCode 
*	function. That way only files chmod 777 can be opened for viewing.
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
				<h2>Pits of Doom - View File Source</h2>
				<?php
					include('inc/links.php');
					
					//no file was selected
					if (!isset($_GET['file'])) { 
						echo "<br/><br/>";
						displayDirectoryFiles("../"); //pass it root location relative to this file
					} else {
						showFileCode($_GET['file']);
					}
				?>
			</div>
		</form>
	</body>
</html>