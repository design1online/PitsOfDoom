<?php
/****************
* File: mapeditor.php
* Date: 3.10.2009
* Author: jade@design1online.com
* Purpose: create maps in the game
*****************/
session_start();
include('inc/mapsymbols.php'); //all the symbols
include('../oop/mysqlobj.php'); //make sure we have the database class
include('../inc/functions.php'); //all of our map functions

//creating a new map file
if (isset($_POST['export'])) {
	
	//connect to the database
	$database = new database($host, $username, $password, $usedatabase);
	
	$map = loadMap($_SESSION['name'], $_SESSION['level']);
	
	//select the ID of the map with this name
	$result = $database->select("id", "map", "WHERE name='" . $_SESSION['name'] . "'");

	if (!$result['id']) { //map file name wasn't found in the database
		$id = $database->insert("map", "name", "'" .  $_SESSION['name'] . "'"); //add a map with this file name
	} else {
		$id = $result['id']; //we found the right id with the database select
	}
	
	for ($i = 0; $i <= $_SESSION['height']; $i++) {
		for ($j = 0; $j <= $_SESSION['width']; $j++) {
			//if this map already exists then update this row
			//otherwise insert this as a new row
			
			//see if this row is already in the database
			$result = $database->select("id", "mapdata", "WHERE mapid=$id AND x=$i and y=$j and z=" . $_SESSION['level'] . "");
				
			//we found this row already in the database, so just update it
			if ($result['id']) {
				$database->update(
					"mapdata",
					"value='" .  $_POST[$i . "_" . $j]  . "'",
					"WHERE mapid=$id and x=$i and y=$j and z=" . $_SESSION['level'] . ""
				);
			} else { //we haven't found a row like this
				$database->insert(
					"mapdata",
					"x, y, z, value, mapid",
					"'$i', '$j', '" . $_SESSION['level'] . "', '" . $_POST[$i . "_" . $j]  . "', '$id'"
				);
			}
		}
	}
	
	$error = "Map changes saved!";
	
	unset($_SESSION);
	unset($_POST);
}

if (isset($_POST['reset'])) {
	unset($_SESSION);
}

//start creating our map, setup our map variables
if (isset($_POST['startmap'])) {
	if (!isset($_POST['width']) || $_POST['width'] <= 0) {
		$error = "This map must have a valid width.";
	} else {
		$_SESSION['width'] = $_POST['width'];
	}
		
	if (!isset($_POST['height']) || $_POST['height'] <= 0) {
		$error = "This map must have a valid height.";
	} else {
		$_SESSION['height'] = $_POST['height'];
	}
		
	if (!isset($_POST['name'])) {
		$_SESSION['name'] = "Default Map";
	} else {
		$_SESSION['name'] = $_POST['name'];
	}
		
	if (!isset($_POST['level'])) {
		$_SESSION['level'] = 1;
	} else {
		$_SESSION['level'] = $_POST['level'];
	}
} 

//we're not viewing the map editor yet
if (!isset($_POST['export'])) {
?>
<html>
	<head>
		<title><?php echo $_sitetitle; ?></title>
		<link rel="stylesheet" type="text/css" href="../css/default.css" />
		<script type="text/javascript" src="inc/utilities.js"></script>
	</head>
	<body
	<?php
		if (isset($_SESSION['width']) && isset($_SESSION['height'])) {
			echo "onLoad=\"resetColors(form)\"";
		}
	?>>
		<form action="#" method="post" name="form">
			<div id="main">
				<div id="mainbanner"></div>
				<div id="content">
				<h2>Pits of Doom - Map Editor</h2>
				<?php
					include('inc/links.php');

					//first they have to select the map information
					if (!isset($_SESSION['width']) && !isset($_SESSION['height'])) {
				?>
				<p align="center">
					<?php if ($error) echo "<br/>Alert: $error<br/><br/>"; ?>
				</p>
				<blockquote>
					<p>
						Name: <input type="text" name="name" />
					</p>
					<p>
						Level: <input type="text" name="level" />
					</p>
					<p>
						Width: <input type="text" name="width" />
					</p>
					<p>
						Height: <input type="text" name="height" />
					</p>
					<p align="center">
						<input type="submit" name="startmap" value="Start Editor" />
					</p>
				</blockquote>
				<?php
				} else { //display the map editor
					echo '<p>&nbsp;</p>
					<p align="center">
						<strong>Creating Map For ' . $_SESSION['name'] . ' Level ' . $_SESSION['level'] . '</strong><br/>
						Dimensions: ' . $_SESSION['width'] . ' X ' . $_SESSION['height'] . '
					</p>
					<p align="center">
						<table cellpadding="2" cellspacing="2">
							<tr>
								<td></td>';

					//does this look familiar?!?
					for ($i = 0; $i < $_SESSION['width']; $i++) {
						echo "<td>$i</td>";
					}

					echo "</tr>";

					for ($i = 0; $i < $_SESSION['height']; $i++) {
						echo "<tr><td>$i</td>";

						for ($j = 0; $j < $_SESSION['width']; $j++) {
							echo "<td><select name=\"" . $i . "_" . $j . "\" onChange=\"setColor(this);\"";

							echo ">";

							//show all the options in the drop down box
							for ($r = 0; $r < sizeof($mapsymbols); $r++) {
								echo "<option value=\"" . $mapsymbols[$r] . "\"";

								//we colors that makes them easier to see
								if ($mapsymbols[$r] == "W") {
									echo " style=\"background-color: #CCC; color: #FFF;\"";
								}
								
								if ($mapsymbols[$r] == "E") {
									echo " style=\"background-color: #FFF; color: #000;\"";
								}
								
								if ($mapsymbols[$r] == "X") {
									echo " style=\"background-color: #000; color: #FFF;\"";
								}
								
								if ($mapsymbols[$r] == "L") {
									echo " style=\"background-color: #FFFF00; color: #000;\"";
								}
								
								if ($mapsymbols[$r] == "S") {
									echo " style=\"background-color: #0000FF; color: #FFF;\"";
								}
								
								if ($mapsymbols[$r] == "T") {
									echo " style=\"background-color: #009900; color: #FFF;\"";
								}
								
								if (isset($_POST[$i . "_" . $j]) == $mapsymbols[$r]) {
									echo " selected";
								} else if (!$_POST[$i . "_" . $j]) {
										if (($i == 0 && $mapsymbols[$r] == "W") //the top row
										 || ($j == 0 && $mapsymbols[$r] == "W") //down the left side
										 || ($i == $_SESSION['height']-1 && $mapsymbols[$r] == "W") //the bottom row
										 || ($j == $_SESSION['width']-1 && $mapsymbols[$r] == "W") //down the right side
										) {
											echo " selected";
										} else if ((($i != 0 && $mapsymbols[$r] == "E") //not the top row
												&& ($j != 0 && $mapsymbols[$r] == "E")) //not the left side
												&& (($i !=  $_SESSION['height']-1 && $mapsymbols[$r] == "E") //not the bottom row
												&& ($j != $_SESSION['width']-1 && $mapsymbols[$r] == "E")) //not the right side
										) {
											echo " selected";
										}
								}

								echo ">" . $mapsymbols[$r] . "</option>";
							}

							echo "</select></td>";
						}

						echo "</tr>";
					}

					echo '</table>
					<br/>
					<p align="center">
						<input type="submit" name="export" value="Update Database"/>
						<input type="submit" name="reset" value="Start A New Map" onClick="return confirm(\'Are you sure?\')" />
					</p>';
				}
				?>
			</div>
		</form>
	</body>
</html>
<?php
} //end exporting a file