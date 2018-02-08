<?php
//lets turn on some error handling
error_reporting(E_ALL);
ini_set("display_errors", 1);

//now start our session
session_start();

//include any files we need
include('mysqlobj.php');
include('functions.php');

/**************
* File: Character.php (Lesson 6)
* 
* Assignment:
*		1) Write your own custom functions to load maps from the map editor into the game
*		2) Add functionailty so that when you land on a pit you "fall" down to a lower map level.
*		3) Add functionailty so that when you are on a ladder you can move up or down a level.
*		4) Let the player keep playing after they've found the treasure, but keep track of how much
*			treasure they've collected to far.
*		5) Once they've found the treasure change that spot on the map to an empty space
*         
* Tutorial Goals:
*		0) Intoduction to forms -- DONE
*		1) Make the treasure be located in a random direction -- DONE
*		2) Make the player walk around a larger area before they find the treasure (arrays) -- DONE
*			2.1) Made a map file editor -- DONE
*			2.2) Load map files from a txt file -- DONE
*			2.3) Let the player move up and down map levels using pits and ladders -- DONE
*			2.4) Make the map that shows up on the screen nicer -- DONE
*			2.5) Compact our code so it's easier to read -- DONE
*			2.6) Convert everything over to a database
*		3) Let the player pick up items
*		4) Let the player use items
*		5) Give the player health
*		6) Let the player fight monsters
*		7) Put in obstacles that block the player's path
*		8) Put in more pits the player can fall into and die
*		9) Put keys into the maze
*		10) Put locked doors that can only be opened if you have a key
*		11) Store the player's information in a database (mysql)
*		12) Load the player's information from a database
*		13) Make the player login before they can start playing (mysql & php combined)
*		14) Send the player an email if they forget their password
*		15) Allow other players to create an account on the game
*		16) Let the player talk to other members with messages
*		17) Let the player post on a message board
*
* Techniques Taught This Lesson:
*		1) Including more files
*		1) exporting a file to download
*		2) loading a file's contents into an array
*		3) switching map contents based on where the character is in the map
*		4) compacting your code so it's more managable as things get more complicated
**************/

//setup database information
$host = "localhost"; //usually localhost or 127.0.0.1
$username = "root"; //database username
$password = ""; //database password
$usedatabase = "lesson6"; //name of your database

//we have to start our database connection
$database = new database($host, $username, $password, $usedatabase);

//if there is no map currently loaded
if (!$_SESSION['mapid']) {
	startNewGame();
}

//they're starting a new game
if (isset($_POST['reset'])) {
	resetGame();
}

//they pressed the right button
if (isset($_POST['right'])) {
	moveCharacter("right");
}

//they pressed the down button
if (isset($_POST['back'])) {
	moveCharacter("back");
}
	
//they pressed the up button
if (isset($_POST['forward'])) {
	moveCharacter("forward");
}

//they pressed the left button
if (isset($_POST['left'])) {
	moveCharacter("left");
}

?>
<html>
  <head>
    <title>Pits of Doom</title>
  </head>
  <body>
    <form action="#" method="post">
      <?php
      if (isset($_GET['showmap'])) {
        displayMap(
          $_SESSION['mapid'],
          $_SESSION['x'],
          $_SESSION['y'],
          $_SESSION['level'],
          width($_SESSION['mapid'], $_SESSION['level']),
          height($_SESSION['mapid'], $_SESSION['level'])
        );
      }
      ?>
      <p align="center">
      <?php
         //the game isn't over yet
        if (!isset($_SESSION['gameover']) || $_SESSION['gameover'] == false) {
      ?>
        <table cellpadding="2" cellspacing="2">
        <tr>
          <td colspan="2" align="center">
            Welcome to Pits of Doom.
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
        } else { //the game IS over, they fell into a pit or found the treasure
          echo '<b>Game Over!</b>';
        }
      ?>
        <br/><br/>
        <input type="submit" name="reset" value="Reset Game" /><br/>
      <?php
        //now we can make it so you can turn the map on or off
        if (!isset($_GET['showmap'])) { //map isn't being shown
          echo '<a href="?showmap=yes">Cheat: show the map</a>';
        } else { //map is already being shown
          echo '<a href="character.php">Play Fair: hide the map</a>';
        }
      ?>
      </p>
    </form>
  </body>
</html>