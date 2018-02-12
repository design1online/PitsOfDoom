<?php
/****************
* File: login.php
* Date: 10.14.2008
* Author: jade@design1online.com
* Purpose: allow authorized members into the game
*****************/
include('inc/classfiles.php'); //let's condense all our class files into one place
include('inc/functions.php'); //include the functions						   
include('inc/dbconnect.php'); //connect to the database

if (isset($_POST['submit'])) {
	
	//check to see if this login is in the members table
	//by retrieving their member id if their username and password matches
	$id = $_SESSION['database']->single(
		"id",
		"members",
		"WHERE username='" . $_POST['username'] . "' AND password='" . $_POST['password'] . "'"
	);
	
	//we found the member, let's create a session with this member's information
	if ($id) {
		$_SESSION['member'] = new member($id);
		
		//log them into the game
		$_SESSION['member']->login();
		
		//now let's redirect them to the map and their character
		header("Location: main.php");
		exit;
	}
	
	//we didn't find the login, we won't let them login
	$error = errorMsg("Incorrect login information");
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
					<h2>Pits of Doom - Login</h2>
					<div id="loginbox">
						<p>
							Username:
							<input
								type="text"
								name="username"
								value="<?php if (isset($_POST['username'])) { echo $_POST['username']; } ?>"
							/>
						</p>
						<p>
							Password:
							<input type="password" name="password" />
						</p>
						<p align="center">
							<input type="submit" name="submit" value="Start Playing" />
							<br/><a href="lostpassword.php">Lost Password?</a>
						</p>
					</div>
					<?php if (isset($error)) { echo $error; } ?>
					Welcome. Enter a world where exploring -- and surviving -- is half the fun.
					Create your own characters, battle other members and game monsters, and chat with your friends
					at the same time. Be careful! Even a cat only has nine lives.
					<br/><br/>
					<p align="center">
						Not a member? <a href="join.php">Create an account today</a>!
					</p>
					<div id="footer">
						<?php include('inc/footer.php'); ?>
					</div>				
				</div>
			</div>
		</form>
	</body>
</html>
