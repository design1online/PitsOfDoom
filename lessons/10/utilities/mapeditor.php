<?php
/****************
* File: mapeditor.php
* Date: 2.8.2011
* Author: design1online.com
* Purpose: create maps in the game
*****************/
require_once('../oop/mysqlobj.php'); //database object
require_once('../oop/mapobj.php'); //map object
require_once('../oop/coordinateobj.php'); //map coordinate object
require_once('../inc/functions.php'); //include the functions						   
require_once('../inc/dbconnect.php'); //connect to the database
require_once('inc/mapsymbols.php'); //all of our map functions
$USE_SPRITES = true;

if ($_POST['mainmenu'] || !$_SESSION) {
	session_destroy();
	header("Location: mapeditor.php");
	exit;
}

if ($_POST['update']) //update/edit the map data
{
	
	//select the ID of the map with this name
	$result = $database->select("id", "map", "WHERE name='" . $_SESSION['name'] . "'");

	if (!$result['id']) //map file name wasn't found in the database
		$id = $database->insert("map", "name", "'" .  $_SESSION['name'] . "'"); //add a map with this file name
	else
		$id = $result['id']; //we found the right id with the database select
	
	for ($i = 0; $i <= $_SESSION['height']-1; $i++)
	{
		for ($j = 0; $j <= $_SESSION['width']-1; $j++)
		{
			//if this map already exists then update this row
			//otherwise insert this as a new row
			
			//see if this row is already in the database
			$result = $database->select("id", "mapdata", "WHERE mapid=$id AND x=$i and y=$j and z=" . $_SESSION['level'] . "");

			if ($_POST["image" . $i . "_" . $j])
			{
				$tilecoord = explode("_", $_POST["image" . $i . "_" . $j]);
				$database->update("mapdata", "tile_x='{$tilecoord[0]}', tile_y='{$tilecoord[1]}'", "WHERE mapid=$id and x=$i and y=$j and z=" . $_SESSION['level'] . "");
			}
				
			//we found this row already in the database, so just update it
			if ($result['id']) 
				$database->update("mapdata", "value='" .  ($_POST["tile" . $i . "_" . $j])  . "'", "WHERE id='" . $result['id'] . "'");
			else //we haven't found a row like this
				$database->insert("mapdata", "x, y, z, value, mapid, tile_x, tile_y", "'$i', '$j', '" . $_SESSION['level'] . "', '" . ($_POST["tile" . $i . "_" . $j])  . "', '{$tilecoord[0]}', '{$tilecoord[1]}', '$id'");
		}
	}

	//reload the map
	$_SESSION['map'] = new mapobj($_SESSION['mapid'], $_SESSION['level']);
	
	$error = "Map changes saved for level {$_SESSION['level']}!";
	
	unset($_POST);
}

if ($_POST['startmap']) //start creating our map, setup our map variables
{

	if (!$_POST['width'] || $_POST['width'] <= 0)
		$error = "This map must have a valid width.";
	else
		$_SESSION['width'] = $_POST['width'];
		
	if (!$_POST['height'] || $_POST['height'] <= 0)
		$error = "This map must have a valid height.";
	else
		$_SESSION['height'] = $_POST['height'];
		
	if (!$_POST['name'])
		$_SESSION['name'] = "Default Map";
	else
		$_SESSION['name'] = $_POST['name'];
		
	if (!$_POST['level'])
		$_SESSION['level'] = 1;
	else
		$_SESSION['level'] = $_POST['level'];

	//add a map with this file name
	$id = $database->insert("map", "name", "'" .  $_SESSION['name'] . "'"); 

	for ($i = 0; $i <= $_SESSION['height']-1; $i++)
	{
		for ($j = 0; $j <= $_SESSION['width']-1; $j++)
		{
			$database->insert("mapdata", "x, y, z, mapid", "'$i', '$j', '" . $_SESSION['level'] . "', '$id'");
		}
	}

	$_SESSION['map'] = array();
	$error = "New map created!";
}

//loading a map
if ($_POST['loadmap'])
{
	$_SESSION['mapid'] = $_POST['id'];

	if (!$_POST['level'])
		$_SESSION['level'] = 1;
	else
		$_SESSION['level'] = $_POST['level'];

	if (!$_SESSION['level'])
		$_SESSION['level'] = 1;

	//load the map and set the session variables
	$_SESSION['map'] = new mapobj($_SESSION['mapid'], $_SESSION['level']);

	$_SESSION['name'] = $_SESSION['map']->name;
	$_SESSION['width'] = $_SESSION['map']->width;
	$_SESSION['height'] = $_SESSION['map']->height;
	$_SESSION['tile_width'] = $_SESSION['map']->tile_width;
	$_SESSION['tile_height'] = $_SESSION['map']->tile_height;
}

if ($_POST['editmap'])
{
	if (!$_POST['level'])
		$_SESSION['level'] = 1;
	else
		$_SESSION['level'] = $_POST['level'];
		
	//update the map tilesheet info
	$_SESSION['map'] = new mapobj($_POST['tile_mapid'], $_SESSION['level']);
	$_SESSION['map']->updateTilesheet($_POST['sheet'], $_POST['tile_width'], $_POST['tile_height'], $_POST['tile_xoffset'], $_POST['tile_yoffset']);
	$_SESSION['map'] = array();
	$error = "Map tilesheet updated!";
}

if (!$_POST['update'])
{
?>
<html>
<head>
<title>Map Editor</title>
<script type="text/javascript" src="inc/utilities.js"></script>
<link rel="stylesheet" type="text/css" href="../js/dd.css" />
<script type="text/javascript" src="../js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../js/jquery.dd.js"></script>
<script type="text/javascript" src="../js/mapeditor.js"></script>
</head>
<body>
<form action="#" method="post" name="form">
	<div id="main">
		<div id="content">
		<h2 align="center">Map Editor</h2>
<?php
//first they have to select the map information
if (empty($_SESSION['map']))
{
?>
<center>
<?php if ($error) echo "<br/>$error<br/><br/>"; ?>
</center>
<blockquote>
<table width="100%">
	<tr>
		<td valign="top">
			<center><b>Create New</b></center><br/>
			Name: <input type="text" name="name" /><br/><br/>
			Map Height: <input type="text" name="height" size="3" value="15" /><br/><br/>
			Map Width: <input type="text" name="width" size="3" value="15" />
			<center><input type="submit" name="startmap" value="Create Map" /><br/>
			You must change the tilesheet once your map has been created.</center>
		</td>
		<td valign="top" width="50%">
			<center><b>Edit Existing</b></center><br/>
			Map: <select name="id" />
				<?php
					foreach(getMapsArray() as $key => $map)
						echo "<option value='" . $map['id'] . "'>" . $map['name'] . "</option>";
				?>
				</select>
				<br/><br/>
			Level: <input type="text" name="level" maxlength="2" size="4" />
				<br/><br/>
				<center><input type="submit" name="loadmap" value="Load" /></center>
				<br/><br/>
			</center>
		<?php 
		//if we have these enabled
		if ($USE_SPRITES)
		{
		?>
			<br/>
			<center><b>Change Tilesheet</b></center><br/><br/>
			Map: <select name="tile_mapid" />
				<?php
					foreach(getMapsArray() as $key => $map)
					{
						echo "<option value='" . $map['id'] . "'>" . $map['name'] . "</option>";
					}
				?>
				</select>
			<br/><br/>
			Tile Sheet: <select name="sheet">
		<?php
  			foreach(getTileSheets("../images/tiles") as $key => $value)
			{
				echo "<option value='$value'";
				if ($_SESSION['map']->tile_sheet == $value) echo " selected";
				echo ">$value</option>";
			}
		?>
				</select><br/><br/>
			Tile Height: <input type="text" name="tile_height" size="3" value="48" /><br/><br/>
			Tile Width: 	<input type="text" name="tile_width" size="3" value="48" /><br/><br/>
			Tile Xoffset: <input type="text" name="tile_xoffset" value="2" /><br/><br/>
			Tile Yoffset: <input type="text" name="tile_yoffset" value="2" />
		<?php
		}
		?>
		<br/><br/>
			<center><input type="submit" name="editmap" value="Change Tiles" /><br/>
				add tile sheets to your images/tiles folder</center>

		</td>
	</tr>
</table>
</blockquote>
<?php
}
else //display the map editor
{
	
	echo "<br/><br/><center><b>" . $_SESSION['name'] . " Level " . $_SESSION['level'] . "</b><br/>
	Dimensions: " . $_SESSION['width'] . " X " . $_SESSION['height'] . "</center><br/><br/>";
	
	echo "<center>
		<table cellpadding=\"5\" cellspacing=\"2\">
			<tr>
				<td>
			<table cellpadding=\"1\" cellspacing=\"1\">
			<tr>
				<td></td>";

	//does this look familiar?!?
	for ($i = 0; $i < $_SESSION['width']; $i++)
		echo "<td>$i</td>";
		
	echo "</tr>";
		
	for ($i = 0; $i < $_SESSION['height']; $i++)
	{
	
		echo "<tr><td>$i</td>";
	
		for ($j = 0; $j < $_SESSION['width']; $j++)
		{
			echo "<td><div id=\"$i" . "_" . "$j\" style=\"width:{$_SESSION['tile_width']}; height: {$_SESSION['tile_height']}; background-image: url('../images/tile.php?map=" . $_SESSION['mapid'] . "&x=" . $_SESSION['map']->tile_x($i, $j, $_SESSION['level']) . "&y=" . $_SESSION['map']->tile_y($i, $j, $_SESSION['level']) . "');\" class=\"tilebox\">";

			echo $_SESSION['map']->value($i, $j, $_SESSION['level']) . "\n
			<input type=\"hidden\" id=\"tilename" . $i . "_" . $j . "\" name=\"tilename" . $i . "_" . $j . "\" value=\"" . $_SESSION['map']->value($i, $j, $_SESSION['level']) . "\" />
			<input type=\"hidden\" id=\"tile" . $i . "_" . $j . "\" name=\"tile" . $i . "_" . $j . "\"value=\"" . $_SESSION['map']->value($i, $j, $_SESSION['level']) . "\" />
			<input type=\"hidden\" id=\"image" . $i . "_" . $j . "\" name=\"image" . $i . "_" . $j . "\" value=\"\" />
			</div></td>";
		}
			
		echo "</tr>";
	
	}
		
	echo "</table>
			</td>
			<td valign=\"top\">
				<center><h2>Modify Cell</h2></center>

				<div id=\"selectedTileInfo\"><b>Selected Tile:</b> None</div>
				<br/>
				<b>Tile Type:</b> <br/>
					<select id=\"tileType\">";
					
				foreach($mapsymbols as $index => $value)
					echo "<option value=\"$value\">$value</option>";

	echo "
					</select><br/><br/>
					
				<b>Tile Image:</b><br/>
	<select name=\"tileimage\" id=\"tileimage\" style=\"display:none; width:" . ($_SESSION['tile_width'] + 25) . ";\" class=\"sheet\">";

	for ($i = 0; $i < 12; $i++)
	{
		for ($j = 0; $j < 10; $j++)
			echo "<option value='$i_$j' title='../images/tile.php?map={$_SESSION['mapid']}&x=$i&y=$j'>$i,$j</option>";
	}
echo "
	</select>
	<input type=\"hidden\" name=\"level\" value=\"" . $_SESSION['level'] . "\" />
			<center>
				<br/><br/><br/><br/>
				<b>Done with all of your changes?</b><br/><br/>
				<input type=\"submit\" name=\"update\" value=\"Update Map\"/>
				<br/><br/><br/><br/>
					<b>Return to the main map screen?</b><br/><br/>
				<input type=\"submit\" name=\"mainmenu\" value=\"Main Screen\" onClick=\"return confirm('Are you sure?')\" />
			</center>
			</td>
		</tr>
	</table>";

}
?>
	</div>
</form>
</body>
</html>
<?php
} //end updating a map 
?>