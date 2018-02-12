<?php
/****************
* File: lostpassword.php
* Date: 10.14.2008
* Author: jade@design1online.com
* Purpose: send an email with login information to the member
*****************/
include('inc/classfiles.php'); //let's condense all our class files into one place
include('inc/functions.php'); //include the functions						   
include('inc/dbconnect.php'); //connect to the database

if (isset($_POST['submit'])) {
	
	//check to see if this login is in the members table
	//by retrieving their member id if their username or email matches
	$id = $_SESSION['database']->single(
		"id",
		"members",
		"WHERE username='" . $_POST['username'] . "' OR email='" . $_POST['email'] . "'"
	);
	
	//we found the member, let's create a session with this member's information
	if ($id) {
		$_SESSION['member'] = new member($id);
		
		//send their lost password information
		$error = $_SESSION['member']->sendPassword();
	} else { //we didn't find them anywhere
		$error = errorMsg("Username or email not found");
	}
}
?>
<html>
	<head>
		<title><?php echo $_sitetitle; ?></title>
		<?php include('inc/header.php'); ?>
	<body>
	<form action="#" method="post">
		<div id="main">
			<div id="mainbanner"></div>
			<div id="content">
				<h2>Pits of Doom - Lost Password</h2>
				Not a member? <a href="join.php">Create an account </a> or <a href="login.php">login to start playing</a>.
				<?php if (isset($error)) { echo '<p align="center">' . $error . '</p>'; } ?>
				<p align="center">
					Username:
					<input
						type="text"
						name="username"
						value="<?php if (isset($_POST['username'])) { echo $_POST['username']; } ?>"
					/>
					- OR - 
					Email:
					<input
						type="text"
						name="email"
						value="<?php if (isset($_POST['email'])) { echo $_POST['email']; } ?>"
					/>
				</p>
				<p align="center">
					<input type="submit" name="submit" value="Send Login Information" />
				</p>
				<div id="footer">
					<?php include('inc/footer.php'); ?>
				</div>
			</div>
		</div>
	</form>
	</body>
</html>
