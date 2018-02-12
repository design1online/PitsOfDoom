<?php
/****************
* File: main.php
* Date: 10.14.2008
* Author: jade@design1online.com
* Purpose: draw the main game layout
*****************/
include('inc/classfiles.php'); //let's condense all our class files into one place
include('inc/functions.php'); //include the functions						   
include('inc/dbconnect.php'); //connect to the database

if (!isset($_SESSION['member'])) { //they haven't logged in yet, so they can't see this page
	header("Location: login.php"); //redirect them to the login page
	exit; //use this so the rest of the page won't load
}

if (isset($_POST['right'])) { //they pressed the right button
	$error = $_SESSION['member']->curcharacter->move("right");
}

if (isset($_POST['back'])) { //they pressed the down button
	$error = $_SESSION['member']->curcharacter->move("back");
}
	
if (isset($_POST['forward'])) { //they pressed the up button
	$error = $_SESSION['member']->curcharacter->move("forward");
}

if (isset($_POST['left'])) { //they pressed the left button
	$error = $_SESSION['member']->curcharacter->move("left");
}
	
// check to see if they changed maps
if ($_SESSION['member']->map->level != $_SESSION['member']->curcharacter->z
	|| $_SESSION['member']->map->id != $_SESSION['member']->curcharacter->mapid
) {
	$_SESSION['member']->map = new mapobj($_SESSION['member']->curcharacter->mapid, $_SESSION['member']->curcharacter->z);
}
?>
<html>
	<head>
		<title><?php echo $_sitetitle; ?></title>
		<?php include('inc/header.php'); ?>
	</head>
	<body>
		<form action="#" method="post">
		<div id="main">
			<div id="mainbanner"></div>
				<div id="content">
					<h2>Pits of Doom - <?php echo mapName($_SESSION['member']->curcharacter->mapid); ?></h2> 
					<table cellpadding="2" cellspacing="0" width="100%" border="0">
						<tr>
							<td>
							<?php
							//now we want to always display the map
							displayMap($_SESSION['member']);
							?>
							</td>
							<td valign="top" width="43%">
								<p align="center">
									<strong>Welcome <?php echo $_SESSION['member']->username; ?>.</strong>
								</p>
								<?php 
									if (isset($error)) {
										echo '<p align="center">' . $error . '</p>';
									}

									//check to see if they need to find a monster
									include('inc/monsterstats.php');

									//character up/down, right/left buttons
									include('inc/navigation.php'); 

									//always show the characters stats
									include('inc/characterstats.php'); 
								?>
							</td>
						</tr>
						<tr>
							<td align="center">
								<?php include('inc/maplinks.php'); ?>
							</td>
							<td align="center">
								<?php include('inc/characterlinks.php'); ?>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<?php include('inc/chattext.php'); ?>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<table cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td>
											<input type="text" name="chatmess" value="" size="101" />
										</td>
										<td>
											<input type="submit" name="postmessage" value="Post" />
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div id="footer">
									<?php include('inc/footer.php'); ?>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</form>
	</body>
</html>