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
function newMember($username, $password, $email, $first, $last, $type) {
	global $_SESSION;
	
	//first we do all our checks to make sure things were entered correctly
	if (!$username) {
		return errorMsg("You must enter a username");
	}
	
	if (!$password) {
		return errorMsg("You must enter a password");
	}
		
	if (!$email) {
		return errorMsg("You must enter an email");
	}
	
	if (!$first) {
		return errorMsg("You must give your character a first name");
	}
	
	if (!$last) {
		return errorMsg("You must give your character a last name");
	}
	
	//now we check to make sure things are long enough
	if (strlen($password) < 5) { //strlen() is a built in PHP function
		return errorMsg("Your password needs to be at least 5 characters long");
	}
	
	if (strlen($username) < 3) {
		return errorMsg("Your username needs to be at least 3 characters long");
	}
		
	//now we have to check to make sure we don't end up with duplicates
	//some types of databases do things like this for you (called key constraints)
	//but I think it's only lazy programmers that rely on those to catch errors for you :p
	
	if (usernameExists($username)) { //these are custom functions from this file
		return errorMsg("This username already exists, please pick another one");
	}
	
	if (emailExists($email)) {
		return errorMsg("You already have an account on Pits of Doom. Try sending for your login information");
	}
	
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
	
	$_SESSION['database']->insert(
		"characters",
		"memberid, firstname, lastname, type, health, speed, intelligence, strength, agility, magic, mapid, x, y, z", 
		"'$id', '$first', '$last', '$type', '$health', '$speed', '$intelligence', '$strength', '$agility', '$magic', '$mapid', '$x', '$y', '$z'"
	);
	
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
	
	if ($found) {
		return true;
	}
	
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
	
	if ($found) {
		return true;
	}
	
	return false;
}

/**************
* Find and return an object from an array
* Note: this will not work for objects that don't have an ID
**************/
function findObject($array, $id)
{
	foreach($array as $object) {
		if ($object->id == $id) {
			return $object;
		}
	}
}

/**************
* The width of the array map
* MUST HAVE ALREADY CONNECTED TO DATABASE!!
**************/
function height($mapid, $level)
{
	global $_SESSION;

	//select the largest x coordinate
	$result = $_SESSION['database']->select("x", "mapdata", "WHERE mapid=$mapid and z=$level ORDER BY x DESC LIMIT 1");
	
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
	$result = $_SESSION['database']->select("y", "mapdata", "WHERE mapid=$mapid and z=$level ORDER BY y DESC LIMIT 1");
	
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
	$result = $_SESSION['database']->select("z", "mapdata", "WHERE mapid=$mapid ORDER BY z DESC LIMIT 1");
	
	return $result['z'];
}

/**************
* Display the map file on the screen
* 	and the character's position on the map
* Preconditions: map object and a member object
* MUST HAVE ALREADY CONNECTED TO DATABASE!!
**************/
function displayMap($member)
{
	//load information for this particular map and then
	//limit it to the level they're currently on to reduce database queries
	if (!$member->map) { //we don't want to load the map over and over again...
		$member->map = new mapobj($member->curcharacter->mapid, $member->curcharacter->z);
	}
	
	echo "<center>
		<table cellpadding=\"2\" cellspacing=\"2\">";
		
	//factor in the character's viewport
	$fov = 5; //right now this defaults to a set value
	
	for ($i = $member->curcharacter->x - $fov; $i <= $member->curcharacter->x + $fov; $i++) {
		echo "<tr>"; //<td>$i</td>";
		
		for ($j = $member->curcharacter->y - $fov; $j <= $member->curcharacter->y + $fov; $j++) {
		    //get the value from the database instead of from the variable
			$value = $member->map->value($i, $j, $member->curcharacter->z);
				
			if ($member->curcharacter->x == $i && $member->curcharacter->y == $j) {				
				echo "<td>";
				if ($value == "T") {
					echo "<font color=\"green\">";
				} else {
					echo "<font color=\"red\">";
				}
				
				echo "C</font></td>";
			}	else {
				echo "<td><font color=\"";

				  //display the appropriate info on the map
				  if (!hasMonster($i, $j, $member->curcharacter->z, $member->curcharacter->mapid)) {
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
					} else {
					echo "#666666"; //red
					$value = "M"; //replace with a monster
				}
					
				echo "\">" . $value . "</font></td>";
			}
			
		}
		echo "</tr>";
	}
	
	echo "</tr>
		</table>
	</center>";
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
	} else {
		return "File not found!";
	}
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
	$monsterid = $_SESSION['database']->single(
		"id",
		"characters",
		"WHERE monster=1 AND x=$x and y=$y and z=$z and mapid=$mapid LIMIT 1"
	);
	
	if ($monsterid) {
		return $monsterid;
	} else {
		return 0;
	}
}