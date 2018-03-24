<?php
/****************
* File: index.php
* Date: 10.14.2008
* Author: jade@design1online.com
* Purpose: splash page for default site entrance
*****************/
?>
<html>
<head>
	<title></title>
	<?php include('inc/header.php'); ?>
</head>
<body>
	<div id="main">
		<div id="mainbanner"></div>
		<div id="content">
		<h2><?php echo $_lessontitle; ?></h2>
		<div id="photobox">
			<img src="images/dragon.png" style="border-bottom: 1px solid #000; margin-bottom: 15px;"><br/>
			<a href="login.php">Play Game</a>
			| <a href="http://design1online.com/examples/lesson8/download/Pits%20Of%20Doom%20Lesson%209.zip">Download</a>
			| <a href="http://design1online.com/contact.php">Contact</a>
		</div>
			<?php echo $_description; ?>
			<br/><br/>
			<h2>Pits of Doom: Lesson 9 - Bring the Fight</h2>
			<p>
				Welcome! Pits of Doom is an opensource multiplayer text-based game tutorial. You'll find each tutorial lesson and code for 
				the game at each stage. These will walk you through the game's creation from the planning process to the logic behind it's 
				code. All files are heavily commented for enhanced understanding. Pits of Doom is a great tool for anyone interested in making 
				or developing their own text-based online simulation game.
			</p> 
			<p>
				But of course, if you just want to sit back, relax and enjoy yourself you can create an account today and play the game.
			</p>
			<div id="footer">
				<?php include('inc/footer.php'); ?>
			</div>
		</div>
	</div>
</body>
</html>