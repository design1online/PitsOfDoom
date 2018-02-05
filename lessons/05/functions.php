<?php
/**************
* File: functions.php
* Purpose: we'll put all of our functions in here
*	so our page looks cleaner and easier to read
**************/

/**************
* The width of the array map
**************/
function height($map)
{
	return sizeof($map)-1;
}

/**************
* The height of the array map
**************/
function width($map)
{
	return sizeof($map[0])-1;
}

/**************
* Display the map file on the screen
* 	and the character's position on the map
**************/
function displayMap($x, $y, $map, $width, $height)
{
	echo '<p align="center">
		    <table cellpadding="2" cellspacing="2">
		      <tr>
            <td></td>';
		  
	for ($i = 0; $i < $width; $i++) {
		echo "<td>$i</td>";
	}
	
	for ($i = 0; $i <= $height; $i++) {
		echo "<tr><td>$i</td>";
		
		for ($j = 0; $j <= $width; $j++) {
			if ($x == $i && $y == $j) {
				echo "<td>";
				if ($map[$i][$j] == "T") {
					echo "<font color=\"green\">";
        } else {
					echo "<font color=\"red\">";
        }
				
				echo "C</font></td>";
			} else {
				echo "<td><font color=\"";

        //we add color to the map by
        //changing the color of the letters
        switch ($map[$i][$j]) {
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
					
				echo "\">" . $map[$i][$j] . "</font></td>";
				
			}
			
		}
		echo "</tr>";
	}
	
	echo "  </tr>
	  </table>
    <br/>Your Coordinates: ($x, $y)
  </p>";
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
	$directory = "maps/";
	
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
					while (substr($line, $i, 1) != "\n") {
						if ($i % 2 != 0) {
              //we do this so we don't load
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
  } else {
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
* coordinate. It MUST be passed an entire array
* filled with maze data for it to work.
*
* Notice: The variable name in the function parameter
* 	(in this case its $array) does not have to match
*	the name of the variable you pass to the function
*	when you call it.
**************/
function startPositionX($array)
{
	for ($x = 0; $x < height($array); $x++) {
		for ($y = 0; $y < width($array); $y++) {
			if ($array[$x][$y] == "S") {
				return $x;
      }
    }
  }
}

/**************
* This finds the position of the starting location
* marked with the value S and returns the Y
* coordinate. It MUST be passed an entire array
* filled with maze data for it to work.
**************/
function startPositionY($array)
{
	for ($x = 0; $x < height($array); $x++) {
		for ($y = 0; $y < width($array); $y++) {
			if ($array[$x][$y] == "S") {
				return $y;
      }
    }
  }
}

/**************
* This returns true if there is a pit in the given 
* coordinates x, y. It MUST be passed an entire array
* filled with maze data for it to work and the x, y coordinates
* to check for a pit. Otherwise it returns false.
**************/
function hasPit($x, $y, $array)
{
	if ($array[$x][$y] == "X") {
		return true;
  }
		
	return false;
}

/**************
* This returns true if there is a item in the given 
* coordinates x, y. It MUST be passed an entire array
* filled with ITEM data for it to work and the x, y coordinates
* to check for a item. If it finds an item it returns the item
* symbol, otherwise it return 0;
**************/
function hasItem($x, $y, $array)
{
	if ($array[$x][$y] != 0) {
		return $array[$x][$y];
  }
		
	return 0;
}

/**************
* This returns true if the treasure is at the given
* coordinates x, y. It MUST be passed an entire array
* filled with maze data for it to work and the x, y coordinates
* to check for the treasure. Otherwise it returns false.
**************/
function hasTreasure($x, $y, $array)
{
	if ($array[$x][$y] == "T") {
		return true;
  }
		
	return false;
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
							
	$_SESSION['mapname'] = "Round Hill";
							
	//since they haven't played yet we load this map by default
	$_SESSION['map'] = loadMap($_SESSION['mapname'], $_SESSION['level']);
	
	//set the character's starting position on this map
	//remember each map has a starting position in a different
	//location. This finds the starting position for the map
	//you give it
	$_SESSION['x'] = startPositionX($_SESSION['map']);
	$_SESSION['y'] = startPositionY($_SESSION['map']);
}

/**************
* The player has decided to start a new game
**************/
function resetGame()
{
  //lets start the new game
	startNewGame();
	
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
	
	switch($direction) { 
    //we want to change what we're checking
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
	if ($_SESSION['map'][$_SESSION['x']][$_SESSION['y']] == "L") {
		//they are currently ON a ladder position
		//if they hit the up direction, move them up a level (if not at level 1)
		if ($direction == "forward" && $_SESSION['level'] != 1) {
			echo "You moved up the ladder!";
			
			//move them up a level
			$_SESSION['level'] = $_SESSION['level'] - 1;
			
			//load a new map
			$_SESSION['map'] = loadMap($_SESSION['mapname'], $_SESSION['level']);
	
			//set the character's starting position in the NEW map
			$_SESSION['x'] = startPositionX($_SESSION['map']);
			$_SESSION['y'] = startPositionY($_SESSION['map']);
      
		} else if ($direction != "back") {
				//let them move some other direction
				if ($_SESSION['map'][$newx][$newy] == "T") {
					//the treasure is in this direction
					echo "You found the treasure!";
					$_SESSION['x'] = $newx;
					$_SESSION['y'] = $newy;
					$_SESSION['gameover'] = true;
          
				} else if ($_SESSION['map'][$newx][$newy] == "W") {
					//don't update their position, they can't move here
					echo "You hit a wall!";
          
				} else if ($_SESSION['map'][$newx][$newy] == "E") {
					//empty space, move them to this new location
					echo "You moved $direction one space.";
					$_SESSION['x'] = $newx;
					$_SESSION['y'] = $newy;
          
				} else if ($_SESSION['map'][$newx][$newy] == "S") {
					//starting location, move them to this new location
					echo "You moved $direction one space.";
					$_SESSION['x'] = $newx;
					$_SESSION['y'] = $newy;
          
				} else if ($_SESSION['map'][$newx][$newy] == "X") {
					//they found a pit
					echo "You fell into a pit and dropped down a level!";
						
					//move them down a level
					$_SESSION['level'] = $_SESSION['level'] + 1;
						
					//load a new map
					$_SESSION['map'] = loadMap($_SESSION['mapname'], $_SESSION['level']);
				
					//set the character's starting position in the NEW map
					$_SESSION['x'] = startPositionX($_SESSION['map']);
					$_SESSION['y'] = startPositionY($_SESSION['map']);
          
				} else if ($_SESSION['map'][$newx][$newy] == "L") {
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
			
			//load a new map
			$_SESSION['map'] = loadMap($_SESSION['mapname'], $_SESSION['level']);
	
			//set the character's starting position in the NEW map
			$_SESSION['x'] = startPositionX($_SESSION['map']);
			$_SESSION['y'] = startPositionY($_SESSION['map']);
		
    } else if ($direction != "forward") {
			//let them move some other direction
				if ($_SESSION['map'][$newx][$newy] == "T") {
					//the treasure is in this direction
					echo "You found the treasure!";
					$_SESSION['x'] = $newx;
					$_SESSION['y'] = $newy;
					$_SESSION['gameover'] = true;
				
        } else if ($_SESSION['map'][$newx][$newy] == "W") {
					//don't update their position, they can't move here
					echo "You hit a wall!";
     
				} else if ($_SESSION['map'][$newx][$newy] == "E") {
					//empty space, move them to this new location
					echo "You moved $direction one space.";
					$_SESSION['x'] = $newx;
					$_SESSION['y'] = $newy;
          
				} else if ($_SESSION['map'][$newx][$newy] == "S") {
					//starting location, move them to this new location
					echo "You moved $direction one space.";
					$_SESSION['x'] = $newx;
					$_SESSION['y'] = $newy;
          
        } else if ($_SESSION['map'][$newx][$newy] == "X") {
					//they found a pit
					echo "You fell into a pit and dropped down a level!";
						
					//move them down a level
					$_SESSION['level'] = $_SESSION['level'] + 1;
						
					//load a new map
					$_SESSION['map'] = loadMap($_SESSION['mapname'], $_SESSION['level']);
				
					//set the character's starting position in the NEW map
					$_SESSION['x'] = startPositionX($_SESSION['map']);
					$_SESSION['y'] = startPositionY($_SESSION['map']);
          
				} else if ($_SESSION['map'][$newx][$newy] == "L") {
					//they found a ladder
					echo "You found a ladder. Move up or down?";
						
					//move them to the position on the map that has the ladder
					//but don't change which level they're on
					$_SESSION['x'] = $newx;
					$_SESSION['y'] = $newy;
				}
		}
	} else if ($_SESSION['map'][$newx][$newy] == "T") {
		//the treasure is in this direction
		echo "You found the treasure!";
		$_SESSION['x'] = $newx;
		$_SESSION['y'] = $newy;
		$_SESSION['gameover'] = true;
	} else if ($_SESSION['map'][$newx][$newy] == "W") {
		//don't update their position, they can't move here
		echo "You hit a wall!";
	}	else if ($_SESSION['map'][$newx][$newy] == "E") {
		//empty space, move them to this new location
		echo "You moved $direction one space.";
		$_SESSION['x'] = $newx;
		$_SESSION['y'] = $newy;
	} else if ($_SESSION['map'][$newx][$newy] == "S") {
		//starting location, move them to this new location
		echo "You moved $direction one space.";
		$_SESSION['x'] = $newx;
		$_SESSION['y'] = $newy;
	} else if ($_SESSION['map'][$newx][$newy] == "X") {
		//they found a pit
		echo "You fell into a pit and dropped down a level!";
			
		//move them down a level
		$_SESSION['level'] = $_SESSION['level'] + 1;
			
		//load a new map
		$_SESSION['map'] = loadMap($_SESSION['mapname'], $_SESSION['level']);
	
		//set the character's starting position in the NEW map
		$_SESSION['x'] = startPositionX($_SESSION['map']);
		$_SESSION['y'] = startPositionY($_SESSION['map']);
    
	} else if ($_SESSION['map'][$newx][$newy] == "L") {
		//they found a ladder
		echo "You found a ladder. Move up or down?";
			
		//move them to the position on the map that has the ladder
		//but don't change which level they're on
		$_SESSION['x'] = $newx;
		$_SESSION['y'] = $newy;
	}
}