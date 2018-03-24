<?php
/*******
* File: inc/mapsymbols.php
* Author: design1online.com
* Date: 2.8.2011
* Purpose: map related data and functions
*******/

/****************
* Purpose: get a list of the sprite sheets
* Precondition: $root must be the relative path to the folder with the sprite tile sheets
* Postcondition: an array with the tile sheet names is returned
*****************/
function getTileSheets($dir)
{
	$result = array();

   	 if ($dh = opendir($dir)) {
    	    while (($file = readdir($dh)) !== false) {
		if (!is_dir($file) && $file != "thumbs")
			array_push($result, $file);
    	    }
    	    closedir($dh);
	}

       //return whatever names we found
	return $result;
}

/****************
* Purpose: get a list of the maps on the game
* Precondition: must have a connection to the database
* Postcondition: an array with the map names is returned
*****************/
function getMapsArray()
{
	global $_SESSION;
	$result = array();

	$loop = $_SESSION['database']->query("SELECT id, name, 1 FROM park_maps");

	while ($row = mysql_fetch_array($loop))
	{
		$mapdata = array(
			"id" => $row['id'],
			"name" => $row['name'],
			"depth" => $row['depth']
		);

		array_push($result, $mapdata);
	}

	return $result;
}

/**
* Purpose: get park map and coordinates
**/
function getParkInfo($user_id)
{
	$data = array();
	$z = 1;

	$result = mysql_query("SELECT park_map, park_x, park_y, energy, happy FROM players WHERE id=$user_id")
		or die ('cannot select park information');

	$row = mysql_fetch_array($result);

	if (!$row['park_x'] && !$row['park_y'])
	{
		$row['park_x'] = startPositionX($row['park_map'], $z);
		$row['park_y'] = startPositionY($row['park_map'], $z);
	}

	$data = array("map" => $row['park_map'], 
			"x" => $row['park_x'], 
			"y" => $row['park_y'],
			"z" => $z, 
			"energy" => $row['energy'],
			"happy" => $row['happy']);

	return $data;
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
	
	return $result['x']+1;
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
	
	return $result['y']+1;
}

/**************
* The depth of the map
* MUST HAVE ALREADY CONNECTED TO DATABASE!!
**************/
function depth($mapid)
{
	global $_SESSION;

	//select the largest z coordinate
	//$result = $_SESSION['database']->select("z", "mapdata", "WHERE mapid=$mapid ORDER BY z DESC LIMIT 1");
	
	return 1;
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
	$data = getParkInfo($member);
	$energy_cost = 5;
	$where = "WHERE id='$member'";

	//if they don't have enough energy don't let them move
	if ($data['happy'] < 50)
	{
		echo "<center>Your mood is too low! You don't feel like exploring anymore.</center>";
		return;
	}
	if ($data['energy'] < 5)
	{
		echo "<center>Your energy is low! You're too tired to continue.</center>";
		return;
	}

	//decrease energy
	if ($member != 186 && $member != 1)
		mysql_query("UPDATE players SET energy = energy - $energy_cost WHERE id = '$member'")
		or die ('cannot update player energy on park map display');

	//check if this is an interactive area
	if ($url = interactiveArea($data['x'], $data['y'], $data['z'], $data['map']))
	{
		//move them back to a non-interactive space
		if (!isBlocked($data['x']-1, $data['y'], $data['map'], $data['z']))
			$_SESSION['database']->update("players", "park_x=" . ($data['x']-1) . ", park_y={$data['y']}, park_z={$data['z']}", $where);
		else if (!isBlocked($data['x']+1, $data['y'], $data['map'], $data['z']))
			$_SESSION['database']->update("players", "park_x=" . ($data['x']+1) . ", park_y={$data['y']}, park_z={$data['z']}", $where);
		else if (!isBlocked($data['x'], $data['y']-1, $data['map'], $data['z']))
			$_SESSION['database']->update("players", "park_x={$data['x']}, park_y=" . ($data['y']-1) . ", park_z={$data['z']}", $where);
		else
			$_SESSION['database']->update("players", "park_x={$data['x']}, park_y=" . ($data['y']+1) . ", park_z={$data['z']}", $where);
		
		//redirect them to the interactive area	
		echo "<script language=\"javascript\">
			window.location = \"" . $url . "\";
			</script>";
		return;
	}

	//check to see if they've found an animal
	if ($animal = hasAnimal($data['x'], $data['y'], $data['z'], $data['map']))
	{
		//add the animal to their journal
		$result = mysql_query("SELECT img, name, found_desc FROM animal_reg WHERE id='$animal'")
			or die ('cannot select animal information');

		$row = mysql_fetch_array($result);

		mysql_query("INSERT INTO
					animals_spotted 
					(
						animal_key, 
						user,
						name, 
						park_id,
						date
					) 
				VALUES
					(
						'$animal',
						'$member',
						'{$row['name']}',
						'{$data['map']}',
						NOW())
				") or die('could not add spotted animal to your animal journal from parks');

		echo "<center><img src=\"{$row['img']}\" border=\"0\"><br/>Congratulations! You spotted a " . stripslashes($row['name']) . ".<p>" . stripslashes($row['found_desc']) . "<p>Your animal index has been updated.</center>";

		return;
	}

	//load information for this particular map
	$map = new mapobj($data['map'], $data['z']);
	
	echo "<center><br/><br/>
		<table cellpadding=\"0\" cellspacing=\"0\">";
		
	//factor in the character's viewport
	$fov = 2; //right now this defaults to a set value

	$xmin = $data['x'] - $fov;
	$xmax = $data['x'] + $fov;
	$ymin = $data['y'] - $fov;
	$ymax = $data['y'] + $fov;
	
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

	if ($xmax > width($data['map'], $data['z']))
	{
		$xmin -= ($xmax - width($data['map'], $data['z']));
		$xmax = width($data['map'], $data['z'])-1;
	}

	if ($ymax > height($data['map'], $data['z']))
	{
		$ymin -= ($ymax - width($data['map'], $data['z']));
		$ymax = height($data['map'], $data['z'])-1;
	}
	
	for ($i = $xmin; $i <= $xmax; $i++)
	{
		echo "<tr>";
		
		for ($j = $ymin; $j <= $ymax; $j++)
		{
		    //get the value from the database instead of from the variable
		    $value = $map->value($i, $j, $data['z']);
		    $blocked = $map->isBlocked($i, $j, $data['z']);
		
		    if ($value)
		    {
			//display the character
			if ($data['x'] == $i && $data['y'] == $j)
			{				
				echo "<td width=" . $map->tile_width . " height=" . $map->tile_height . " style=\"background-image: url('images/tile.php?map=" . $map->id . "&x=" . $map->tile_x($i, $j) . "&y=" . $map->tile_y($i, $j) . "');\"><center><img src=images/compass/goldmarker.gif border=0 alt=you></center></td>";
			}
			else //regular tile
			{
				echo "<td width=" . $map->tile_width . " height=" . $map->tile_height . " style=\"background-image: url('images/tile.php?map=" . $map->id . "&x=" . $map->tile_x($i, $j) . "&y=" . $map->tile_y($i, $j) . "');\"></div></td>";
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
* This finds the position of the starting location
* marked with the value S and returns the X 
* coordinate. 
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
**************/
function startPositionY($mapid, $level)
{
	global $_SESSION;
	
	$result = $_SESSION['database']->select("y", "mapdata", "WHERE mapid=$mapid and z=$level and value='S' LIMIT 1");
	
	return $result['y'];
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

/**************
* Returns true if this square is blocked
**************/
function isBlocked($x, $y, $mapid, $z)
{
	$area_id = getValue($x, $y, $mapid, $z);

	$result = mysql_query("SELECT blocked FROM park_areas WHERE id='$area_id'")
		or die ('cannot see if this area is blocked');
	$row = mysql_fetch_array($result);
	return $row['blocked'];
}	

/*******
* Return the id of the map the player is currently on
*******/
function currentMap($id)
{
	global $_SESSION;
	$result = $_SESSION['database']->select("park_map", "players", "WHERE id='$id'");

	return $result['park_map'];
}

/*******
* Return the name of this map the user is on
*******/
function mapName($id)
{
	global $_SESSION;
	$data = getParkInfo($id);
	
	$result = $_SESSION['database']->select("name", "park_maps", "WHERE id='" . $data['map'] . "'");
	return $result['name'];
}

/*******
* Check to see if this is an interactive area
*******/
function interactiveArea($x, $y, $z, $map)
{

	//check to see what type of map tile they're on
	$result = mysql_query("SELECT A.url FROM mapdata M
				INNER JOIN park_areas A on A.id = M.value 
				WHERE mapid='$map' AND x='$x' AND y='$y' AND z='$z'")
		or die ('cannot select tile area');

	$row = mysql_fetch_assoc($result);

	return $row['url'];
}


/*******
* Check to see if they've found an animal
*******/
function hasAnimal($x, $y, $z, $map)
{
	global $_SESSION;
	$PERCENT_CHANCE_OF_SEEING_ANIMAL = 10;

	//check to see if they have the animal spotter equipped
	$result = mysql_query("SELECT item_id FROM items WHERE item_key=239 AND status='Eq' AND
		owner_id='{$_SESSION['id']}'")
		or die ('cannot see if they have the animal spotter equipped');

	$row = mysql_fetch_assoc($result);
	
	if ($row['item_id'])
		$PERCENT_CHANCE_OF_SEEING_ANIMAL += 5;

	//check to see what type of map tile they're on
	$result = mysql_query("SELECT value FROM mapdata WHERE mapid='$map' AND x='$x' AND y='$y' AND z='$z'")
		or die ('cannot select tile area');

	$row = mysql_fetch_assoc($result);

	//check to see if there are any animals linked to that tile type
	$result = mysql_query("SELECT animal_id FROM park_animal_areas WHERE area_id='{$row['value']}' ORDER BY rand() LIMIT 1")
		or die ('cannot fetch animals found in this tile area');

	$row = mysql_fetch_assoc($result);

	//if there are animals and they see one
	if (mysql_num_rows($result) && (rand(0, 500) <= $PERCENT_CHANCE_OF_SEEING_ANIMAL))
		return $row['animal_id'];

	return 0;
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

function getParkAreaName($id)
{
	$result = mysql_query("SELECT name FROM park_areas WHERE id='$id'")
		or die ('cannot select park area name');
	$row = mysql_fetch_assoc($result);
	return $row['name'];
}

function successMsg($msg)
{
	echo "<center><b>Success!</b> $msg</center>";
}

function questionMsg($msg)
{
	echo "<center>$msg?</center>";
}

function errorMsg($msg)
{
	echo "<center><b>Error:</b> $msg</center>";
}
?>