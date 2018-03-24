<?php
/****************
* File: monsters.php
* Date: 3.9.2009
* Author: jade@design1online.com
* Purpose: generate monsters for the game
*****************/
include('../oop/characterobj.php'); //need this because monsters extend character
include('../oop/monsterobj.php');
include('../oop/mysqlobj.php');
include('../inc/functions.php'); //include the functions						   
include('../inc/dbconnect.php'); //connect to the database

if ($_POST['number'])
	$error = generateMonsters($_POST['number']); //function found in oop/monsterobj.php
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
		<h2>Pits of Doom - Generate Monsters</h2>
		<?php include('inc/links.php'); ?>
		<blockquote>
			<?php if ($error) echo $error; ?>
			Use this utility to generate monsters for your game. They will be placed in random locations
			across all of your maps and at all map depth levels. None of them will be placed on wall coordinates
			but any other spot is fair game.
			<br/><br/>
			Number To Generate: <input type="text" name="number" value="<?php echo $_POST['number']; ?>" /><br/>
			<br/>
			<center>
				<input type="submit" name="submit" value="Make Um' Mean" />
			</center>
		</blockquote>
	</div>
</form>
</body>
</html>