<?php
//turn on error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

/****************
* File: index.php
* Date: 10.14.2008
* Author: jade@design1online.com
* Purpose: splash page for default site entrance
*****************/
?>
<html>
<head>
	<title>Pits of Doom | OpenSource Online PHP/MySQL Multiplayer Game Tutorial</title>
	<?php include('inc/header.php'); ?>
</head>
<body>
	<div id="main">
		<div id="mainbanner"></div>
		<div id="content">
			<h2>Pits of Doom: Lesson 8 -- Monster Maddness</h2>
			<div id="photobox">
				<img src="images/dragon.png" style="border-bottom: 1px solid #000; margin-bottom: 15px;"><br/>
				<a href="login.php">Play Game</a>
			</div>
			Welcome! Pits of Doom is an opensource multiplayer text-based game tutorial.
			You'll find each tutorial lesson and code for the game at each stage. These will walk
			you through the game's creation from the planning process to the logic behind it's code. All files
			are heavily commented for enhanced understanding. Pits of Doom is a great tool for anyone interested 
			in making or developing their own text-based online simulation game.
			<br/><br/>
			But of course, if you just want to sit back, relax and enjoy yourself you can create an account
			today and play the game.
			<div id="footer">
				<?php include('inc/footer.php'); ?>
			</div>
		</div>
	</div>
</body>
</html>