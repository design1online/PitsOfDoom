<?php
/****************
* File: functions.php
* Date: 3.10.2009
* Author: jade@design1online.com
* Purpose: general funtions we always want access to
*****************/

/**************
* Create a new member in the game
**************/
function newMember($username, $password, $email, $first, $last, $type)
{
	global $_SESSION;
	
	//first we do all our checks to make sure things were entered correctly
	if (!$username)
		return errorMsg("You must enter a username");
		
	if (!$password)
		return errorMsg("You must enter a password");
		
	if (!$email)
		return errorMsg("You must enter an email");
		
	if (!$first)
		return errorMsg("You must give your character a first name");
		
	if (!$last)
		return errorMsg("You must give your character a last name");
		
	//now we check to make sure things are long enough
	if (strlen($password) < 5) //strlen() is a built in PHP function
		return errorMsg("Your password needs to be at least 5 characters long");
		
	if (strlen($username) < 3)
		return errorMsg("Your username needs to be at least 5 characters long");
		
	//now we have to check to make sure we don't end up with duplicates
	//some types of databases do things like this for you (called key constraints)
	//but I think it's only lazy programmers that rely on those to catch errors for you :p
	
	if (usernameExists($username)) //these are custom functions from this file
		return errorMsg("This username already exists, please pick another one");
		
	if (emailExists($email))
		return errorMsg("You already have an account on Pits of Doom. Try sending for your login information");
		
	//we escape any strange characters in the string
	$username = htmlspecialchars($username);
	$email = htmlspecialchars($email);
	$password = htmlspecialchars($password);
	$first = htmlspecialchars($first);
	$last = htmlspecialchars($last);
		
	//okay, everything check out, now we can create the member
	//and we store their ID number into an $id variable
	$id = $_SESSION['database']->insert("members", "username, email, password", "'$username', '$email', '$password'");

	//and make their character
	//depending on which type they chose, we want to generate random statistics
	$health = 100; //always start off with full health
	$speed = characterRandAttribute($type, "speed");
	$intelligence = characterRandAttribute($type, "intelligence");
	$strength = characterRandAttribute($type, "strength");
	$agility = characterRandAttribute($type, "agility");
	$magic = characterRandAttribute($type, "magic");
	$mapid = 1; //set them to a default map
	$z = 1; //set them to the first level of the map
	$x = startPositionX($mapid, $z);
	$y = startPositionY($mapid, $z);
	
	$_SESSION['database']->insert("characters", "memberid, firstname, lastname, type, health, speed, intelligence, strength, agility, magic, mapid, x, y, z", 
														   "'$id', '$first', '$last', '$type', '$health', '$speed', '$intelligence', '$strength', '$agility', '$magic', '$mapid', '$x', '$y', '$z'");
	
	//this clears all the form values
	unset($_POST);
	
	//that's it!!! How easy was that?!?
	return successMsg("Your account has been created. <a href=\"login.php\">Login and start playing</a>");
}

/**************
* Return a random value for this type and attribute
**************/
function characterRandAttribute($type, $attribute)
{
	global $_SESSION;
	$table = "character_types";
	$where = "WHERE id='$type'";
	
	//if we pass this speed, we want minspeed and maxspeed
	$minattribute = "min" . $attribute;
	$maxattribute = "max" . $attribute;
	
	$min = $_SESSION['database']->single($minattribute, $table, $where);
	$max = $_SESSION['database']->single($maxattribute, $table, $where);
	
	//now we return a random number generated between min - max
	return rand($min, $max);
}

/**************
* Checks to see if a username already exists
* Returns true if username is found
**************/
function usernameExists($username)
{
	global $_SESSION;
	
	$found = $_SESSION['database']->single("username", "members", "WHERE username='$username'");
	
	if ($found)
		return true;
	
	return false;
}

/**************
* Checks to see if an email already exists
* Returns true if email is found
**************/
function emailExists($email)
{
	global $_SESSION;
	
	$found = $_SESSION['database']->single("email", "members", "WHERE email='$email'");
	
	if ($found)
		return true;
	
	return false;
}

/**************
* Find and return an object from an array
* Note: this will not work for objects that don't have an ID
**************/
function findObject($array, $id)
{
	foreach($array as $object)
		if ($object->id == $id)
			return $object;
}

/**************
* The width of the array map
* MUST HAVE ALREADY CONNECTED TO DATABASE!!
**************/
function height($mapid, $level)
{
	global $_SESSION;

	//select the largest x coordinate
	$result = $_SESSION['database']->select("x", "mapdata", "WHERE mapid='$mapid' AND z='$level' ORDER BY x DESC LIMIT 1");
	
	return $result['x'];
}

/**************
* The height of the array map
* MUST HAVE ALREADY CONNECTED TO DATABASE!!
**************/
function width($mapid, $level)
{
	global $_SESSION;

	//select the largest x coordinate
	$result = $_SESSION['database']->select("y", "mapdata", "WHERE mapid='$mapid' AND z='$level' ORDER BY y DESC LIMIT 1");
	
	return $result['y'];
}

/**************
* The depth of the map
* MUST HAVE ALREADY CONNECTED TO DATABASE!!
**************/
function depth($mapid)
{
	global $_SESSION;

	//select the largest z coordinate
	$result = $_SESSION['database']->select("z", "mapdata", "WHERE mapid='$mapid' ORDER BY z DESC LIMIT 1");
	
	return $result['z'];
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
	
	if (file_exists($directory . $filename . "_" . $level . ".txt"))
	{
		//open our file for reading the contents
		$fileline = file($directory . $filename . "_" . $level . ".txt");
		
		$x = 0;
		
		//while there is data in the file
		foreach ($fileline as $line_num => $line)
		{
			$i = 1;
			$y = 0; //we need to reset this each time
					//so our row always starts at zero
			
				//if this data is the start of our map
				if (substr($line, $i, 1) == "W")
				{
				
					//start pulling the info for the first row
					//keep loading in info until we reach the end of the line
					//in the row
					while (substr($line, $i, 1) != "\n")
					{
						if ($i % 2 != 0) //we do this so we don't load
										//in any of the spaces between characters
						{
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
	else
		return "File not found!";
}

/**************
* This finds the position of the starting location
* marked with the value S and returns the X 
* coordinate. 
* MUST ALREADY BE CONNECTED TO THE DATABASE
**************/
function startPositionX($mapid, $level)
{
	global $_SESSION;

	$result = $_SESSION['database']->select("x", "mapdata", "WHERE mapid=$mapid and z=$level and value='S' LIMIT 1");
	
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
	global $_SESSION;
	
	$result = $_SESSION['database']->select("y", "mapdata", "WHERE mapid=$mapid and z=$level and value='S' LIMIT 1");
	
	return $result['y'];
}

/**************
* This returns true if there is a pit in the given 
* coordinates x, y and level
* MUST ALREADY BE CONNECTED TO THE DATABASE
**************/
function hasPit($x, $y, $level, $mapid)
{
	global $_SESSION;

	//see if there is a pit here
	$result = $_SESSION['database']->select("id", "mapdata", "WHERE mapid=$mapid and z=$level AND value='X' LIMIT 1");
	
	if ($result['id'])
		return true;
	else
		return false;
}

/**************
* Returns whatever value is on this spot
* in the map
**************/
function getValue($x, $y, $mapid, $level)
{
	global $_SESSION;
	$result = $_SESSION['database']->select("value", "mapdata", "WHERE x='$x' and y='$y' and mapid='$mapid' and z='$level'");
	
	return $result['value'];
}

/*******
* Return the name of this map
*******/
function mapName($id)
{
	global $_SESSION;
	
	$result = $_SESSION['database']->select("name", "map", "WHERE id='$id'");
	return $result['name'];
}

/*******
* Show a pre-formatted message for errors
*******/
function errorMsg($text)
{
	return "<center><span class=\"errorMsg\">Oops!</span> " . stripslashes($text) . ".</center><br/>";
}

/*******
* Show a pre-formatted message for successful actions
*******/
function successMsg($text)
{
	return "<center><span class=\"successMsg\">Success!</span> " . stripslashes($text) . ".</center><br/>";
}

/*******
* Show a pre-formatted message for questions
*******/
function questionMsg($text)
{
	return "<center><span class=\"questionMsg\">Huh.</span> " . stripslashes($text) . "?</center><br/>";
}

/*******
* Check to see if there is a monster at this spot
*******/
function hasMonster($x, $y, $z, $mapid)
{
	global $_SESSION;

	//see if there is a pit here
	$monsterid = $_SESSION['database']->single("id", "characters", "WHERE monster=1 AND x=$x and y=$y and z=$z and mapid=$mapid LIMIT 1");
	
	if ($monsterid)
		return $monsterid;
	else
		return 0;
}

/*******
* Check to see if there is another character in this spot
*******/
function hasMember($x, $y, $z, $mapid)
{
	global $_SESSION;

	$result = $_SESSION['database']->query("SELECT M.id FROM characters C 
			INNER JOIN members M ON M.id = C.memberid
			WHERE C.active=1 AND M.online=1 AND C.x='$x' AND C.y='$y' and C.z='$z'
			AND C.mapid = '$mapid' LIMIT 1");
	
	$row = mysql_fetch_assoc($result);

	return $row['id'];
}

/*******
* Prevent SQL Injections
*******/
function makeClean($value)
{
	if (is_array($value))
		return array_map('makeClean',$value);
	else
		return mysql_real_escape_string(trim($value));
}

/**************
* Display the map file on the screen
* 	and the character's position on the map
* Preconditions: map object and a member object
* MUST HAVE ALREADY CONNECTED TO DATABASE!!
**************/
function displayMap($member)
{
	global $_SESSION;
	
	//load information for this particular map
	$map = new mapobj($member->curcharacter->mapid, $member->curcharacter->z);
	
	echo "<center><br/><br/>
		<table cellpadding=\"0\" cellspacing=\"0\">";
		
	//factor in the character's viewport
	$fov = 4; //right now this defaults to a set value

	$xmin = $member->curcharacter->x - $fov;
	$xmax = $member->curcharacter->x + $fov;
	$ymin = $member->curcharacter->y - $fov;
	$ymax = $member->curcharacter->y + $fov;
	
	if ($xmin < 0)
	{
		$xmax += ($xmin * -1); 
		$xmin = 0;
	}

	if ($ymin < 0)
	{
		$ymax += ($ymin * -1); 
		$ymin = 0;
	}

	if ($xmax > width($member->curcharacter->mapid, $member->curcharacter->z))
	{
		$xmin -= ($xmax - width($member->curcharacter->mapid, $member->curcharacter->z));
		$xmax = width($member->curcharacter->mapid, $member->curcharacter->z)-1;
	}

	if ($ymax > height($member->curcharacter->mapid, $member->curcharacter->z))
	{
		$ymin -= ($ymax - width($member->curcharacter->mapid, $member->curcharacter->z));
		$ymax = height($member->curcharacter->mapid, $member->curcharacter->z)-1;
	}
	
	for ($i = $xmin; $i <= $xmax; $i++)
	{
		echo "<tr>";
		
		for ($j = $ymin; $j <= $ymax; $j++)
		{
		    //get the value from the database instead of from the variable
		    $value = $map->value($i, $j, $member->curcharacter->z);
		    $hasMember = hasMember($i, $j, $member->curcharacter->z, $member->curcharacter->mapid);
		
		    if ($value)
		    {
				//display the character
				if ($member->curcharacter->x == $i && $member->curcharacter->y == $j)
				{
					echo "<td width=" . $map->tile_width . " height=" . $map->tile_height . " style=\"background-image: url('" . $_SERVER['PHP_SELF'] . "/../images/tile.php?map=" . $member->curcharacter->mapid . "&x=" . $map->tile_x($i, $j, $member->curcharacter->z) . "&y=" . $map->tile_y($i, $j, $member->curcharacter->z) . "');\"><center><font color=\"";

					if ($hasMember && $hasMember != $member->id)
						echo "#FF0000";
					else
						echo "#FFFFFF";

					echo "\"><b>C</b></font></center></td>";

				}
				else //regular tile
				{
					echo "<td width=" . $map->tile_width . " height=" . $map->tile_height . " style=\"background-image: url('" . $_SERVER['PHP_SELF'] . "/../images/tile.php?map=" . $member->curcharacter->mapid . "&x=" . $map->tile_x($i, $j, $member->curcharacter->z) . "&y=" . $map->tile_y($i, $j, $member->curcharacter->z) . "');\"><center>";
			
					//display other characters and monsters
					if ($hasMember)
						echo "<center><font color=\"#00FF00\"><b>C</b></font></center>"; //show another member here
					else if (hasMonster($i, $j, $member->curcharacter->z, $member->curcharacter->mapid))
						echo "<center><font color=\"#0000FF\"><b>M</b></font></center>"; //show the monster
					
					echo "</div></td>";
				}
		    }
			
		}
		echo "</tr>";
	}
	
	echo "</tr>
	</table>
	</center>";
}

/**************
* Purpose: Move around the map (does this look familiar?!?)
* Precondition: the direction they're moving
* Postcondition: new  ocation or error message
 **************/
function parkMove($id, $direction)
{
	
	global $_SESSION;
	$data = getParkInfo($id);

	//we need to get the character's current location
	$newx = $data['x'];
	$newy = $data['y'];
	$where = "WHERE id='$id'";
	
	switch($direction) //we want to change what we're checking
					  //depending on the direction the character is moving
	{
		case "right": $newy++; //add one to the y value
			break;
		case "left": $newy--; //subtract one from the y value
			break;
		case "back": $newx++; //add one to x vaue
			break;
		case "forward": $newx--; //subtract one from the x value
			break;
	}
	
	//on a ladder
	if (getValue($data['x'], $data['y'], $data['map'], $data['z']) == "L")
	{
		//if they hit the up direction, move them up a level (if not at highest level)
		if ($direction == "forward" && $data['z'] != 1)
		{
			$message = successMsg("You moved up the ladder");
			
			//move them up a level
			$data['z'] = $data['z'] - 1;
	
			//set the character's starting position in the NEW map
			$data['x'] = startPositionX($data['map'], $data['z']);
			$data['y'] = startPositionY($data['map'], $data['z']);
			
			//now we save their position to the database so if they log off
			//or leave the game the character is still in this position when
			//they come back and play later
			$_SESSION['database']->update("players", "park_x={$data['x']}, park_y={$data['y']}, park_z={$data['z']}", $where);
		
		}
		//if they hit the down direction, move them down a level (if not at lowest level)
		else if ($direction == "back" && $data['z'] != 5)
		{
			$message = successMsg("You moved down the ladder");
			
			//move them down a level
			$data['z'] = $data['z'] + 1;
	
			//set the character's starting position in the NEW map
			$data['x'] = startPositionX($data['map'], $data['z']);
			$data['y'] = startPositionY($data['map'], $data['z']);
					
			//update their position
			$_SESSION['database']->update("players", "park_x={$data['x']}, park_y={$data['y']}, park_z={$data['z']}", $where);
		}
		
		else
		{
				//let them move some other direction
				if (getValue($newx, $newy, $data['map'], $data['z']) == "T")
				{
					//the treasure is in this direction
					$message = successMsg("You found the treasure");
					
					$data['x'] = $newx;
					$data['y'] = $newy;
					
					//update their position
					$_SESSION['database']->update("players", "park_x={$data['x']}, park_y={$data['y']}", $where);
					
				}

				else if (isBlocked($newx, $newy, $data['map'], $data['z']))
				{
					//don't update their position, they can't move here
					$message = errorMsg("You hit a wall");
				}
				
				else if (getValue($newx, $newy, $data['map'], $data['z']) == "S")
				{
					//starting location, move them to this new location
					$data['x'] = $newx;
					$data['y'] = $newy;
					
					//update their position
					$_SESSION['database']->update("players", "park_x={$data['x']}, park_y={$data['y']}", $where);
				}
					
				else if (getValue($newx, $newy, $data['map'], $data['z']) == "X")
				{
					//they found a pit
					$message = errorMsg("You fell into a pit and dropped down a level");
						
					//move them down a level
					$data['z'] = $data['z'] + 1;
				
					$data['x'] = startPositionX($data['map'], $data['z']);
					$data['y'] = startPositionY($data['map'], $data['z']);
					
					//update their position
					$_SESSION['database']->update("players", "park_x={$data['x']}, park_y={$data['y']}, park_z={$data['z']}", $where);
				}
				
				else if (getValue($newx, $newy, $data['map'], $data['z']) == "L")
				{
					//they found a ladder
					$message = questionMsg("You found a ladder. Move up or down");
						
					//move them to the position on the map that has the ladder
					//but don't change which level they're on
					$data['x'] = $newx;
					$data['y'] = $newy;
					
					//update their position
					$_SESSION['database']->update("players", "park_x={$data['x']}, park_y={$data['y']}", $where);
				}
				else
				{
					//empty space, move them to this new location
					$data['x'] = $newx;
					$data['y'] = $newy;
					
					//update their position
					$_SESSION['database']->update("players", "park_x={$data['x']}, park_y={$data['y']}", $where);
				}
		}
	}
	
	else if (getValue($newx, $newy, $data['map'], $data['z']) == "T")
	{
		//the treasure is in this direction
		$message = successMsg("You found the treasure");
		
		$data['x'] = $newx;
		$data['y'] = $newy;
					
		//update their position
		$_SESSION['database']->update("players", "park_x={$data['x']}, park_y={$data['y']}", $where);
		
	}

	else if (isBlocked($newx, $newy, $data['map'], $data['z']))
	{
		//don't update their position, they can't move here
		$message = errorMsg("You hit a wall");
	}
	
	else if (getValue($newx, $newy, $data['map'], $data['z']) == "S")
	{
		//starting location, move them to this new location
		
		$data['x'] = $newx;
		$data['y'] = $newy;
					
		//update their position
		$_SESSION['database']->update("players", "park_x={$data['x']}, park_y={$data['y']}", $where);
	}
		
	else if (getValue($newx, $newy, $data['map'], $data['z']) == "X")
	{
		//they found a pit
		$message = errorMsg("You fell into a pit and dropped down a level");
			
		//move them down a level
		$data['z'] = $data['z'] + 1;
	
		//set the character's starting position in the NEW map
		$data['x'] = startPositionX($data['map'], $data['z']);
		$data['y'] = startPositionY($data['map'], $data['z']);
					
		//update their position
		$_SESSION['database']->update("players", "park_x={$data['x']}, park_y={$data['y']}, park_z={$data['z']}", $where);
	}
	
	else if (getValue($newx, $newy, $data['map'], $data['z']) == "L")
	{
		//they found a ladder
		$message = questionMsg("You found a ladder. Move up or down");
			
		//move them to the position on the map that has the ladder
		//but don't change which level they're on
		$data['x'] = $newx;
		$data['y'] = $newy;
					
		//update their position
		$_SESSION['database']->update("players", "park_x={$data['x']}, park_y={$data['y']}", $where);
	}

	else
	{
		//empty space, move them to this new location
		
		$data['x'] = $newx;
		$data['y'] = $newy;
					
		//update their position
		$_SESSION['database']->update("players", "park_x={$data['x']}, park_y={$data['y']}", $where);
	}
	
	//display the message to the member
	return $message;
}
?>