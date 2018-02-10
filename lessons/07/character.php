<?php
//turn on error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**************
* File: Character.php (Lesson 7)
* 
* Assignment:
*		1) Write your own custom functions to load maps from the map editor into the game
*		2) Add functionailty so that when you land on a pit you "fall" down to a lower map level.
*		3) Add functionailty so that when you are on a ladder you can move up or down a level.
*		4) Let the player keep playing after they've found the treasure, but keep track of how much
*			treasure they've collected to far.
*		5) Once they've found the treasure change that spot on the map to an empty space
*
* Techniques Taught This Lesson:
*		1) Create a MySQL database class
*		2) covert this & the map editor to pull map info from the database
**************/
include('classfiles.php'); //let's condense all our class files into one place
include('functions.php'); //include the functions
session_start();

if (!$_SESSION['member']) { //they haven't logged in yet, so they can't see this page
	header("Location: login.php"); //redirect them to the login page
	exit; //use this so the rest of the page won't load
}

//reconnect to the database
$_SESSION['database']->connect();

//sanitize all form inputs
$_POST = $_SESSION['database']->makeClean($_POST);

if (isset($_POST['right'])) { //they pressed the right button
	$_SESSION['member']->curcharacter->move("right");
}

if (isset($_POST['back'])) { //they pressed the down button
	$_SESSION['member']->curcharacter->move("back");
}
	
if (isset($_POST['forward'])) { //they pressed the up button
	$_SESSION['member']->curcharacter->move("forward");
}

if (isset($_POST['left'])) { //they pressed the left button
	$_SESSION['member']->curcharacter->move("left");
}
?>
<html>
  <head>
    <title>Pits of Doom</title>
  </head>
  <body>
    <form action="#" method="post">
      <?php
      // display the game map
      if (isset($_GET['showmap'])) {
        displayMap(
            $_SESSION['member']->curcharacter->mapid,
            $_SESSION['member']->curcharacter->x, $_SESSION['member']->curcharacter->y, 
            $_SESSION['member']->curcharacter->z, 
            width($_SESSION['member']->curcharacter->mapid, $_SESSION['member']->curcharacter->z), 
            height($_SESSION['member']->curcharacter->mapid, $_SESSION['member']->curcharacter->z)
        );
      }
      ?>
      <p align="center">
        <table cellpadding="2" cellspacing="2">
          <tr>
            <td colspan="2" align="center">
              <strong>Welcome <?php echo $_SESSION['member']->username; ?>!</strong>
              <br/>Active Character: 
              <?php 
                echo $_SESSION['member']->curcharacter->firstname . ' ' . 
                   $_SESSION['member']->curcharacter->lastname; 
              ?>
              <br/>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">
              <input type="submit" name="forward" value="/\" />
            </td>
          </tr>
          <tr>
            <td align="center"><input type="submit" name="left" value="<-" /></td>
            <td align="center"><input type="submit" name="right" value="->" /></tr>
          </tr>
          <tr>
            <td colspan="2" align="center">
              <input type="submit" name="back" value="\/" />
            </td>
          </tr>
        </table>
      <?php
        //now we can make it so you can turn the map on or off
        if (!isset($_GET['showmap'])) { //map isn't being shown
          echo '<a href="?showmap=yes">Cheat: show the map</a>';
        } else { //map is already being shown
          echo '<a href="character.php">Play Fair: hide the map</a>';
        }

        echo ' | <a href="logout.php">Logout</a>';
      ?>
      </p>
    </form>
  </body>
</html>