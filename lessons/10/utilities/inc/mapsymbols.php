<?php
$mapsymbols[0] = "W"; //wall
$mapsymbols[1] = "E"; //empty spot
$mapsymbols[2] = "X"; //pit
$mapsymbols[3] = "S"; //starting position
$mapsymbols[4] = "L"; //ladder
$mapsymbols[5] = "T"; //treasure

/****************
* Purpose: get a list of the sprite sheets
* Precondition: $root must be the relative path to the folder with the sprite tile sheets
* Postcondition: an array with the tile sheet names is returned
*****************/
function getTileSheets($root)
{
	$array = scandir($root); //get all the files and directories
        $result = array(); //the list of file names we're building
	
	//loop through everything we found in that directory
	foreach ($array as $key => $name)
	{
		//if the result is a file name then we add it to our $result array
		if (is_file($root . $name) || is_file($root . "/" . $name))
			array_push($result, $name); //add the name to the array		
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

	$loop = $_SESSION['database']->query("SELECT id, name, depth FROM map");

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
?>