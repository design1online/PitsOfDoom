<?php
//turn on error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**************
* File: Lostpassword.php (Lesson 7)
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

if (isset($_POST['submit'])) {
  
    //check to see if this login is in the members table
    //by retrieving their member id if their username or email matches
    $id = $database->single(
      "id",
      "members",
      "WHERE username='" . $_POST['username'] . "' OR email='" . $_POST['email'] . "'"
    );

    //we found the member, let's create a session with this member's information
    if ($id)  {

      //if you're using PHP 5 you need to remove the ampersand & below
      $_SESSION['member'] = & new member($id);

      //send their lost password information
      $error = $_SESSION['member']->sendPassword();
    } else { //we didn't find them anywhere
      $error = errorMsg("Username or email not found");
    }
}
?>
<html>
  <head>
  <title>Pits of Doom</title>
  </head>
  <body>
    <form action="#" method="post">
      <p align="center">
        <strong>Pits of Doom Lost Password</strong>
        <br/>Not a member? <a href="join.php">create an account </a> or <a href="login.php">login to start playing</a>
      </p>
      <?php if (isset($error)) echo '<p align="center">' . $error . '</p>'; ?>
      <p align="center">
          Username: <input type="text" name="username" value="<?php echo $_POST['username']; ?>" />
          - OR - 
          Email: <input type="text" name="email" value="<?php echo $_POST['email']; ?>" />
      </p>
      <p align="center">
          <input type="submit" name="submit" value="Send Login Information" />
      </p>
    </form>
  </body>
</html>