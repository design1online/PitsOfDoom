<?php
session_start();

/**************
* File: Character.php (Lesson 4)
* 
* Assignment:
*		1) Fill in the empty logic in this new array-based maze game so the player now has to navigate a much larger maze before they
*		    can find the treasure.
*		2) If you get everything working, try making a larger maze with more holes by adding to the array
*         
* Tutorial Goals:
*		0) Intoduction to forms -- DONE
*		1) Make the treasure be located in a random direction -- DONE
*		2) Make the player walk around a larger area before they find the treasure (arrays)
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
*		1) arrays, multi-dimensional arrays
*		2) array keys and values
*		3) using multiple session variables
*		4) accessing session data using variables
**************/

/************
* GAME MAP/MAZE
*	  0	1	2	3	4	5	6
* 0	W	W	W	W	W	W	W
* 1	W	S	E	E	W	E	W
* 2	W	W	X	E	E	E	W
* 3	W	E	E	E	T	X	W
* 4	W	W	W	W	W	W	W
************/

/************
* MAP/MAZE KEY
* S is our starting position
* X is a hole
* W is a wall
* E is an open path
* T is the treasure
************/

/*************
* MAP/MAZE ARRAY
*************/
$maze = array();

$maze[0][0] = "W"; //this is a wall
$maze[0][1] = "W";
$maze[0][2] = "W";
$maze[0][3] = "W";
$maze[0][4] = "W";
$maze[0][5] = "W";
$maze[0][6] = "W";

$maze[1][0] = "W";
$maze[1][1] = "S"; //this is where we're starting from
$maze[1][2] = "E";
$maze[1][3] = "E";
$maze[1][4] = "W";
$maze[1][5] = "E";
$maze[1][6] = "W";

$maze[2][0] = "W";
$maze[2][1] = "W";
$maze[2][2] = "X"; //here's a pit
$maze[2][3] = "E";
$maze[2][4] = "E";
$maze[2][5] = "E";
$maze[2][6] = "W";

$maze[3][0] = "W";
$maze[3][1] = "E";
$maze[3][2] = "E";
$maze[3][3] = "E";
$maze[3][4] = "T"; //here is our treasure
$maze[3][5] = "X"; //another pit
$maze[3][6] = "W";

$maze[4][0] = "W";
$maze[4][1] = "W";
$maze[4][2] = "W";
$maze[4][3] = "W";
$maze[4][4] = "W";
$maze[4][5] = "W";
$maze[4][6] = "W";

//haven't played this game yet
if (!isset($_SESSION['x'])) {
  //start at the S position in the maze
	$_SESSION['x'] = 1;
	$_SESSION['y'] = 1;
}

//they're starting a new game
if (isset($_POST['reset'])) {
  //if this is a new game then we have to reset the gameover
	$_SESSION['gameover'] = false;
	
	//reset them to the starting position
	$_SESSION['x'] = 1;
	$_SESSION['y'] = 1;
	
	//undo which button they pressed
	unset($_POST); 
}

//they pressed the right button
if (isset($_POST['right'])) {
	//we add 1 to the player's y value to move them over right
	//but we have to check what's in that spot before we update their location
  
  //the treasure is in this direction
	if ($maze[$_SESSION['x']][$_SESSION['y']+1] == "T") {
		echo '<p align="center">You found the treasure!</p>';
		$_SESSION['gameover'] = true;
    $_SESSION['y'] = $_SESSION['y'] + 1;
	} else {
    //hit a wall
		if ($maze[$_SESSION['x']][$_SESSION['y']+1] == "W") {
			echo '<p align="center">You hit a wall!</p>';
		} else if ($maze[$_SESSION['x']][$_SESSION['y']+1] == "S") { //starting position
			echo '<p align="center">You\'re back where you started!</p>';
			$_SESSION['y'] = $_SESSION['y'] + 1; //move to this location
		} else if ($maze[$_SESSION['x']][$_SESSION['y']+1] == "E") { //this is an empty space
			echo '<p align="center">You moved right one space.</p>';
			$_SESSION['y'] = $_SESSION['y'] + 1; //move them to this new location
		} else if ($maze[$_SESSION['x']][$_SESSION['y']+1] == "X") { //this is a pit
			echo '<p align="center">You fell into a pit!</p>';
      $_SESSION['y'] = $_SESSION['y'] + 1;
			$_SESSION['gameover'] = true;
		}
	}
}

//they pressed the up button
if (isset($_POST['up'])) {
	//we add 1 to the player's x value to move them up
	if ($maze[$_SESSION['x']+1][$_SESSION['y']] == "T") { //the treasure is in this direction
		echo '<p align="center">You found the treasure!</p>';
		$_SESSION['gameover'] = true;
    $_SESSION['x'] = $_SESSION['x'] + 1;
	} else {
		if ($maze[$_SESSION['x']-1][$_SESSION['y']] == "W") { //hit a wall
			echo '<p align="center">You hit a wall!</p>';
		} else if ($maze[$_SESSION['x']-1][$_SESSION['y']] == "S") { //starting position
			echo '<p align="center">You\'re back where you started!</p>';
			$_SESSION['x'] = $_SESSION['x'] - 1;
		} else if ($maze[$_SESSION['x']-1][$_SESSION['y']] == "E") { //this is an empty space
			echo '<p align="center">You moved forward one space.</p>';
			$_SESSION['x'] = $_SESSION['x'] - 1;
		} else if ($maze[$_SESSION['x']-1][$_SESSION['y']] == "X") { //pit
			echo '<p align="center">You fell into a pit!</p>';
      $_SESSION['x'] = $_SESSION['x'] - 1;
			$_SESSION['gameover'] = true;
		}
	}
}

//they pressed the down button
if (isset($_POST['down'])) {
	//we subtract 1 to the player's x value to move them up
	if ($maze[$_SESSION['x']+1][$_SESSION['y']] == "T") { //the treasure is in this direction
		echo '<p align="center">You found the treasure!</p>';
		$_SESSION['gameover'] = true;
    $_SESSION['x'] = $_SESSION['x'] + 1;
	} else {
		if ($maze[$_SESSION['x']+1][$_SESSION['y']] == "W") { //hit a wall
			echo '<p align="center">You hit a wall!</p>';
		} else if ($maze[$_SESSION['x']+1][$_SESSION['y']] == "S") { //starting position
			echo '<p align="center">You\'re back where you started!</p>';
			$_SESSION['x'] = $_SESSION['x'] + 1;
		} else if ($maze[$_SESSION['x']+1][$_SESSION['y']] == "E") { //this is an empty space
			echo '<p align="center">You moved back one space.</p>';
			$_SESSION['x'] = $_SESSION['x'] + 1;
		} else if ($maze[$_SESSION['x']+1][$_SESSION['y']] == "X") { //fell into a hole!!
			echo '<p align="center">You fell into a pit!</p>';
      $_SESSION['x'] = $_SESSION['x'] + 1;
			$_SESSION['gameover'] = true;
		}
	}
}

if (isset($_POST['left'])) { //they pressed the left button
	//we subtract 1 from the player's y value to move them over left
	if ($maze[$_SESSION['x']][$_SESSION['y']-1] == "T") { //the treasure is in this direction
		echo '<p align="center">You found the treasure!</p>';
		$_SESSION['gameover'] = true;
    $_SESSION['y'] = $_SESSION['y'] - 1;
	} else {
		if ($maze[$_SESSION['x']][$_SESSION['y']-1] == "W") { //hit a wall
			echo '<p align="center">You hit a wall!</p>';
		} else if ($maze[$_SESSION['x']][$_SESSION['y']-1] == "S") { //starting position
			echo '<p align="center">You\'re back where you started!</p>';
			$_SESSION['y'] = $_SESSION['y'] - 1;
		} else if ($maze[$_SESSION['x']][$_SESSION['y']-1] == "E") { //this is an empty space
			echo '<p align="center">You moved left one space.</p>';
			//empty space, move them to this new location
			$_SESSION['y'] = $_SESSION['y'] - 1;
		} else if ($maze[$_SESSION['x']][$_SESSION['y']-1] == "X") { //fell into a hole!!
			echo '<p align="center">You fell into a pit!</p>';
      $_SESSION['y'] = $_SESSION['y'] - 1;
			$_SESSION['gameover'] = true;
		}
	}
}

/*******************
* Purpose: Draw the current map to the screen with a red C marking
*          the character's current position. The C turns green if
*          they've found the treasure
* Precondition: This must have the characters x and y values, the
*               map they're using, and the width/height of that map
* Postcondition: Everything is drawn directly on the screen with the
*               character's coordinates at the bottom
******************/
function drawMap($x, $y, $map, $mapwidth, $mapheight)
{

  //we want a nice header at the top
  echo '<p align="center"><strong>Game Map</strong><br>
       <table cellpadding="2" cellspacing="2">'; 

  //now we want to add an extra row at the top that just shows the coordinate numbers
  //across the top of the map.

  echo "<tr><td></td>"; //we start a row, and add an extra blank column to offset the 
                        //coordinate numbers added over the map (so 0 starts over the first W in the map)


  for ($i = 0; $i <= $mapwidth; $i++) { //now we want to draw numbers for the width of the map
    echo "<td>$i</td>"; //this shows the numbers in a column
  }

  echo "</tr>"; //that's the end of this row, we now have all the numbers across the top of the map
    
   //now it's time to start drawing the map

   for ($i = 0; $i <= $mapheight; $i++) { //we want to draw the entire height
     echo "<tr>"; //each row in the map starts off with a number as well
     echo "<td>$i</td>"; //so we can tell which coordinate it's at

      //now we want to draw what's in that part of the map for the entire width
      for ($j = 0; $j <= $mapwidth; $j++) {
        if ($x == $i && $y == $j && $map[$i][$j] != "T") { //if this is not treasure
             echo '<td>
              <strong>
                <font color="red">C</font>
              </strong>
             </td>'; 
        } else if ($x == $i && $y == $j && $map[$i][$j] == "T") { //this is the treasure
          echo '<td>
            <strong>
              <font color="green">C</font>
            </strong>
           </td>';
        } else {
          //this is just an empty map space
          echo "<td>" . $map[$i][$j] . "</td>";
        }
     }
     echo "</tr>"; //end the row
   }

   //now our table is done, we can end it, and under that
   //we'll show the coordinates of the character so we can
   //tell what spot they're inside of the map numerically
   echo "</table><br/>"; 
   echo "Your coordinates are: ($x, $y)";
   echo "</center>";
}

//we're only going to show the map
//if they decide they want to cheat...
if (isset($_GET['showmap'])) {
  //call our custom draw map function
  //make sure we pass it all the parameters we need
  drawMap($_SESSION['x'], $_SESSION['y'], $maze, 6, 4);
}
?>
<html>
  <head>
    <title>Pits of Doom</title>
  </head>
  <body>
    <form action="#" method="post">
      <p align="center">
         Welcome to my little text action game. This is a really simple game.
        You're standing in a maze and you can move in four directions, up, down, left and right.
        One of the four squares has a magical treasure chest on it. Beware! You only get one guess 
        to find the treasure chest!! If you guess wrong you'll fall into the pit of doom and die...
      </p>
      <?php
        //the game isn't over yet
        if (!isset($_SESSION['gameover']) || $_SESSION['gameover'] == false) {
      ?>
      <p align="center">
        How will you move?
      </p>
      <p align="center">
        <input type="submit" name="left" value="<-" />
        <input type="submit" name="up" value="/\" />
        <input type="submit" name="down" value="\/" />
        <input type="submit" name="right" value="->" />
        <br/><a href="?showmap=yes">or you can cheat and display the map...</a>
      </p>
      <?php
        } else { //the game is over, they fell into a pit or found the treasure
          echo '<p align="center">
            <strong>Game Over!</strong>
           </p>';
        }
      ?>
      <p align="center">
        <input type="submit" name="reset" value="Reset Game?" />
      </p>
    </form>
  </body>
</html>