<?php
/**************
* File: functions.php
* Purpose: we'll put all of our functions in here
*	so our page looks cleaner and easier to read
**************/

/**************
* The width of the array map
* MUST HAVE ALREADY CONNECTED TO DATABASE!!
**************/
function height($mapid, $level)
{
	global $database;

	//select the largest x coordinate
	$result = $database->select(
    "x",
    "mapdata",
    "WHERE mapid=$mapid and z=$level ORDER BY x DESC LIMIT 1"
  );
	
	return $result['x'];
}

/**************
* The height of the array map
* MUST HAVE ALREADY CONNECTED TO DATABASE!!
**************/
function width($mapid, $level)
{
	global $database;

	//select the largest x coordinate
	$result = $database->select(
    "y",
    "mapdata",
    "WHERE mapid=$mapid and z=$level ORDER BY y DESC LIMIT 1"
  );
	
	return $result['y'];
}

/**************
* Display the map file on the screen
* 	and the character's position on the map
* MUST HAVE ALREADY CONNECTED TO DATABASE!!
**************/
function displayMap($mapid, $x, $y, $level, $width, $height)
{
global $database;

	echo '<p align="center">
		  <table cellpadding="2" cellspacing="2">
		  <tr><td></td>';
		  
	for ($i = 0; $i < $width; $i++) {
		echo "<td>$i</td>";
	}
	
	for ($i = 0; $i <= $height; $i++) {
		echo "<tr><td>$i</td>";
		
		for ($j = 0; $j <= $width; $j++) {
		    //get the value from the database instead of from the variable
			$value = getValue($i,  $j, $mapid, $level) ;
				
			if ($x == $i && $y == $j) {				
				echo "<td>";
				if ($value == "T") {
					echo "<font color=\"green\">";
        } else {
					echo "<font color=\"red\">";
        }
				
				echo "C</font></td>";
			} else {
				echo "<td><font color=\"";

					//we add color to the map by
					//changing the color of the letters
					switch ($value) {
						case "W": echo "#CCCCCC"; //gray
							break;
						case "S": echo "#0000FF"; //blue
							break;
						case "L": echo "#FFFF00"; //yellow
							break;
						case "T": echo "#009900"; //green
							break;
						case "X": echo "#000000"; //black
												  //make your game harder by changing these to white!
							break;
						case "E": echo "#FFFFFF"; //white
							break;
					}
					
				echo "\">" . $value . "</font></td>";
				
			}
			
		}
		echo "</tr>";
	}
	
	echo "</tr>
	</table>";
	
	echo "<br/>Your Coordinates: ($x, $y)</p><br/>";
}

/**************
* Load Maze File
* NOTE: This was designed specifiically for files made with the map editor
*	 	it's not guaranteed to work for a file you made and formatted yourself.
*		Please use the editor to generate these files for you.
**************/
function loadMap($filename, $level)
{
	$map = array();
	
	//if you keep the map files in a particular directory
	//fill that in below. Otherwise you can leave this blank
	
	//$directory = "";
	$directory = "./maps/";
	
	if (file_exists($directory . $filename . "_" . $level . ".txt")) {
		//open our file for reading the contents
		$fileline = file($directory . $filename . "_" . $level . ".txt");
		
		$x = 0;
		
		//while there is data in the file
		foreach ($fileline as $line_num => $line) {
			$i = 1;
			$y = 0; //we need to reset this each time
					//so our row always starts at zero
			
				//if this data is the start of our map
				if (substr($line, $i, 1) == "W") {
				
					//start pulling the info for the first row
					//keep loading in info until we reach the end of the line
					//in the row
					while ($i <= strlen($line)) {
						if ($i % 2 != 0) { //we do this so we don't load
										//in any of the spaces between characters
							$map[$x][$y] = substr($line, $i, 1);
							$y++;
						}
						$i++; //take the next character in the row
					}
					$x++; //increment to the next row
				}
		}
		return $map; //return the array with all the map data
	}
	else {
		return "File not found!";
  }
}

/**************
* Load Items File
**************/
function loadItems($array)
{
	/** fill in your code here **/
}

/**************
* This finds the position of the starting location
* marked with the value S and returns the X 
* coordinate. 
* MUST ALREADY BE CONNECTED TO THE DATABASE
**************/
function startPositionX($mapid, $level)
{
	global $database;

	$result = $database->select(
    "x",
    "mapdata",
    "WHERE mapid=$mapid and z=$level and value='S' LIMIT 1"
  );
	
	return $result['x'];
}

/**************
* This finds the position of the starting location
* marked with the value S and returns the Y
* coordinate. 
* MUST ALREADY BE CONNECTED TO THE DATABASE
**************/
function startPositionY($mapid, $level)
{
	global $database;
	
	$result = $database->select(
    "y",
    "mapdata",
    "WHERE mapid=$mapid and z=$level and value='S' LIMIT 1"
  );
	
	return $result['y'];
}

/**************
* This returns true if there is a pit in the given 
* coordinates x, y and level
* MUST ALREADY BE CONNECTED TO THE DATABASE
**************/
function hasPit($x, $y, $level)
{
	global $database;

	//see if there is a pit here
	$result = $database->select(
    "id",
    "mapdata", 
    "WHERE mapid=$mapid and z=$level AND value='X' LIMIT 1"
  );
	
	if ($result['id']) {
		return true;
  } else {
		return false;
  }
}

/**************
* Returns whatever value is on this spot
* in the map
**************/
function getValue($x, $y, $mapid, $level)
{
	global $database;
	$result = $database->select(
    "value",
    "mapdata",
    "WHERE x=$x and y=$y and mapid=$mapid and z=$level"
  );
	
	return $result['value'];
}

/**************
* This returns true if the treasure is at the given
* coordinates x, y. It MUST be passed an entire array
* filled with maze data for it to work and the x, y coordinates
* to check for the treasure. Otherwise it returns false.
**************/
function hasTreasure($x, $y, $array)
{
	global $database;

	$result = $database->select(
    "id",
    "mapdata",
    "WHERE mapid=$mapid and level=$level AND value='T' LIMIT 1"
  );
	
	if ($result['id']) {
		return true;
  } else {
		return false;
  }
}

/**************
* Checks to see if item array has a sword at x,y
**************/
function hasSword($x, $y, $array)
{
	if ($array[$x][$y] == "W") {
		return true;
  }
		
	return false;
}

/**************
* Checks to see if item array has gold at x,y
**************/
function hasGold($x, $y, $array)
{
	if ($array[$x][$y] == "G") {
		return true;
  }
		
	return false;
}

/**************
* Checks to see if item array has a money pouch at x,y
**************/
function hasPouch($x, $y, $array)
{
	if ($array[$x][$y] == "P") {
		return true;
  }
  
	return false;
}

/**************
* Checks to see if item array has a shield at x,y
**************/
function hasShield($x, $y, $array)
{
	if ($array[$x][$y] == "T") {
		return true;
  }
		
	return false;
}

/**************
* Checks to see if item array has a health tonic at x,y
**************/
function hasTonic($x, $y, $array)
{
	if ($array[$x][$y] == "T") {
		return true;
  }
		
	return false;
}

/**************
* Checks to see if item array has an item bag at x,y
**************/
function hasBag($x, $y, $array)
{
	if ($array[$x][$y] == "B") {
		return true;
  }
		
	return false;
}

/**************
* Checks to see if item array has a mega tonic at x,y
**************/
function hasMegaTonic($x, $y, $array)
{
	if ($array[$x][$y] == "T") {
		return true;
  }
		
	return false;
}

/**************
* Checks to see if item array has a locked door at x,y
**************/
function hasLockedDoor($x, $y, $array)
{
	/* fill in your code here */
}

/**************
* Checks to see if item array has an unlocked door at x,y
**************/
function hasUnlockedDoor($x, $y, $array)
{
	/* fill in your code here */
}


/**************
* Checks to see if item array has a key at x,y
**************/
function hasKey($x, $y, $array)
{
	/* fill in your code here */
}

/**************
* The player has decided to start a new game
**************/
function startNewGame()
{
	//this makes the session variable
	//accessible to this function
	global $_SESSION;
	
	$_SESSION['level'] = 1; //the top level on the map
							//higher numbers are deeper down
							
	$_SESSION['mapid'] = 1;
	
	//set the character's starting position on this map
	//remember each map has a starting position in a different
	//location. This finds the starting position for the map
	//you give it
	$_SESSION['x'] = startPositionX($_SESSION['mapid'], $_SESSION['level']);
	$_SESSION['y'] = startPositionY($_SESSION['mapid'], $_SESSION['level']);
}

/**************
* The player has decided to start a new game
**************/
function resetGame()
{
	//make these variables accessible
	//to the scope of this function
	global $_POST, $_SESSION;
	
	$_SESSION['gameover'] = false;
	
	//we reset both of these just in case you decide
	//to let them change which map they're on as they
	//play. When they start a new game we want them
	//to always start on this paricular map
	$_SESSION['level'] = 1;
	$_SESSION['mapid'] = 1;
	
	//reset their position
	//to the S in the current map
	$_SESSION['x'] = startPositionX($_SESSION['mapid'], $_SESSION['level']);
	$_SESSION['y'] = startPositionY($_SESSION['mapid'], $_SESSION['level']);
	
	//undo which button they pressed
	UNSET($_POST); 
}

/**************
* The character is trying to move
* Concept Note: when you find yourself using the same pieces of
*		code over and over again, it's good practice to combine
*		it all into one instead of re-writing the code again and
*		again. Functions are great for doing things like this
**************/
function moveCharacter($direction)
{
	global $_SESSION;
	
	//we need to get the character's current location
	$newx = $_SESSION['x'];
	$newy = $_SESSION['y'];
	
	switch($direction) { //we want to change what we're checking
					  //depending on the direction the character is moving
		case "right": $newy++; //add one to the y value
			break;
		case "left": $newy--; //subtract one from the y value
			break;
		case "back": $newx++; //add one to x vaue
			break;
		case "forward": $newx--; //subtract one from the x value
			break;
	}
	
	//everything else below should look really familiar to you
	//with the exception of adding the ladder to move up and
	//down to different levels
	
	if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level']) == "L") {
		//they are currently ON a ladder position
		//if they hit the up direction, move them up a level (if not at level 1)
		if ($direction == "forward" && $_SESSION['level'] != 1) {
			echo "You moved up the ladder!";
			
			//move them up a level
			$_SESSION['level'] = $_SESSION['level'] - 1;
			
			//we don't need to load a new map anymore, our database takes care of that!
	
			//set the character's starting position in the NEW map
			$_SESSION['x'] = startPositionX($_SESSION['mapid'], $_SESSION['level']);
			$_SESSION['y'] = startPositionY($_SESSION['mapid'], $_SESSION['level']);
		
		} else if ($direction != "back") {
				//let them move some other direction
				if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level']) == "T") {
					//the treasure is in this direction
					echo "You found the treasure!";
					$_SESSION['x'] = $newx;
					$_SESSION['y'] = $newy;
					$_SESSION['gameover'] = true;
				} else if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level']) == "W") {
					//don't update their position, they can't move here
					echo "You hit a wall!";
				} else if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level']) == "E") {
					//empty space, move them to this new location
					echo "You moved $direction one space.";
					$_SESSION['x'] = $newx;
					$_SESSION['y'] = $newy;
				} else if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level']) == "S") {
					//starting location, move them to this new location
					echo "You moved $direction one space.";
					$_SESSION['x'] = $newx;
					$_SESSION['y'] = $newy;
				} else if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level']) == "X") {
					//they found a pit
					echo "You fell into a pit and dropped down a level!";
						
					//move them down a level
					$_SESSION['level'] = $_SESSION['level'] + 1;
				
					$_SESSION['x'] = startPositionX($_SESSION['mapid'], $_SESSION['level']);
					$_SESSION['y'] = startPositionY($_SESSION['mapid'], $_SESSION['level']);
				} else if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level']) == "L") {
					//they found a ladder
					echo "You found a ladder. Move up or down?";
						
					//move them to the position on the map that has the ladder
					//but don't change which level they're on
					$_SESSION['x'] = $newx;
					$_SESSION['y'] = $newy;
				}
		}
		
		//if they hit the down direction, move them down a level (if not at level 5)
		if ($direction == "back" && $_SESSION['level'] != 5) {
			echo "You moved down the ladder!";
			
			//move them down a level
			$_SESSION['level'] = $_SESSION['level'] + 1;
	
			//set the character's starting position in the NEW map
			$_SESSION['x'] = startPositionX($_SESSION['mapid'], $_SESSION['level']);
			$_SESSION['y'] = startPositionY($_SESSION['mapid'], $_SESSION['level']);
    } else if ($direction != "forward") {
			//let them move some other direction
      if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level']) == "T") {
        //the treasure is in this direction
        echo "You found the treasure!";
        $_SESSION['x'] = $newx;
        $_SESSION['y'] = $newy;
        $_SESSION['gameover'] = true;
      } else if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level']) == "W") {
        //don't update their position, they can't move here
        echo "You hit a wall!";
      } else if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level']) == "E") {
        //empty space, move them to this new location
        echo "You moved $direction one space.";
        $_SESSION['x'] = $newx;
        $_SESSION['y'] = $newy;
      } else if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level']) == "S") {
        //starting location, move them to this new location
        echo "You moved $direction one space.";
        $_SESSION['x'] = $newx;
        $_SESSION['y'] = $newy;
      } else if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level']) == "X") {
        //they found a pit
        echo "You fell into a pit and dropped down a level!";

        //move them down a level
        $_SESSION['level'] = $_SESSION['level'] + 1;

        $_SESSION['x'] = startPositionX($_SESSION['mapid'], $_SESSION['level']);
        $_SESSION['y'] = startPositionY($_SESSION['mapid'], $_SESSION['level']);
      } else if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level']) == "L") {
        //they found a ladder
        echo "You found a ladder. Move up or down?";

        //move them to the position on the map that has the ladder
        //but don't change which level they're on
        $_SESSION['x'] = $newx;
        $_SESSION['y'] = $newy;
      }
    }
	} else if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level']) == "T") {
    //the treasure is in this direction
    echo "You found the treasure!";
    $_SESSION['x'] = $newx;
    $_SESSION['y'] = $newy;
    $_SESSION['gameover'] = true;
  } else if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level']) == "W") {
		//don't update their position, they can't move here
		echo "You hit a wall!";
	} else if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level'])== "E") {
		//empty space, move them to this new location
		echo "You moved $direction one space.";
		$_SESSION['x'] = $newx;
		$_SESSION['y'] = $newy;
	} else if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level']) == "S") {
		//starting location, move them to this new location
		echo "You moved $direction one space.";
		$_SESSION['x'] = $newx;
		$_SESSION['y'] = $newy;
	} else if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level']) == "X") {
		//they found a pit
		echo "You fell into a pit and dropped down a level!";
			
		//move them down a level
		$_SESSION['level'] = $_SESSION['level'] + 1;
	
		//set the character's starting position in the NEW map
		$_SESSION['x'] = startPositionX($_SESSION['mapid'], $_SESSION['level']);
		$_SESSION['y'] = startPositionY($_SESSION['mapid'], $_SESSION['level']);
	} else if (getValue($newx, $newy, $_SESSION['mapid'], $_SESSION['level']) == "L") {
		//they found a ladder
		echo "You found a ladder. Move up or down?";
			
		//move them to the position on the map that has the ladder
		//but don't change which level they're on
		$_SESSION['x'] = $newx;
		$_SESSION['y'] = $newy;
	}
}