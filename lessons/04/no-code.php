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

//pick a random direction for the treasure
$treasure = rand(1, 4);

//they're starting a new game
if (isset($_POST['reset']))  {
  //if this is a new game then we have to reset the gameover value
	$_SESSION['gameover'] = false;
  
  //undo which button they pressed
	unset($_POST);
}

//respond to pressing buttons as long as the game isn't over yet
if (!isset($_SESSION['gameover']) || !$_SESSION['gameover']) {

	//they pressed the right button
	if (isset($_POST['right'])) {
		//the treasure is in this direction
		if ($treasure == 1) {
			echo "You found the treasure!";
		} else {
			echo "You fell into a pit and died!"; //the treasure wasn't in that direction
			$_SESSION['gameover'] = true; //that ends the game
		}
	}

	//they pressed the up button
	if (isset($_POST['up'])) {
		if ($treasure == 2) {
			echo "You found the treasure!";
		} else {
			echo "You fell into a pit and died!";
			$_SESSION['gameover'] = true;
		}
	}

	//they pressed the down button
	if (isset($_POST['down'])) {
		if ($treasure == 3) {
			echo "You found the treasure!";
		} else {
			echo "You fell into a pit and died!";
			$_SESSION['gameover'] = true;
		}
	}

	//they pressed the left button
	if (isset($_POST['left'])) {
		if ($treasure == 4) {
			echo "You found the treasure!";
		} else {
			echo "You fell into a pit and died!";
			$_SESSION['gameover'] = true;
		}
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
				Welcome to my little text action game. This is a really simple game.
				You're standing in a maze and you can move in four directions, up, down, left and right.
				One of the four squares has a magical treasure chest on it. Beware! You only get one guess to 
				find the treasure chest!! If you guess wrong you'll fall into the pit of doom and die...
			</p>
			<?php
				//the game isn't over yet
				if (isset($_SESSION['gameover']) &&  $_SESSION['gameover'] == false) {
				//here we can close the php tag even though we haven't closed the if statement
				//just remember you have to close the if statement eventually...
			?>
			<p align="center">
				How will you move?
			</p>
			<p align="center">
				<input type="submit" name="left" value="Left" />
				<input type="submit" name="up" value="Up" />
				<input type="submit" name="down" value="Down" />
				<input type="submit" name="right" value="Right" />
			</p>
			<p align="center">
				Try pressing a single button again and again until you find the treasure. You'll notice the treasure 
				still shows up at random places but if you fall into a pit and die you can't move anymore, even if you
				REFRESH the page. The only time you can move again is if you RESET the game.
			</p>
			<?php
				//we end the if statement, we don't want to show them a way to move again if they've died
				//by falling into a pit
				}
				else { //the game IS over, they fell into a pit by pressing a button
					echo '<p align="center">
							<strong>Game Over!</strong>
						</p>';
				}
			?>
			<p align="center">
				<input type="submit" name="reset" value="Reset Game" />
			</p>
		</form>
	</body>
</html>