<?php
//turn on error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**************
* File: Login.php (Lesson 7)
* 
* Assignment:
*		1) Write a login script that will check to see if the member exists in the members table
*		    of the database
*
* Techniques Taught This Lesson:
*		1) Using multiple classes
*		2) Setting up sessions based on database values
*		3) Put objects in a sessions
**************/
include('classfiles.php'); //let's condense all our class files into one place
include('functions.php'); //include the functions						   
session_start();

$host = "localhost"; //this is usually localhost or 127.0.0.1
$username = "root"; //your database username
$password = ""; //your database password
$db = "lesson7"; //your database name

// open a connection to the database
$_SESSION['database'] = new database($host, $username, $password, $db);

//clean all of our form input values before we use them in the database
$_POST = $_SESSION['database']->makeClean($_POST);

if (isset($_POST['submit'])) {
	
	//check to see if this login is in the members table
	//by retrieving their member id if their username and password matches
  //we don't need to escape the $_POST values because we've already done
  //that when we called makeClean earlier
	$id = $_SESSION['database']->single(
    "id",
    "members",
    "WHERE username='" . $_POST['username'] . "' AND  password='" . $_POST['password'] . "'"
  );
	
	//we found the member, let's create a session with this member's information
	if ($id) {

		$_SESSION['member'] = new member($id);
		
		//log them into the game
		$_SESSION['member']->login();
		
		//now let's redirect them to the map and their character
		header("Location: character.php");
		exit;
	}
	
	//we didn't find the login, we won't let them login
	$error = errorMsg("Incorrect login information");
}
?>
<html>
  <head>
    <title>Pits of Doom</title>
  </head>
  <body>
    <form action="#" method="post">
      <p align="center">
        <strong>Pits of Doom Login</strong>
        <br/>Not a member? <a href="join.php">create an account today!</a>
      </p>
      <?php if (isset($error)) { echo '<p align="center">' . $error . '</p>'; } ?>
      <blockquote>
        <p>
					Username:
					<input type="text" name="username" value="<?php if (isset($_POST['username'])) { echo $_POST['username']; } ?>" />
				</p>
        <p>
					Password:
					<input type="password" name="password" />
				</p>
        <p align="center">
          <input type="submit" name="submit" value="Login" />
          <br/><a href="lostpassword.php">Lost Password?</a>
        </p>
      </blockquote>
    </form>
  </body>
</html>