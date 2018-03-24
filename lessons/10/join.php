<?php
/****************
* File: join.php
* Date: 10.14.2008
* Author: jade@design1online.com
* Purpose: join the game for the first time
*****************/
include('inc/classfiles.php'); //let's condense all our class files into one place
include('inc/functions.php'); //include the functions						   
include('inc/dbconnect.php'); //connect to the database

if ($_POST['submit']) //short and super sweet!
	$error = newMember($_POST['username'], $_POST['password'], $_POST['email'], $_POST['first'], $_POST['last'], $_POST['type']);
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
		<h2>Pits of Doom - Create An Account</h2>
		<?php if ($error) echo $error;  ?>
		<blockquote>
			<b>Account Information</b>
			<p>Username: <input type="text" name="username" value="<?php echo $_POST['username']; ?>" /> </p>
			<p>Password: <input type="password" name="password" value="" /></p>
			<p>Email: <input type="text" name="email" value="<?php echo $_POST['email']; ?>" /></p>
			<b>Character Information</b>
			<p>First Name: <input type="text" name="first" value="<?php echo $_POST['first']; ?>" /> </p>
			<p>Last Name: <input type="text" name="last" value="<?php echo $_POST['last']; ?>" /></p>
			<p>Type: <select name="type">
				<?php
				//we're going to load all our character types from our database
				$loop = $database->query("SELECT id, name FROM character_types");
								
				while ($row = mysql_fetch_array($loop))
					echo "<option value=" . $row['id'] . ">"  . $row['name'] . "</option>";			
				?>
				</select>
			</p>
			<p align="center">
					<input type="submit" name="submit" value="Sign Me Up!" />
			</p>
		</blockquote>
		<div id="footer"><?php include('inc/footer.php'); ?></div>
	</div>
</form>
</body>
</html>
