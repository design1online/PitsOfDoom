<?php
/**************
* File: Character.php (Lesson 2)
* 
* Programing Objectives: 
*		1) Pick a direction for the treasure chest to be in
*		2) Tell the user if they move in the direction that has the treasure chest
*		3) If the user picks a disrection without the treasure chest, they loose
*		4) ONLY SHOW the welcome message if they haven't picked a direction yet
*         
* Tutorial Goals:
*		0) Introduction to forms
*		1) Make the treasure be located in a random direction
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
*		1) Form $_POST and $_GET
*		2) If-else statements
*		3) Printing to the screen
**************/

//Now we check to see which button they pressed if any
if (isset($_POST['right'])) {
   echo "You found the treasure!!<br/>";
}

//all other directions have a pit in them
if (isset($_POST['left'])) {
   echo "You fell into a pit and died!";
}

if (isset($_POST['forward'])) {
   echo "You fell into a pit and died!";
}

if (isset($_POST['back'])) {
   echo "You fell into a pit and died!";
}
?>
  <html>

  <head>
    <title>Pits of Doom</title>
  </head>

  <body>

<?php
//we only want to show this message if they haven't pressed any of these buttons
if (!isset($_POST['left']) && !isset($_POST['forward']) && !isset($_POST['back']) && !isset($_POST['right'])) {
?>
      <form action="#" method="post">
        <p align="center">Welcome to the start of my online game tutorial! If this is your very first online game it's best to start off really simple and build up as we go along. Think you can make this yourself? Every tutorial has two files, one version where you can
          try to program the game yourself, and another version with a working copy of the code. If you already have programming experience then writing the game on your own should be a snap. If not, take a while to go through the files that already have
          the code provided. Imagine you're standing in a maze and you can move in four directions, left, right, forward and back. One of the four squares has a magical treasure chest on it. Beware! You only get one guess to find the treasure chest!! If you guess wrong you'll
          fall into a pit of doom and die.
        </p>
        <p align="center">
          How will you move?
        </p>
        <p align="center">
          <input type="submit" name="left" value="Left" />
          <input type="submit" name="forward" value="Forward" />
          <input type="submit" name="back" value="Back" />
          <input type="submit" name="right" value="Right" />
        </p>
      </form>
<?php
} //this is the end of our if statement
  //notice how we don't have to have an else statement
  //following it. What happens once you've pressed a button?
?>
  </body>
</html>