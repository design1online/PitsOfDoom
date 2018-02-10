<?php
//turn on error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**************
* File: Join.php (Lesson 7)
* 
* Assignment:
*		1) Write a script that will send an email with login information to the given username or email address
*
* Techniques Taught This Lesson:
*		1) Using multiple classes
*		2) Setting up sessions based on database values
*		3) Put an objects in a sessions
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

//make sure we prevent any injections from our form data
$_POST = $_SESSION['database']->makeClean($_POST);

//they're trying to join pits of doom -- short and sweet!
if (isset($_POST['submit'])) {
	$error = newMember(
    $_POST['username'],
    $_POST['password'],
    $_POST['email'],
    $_POST['first'],
    $_POST['last'],
    $_POST['type']
  );
}
?>
<html>
  <head>
    <title>Pits of Doom</title>
  </head>
  <body>
    <form action="#" method="post">
      <p align="center">
        <strong>Join Pits of Doom Online Community</strong>
        <br/>Once you're done all you need to do is <a href="login.php">login to start playing</a>
      </p>
      <?php if (isset($error)) echo '<p align="center">' . $error . '</p>';  ?>
      <blockquote>
        <strong>Account Information</strong>
        <p>
          Username:
          <input type="text" name="username" value="<?php if (isset($_POST['username'])) { echo $_POST['username']; } ?>" />
        </p>
        <p>
          Password:
          <input type="password" name="password" value="" />
        </p>
        <p>
          Email:
          <input type="text" name="email" value="<?php if (isset($_POST['email'])) { echo $_POST['email']; } ?>" />
        </p>
        <strong>Character Information</strong>
        <br/>you can make more of these later
        <p>
          First Name:
          <input type="text" name="first" value="<?php if (isset($_POST['first'])) { echo $_POST['first']; } ?>" />
        </p>
        <p>
          Last Name:
          <input type="text" name="last" value="<?php if (isset($_POST['last'])) { echo $_POST['last']; } ?>" />
        </p>
        <p>
          Type:
          <select name="type">
          <?php
              //we're going to load all our character types from our database
              $loop = $_SESSION['database']->query("SELECT id, name FROM character_types");

              while ($row = $loop->fetch_array()) {
                echo "<option value=" . $row['id'] . ">"  . $row['name'] . "</option>";		
              }
           ?>
           </select>
        </p>
        <p align="center">
          <input type="submit" name="submit" value="Sign Me Up!" />
        </p>
      </blockquote>
    </form>
  </body>
</html>