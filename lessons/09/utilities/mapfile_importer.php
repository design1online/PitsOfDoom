<?php
include('mysqlobj.php');
include('functions_v5.php');


//REMEMBER!! your maps must be in the directory specified by the loadmap function
//Trying to load map into the database
if ($_POST['submit']) 
{
	//setup our password information
	$host = "localhost";
	$username = "design1o_Jade2";
	$password = "788design1";
	$database = "design1o_pitsofdoom";

	//we have to start our database connection
	$database = new database($host, $username, $password, $database);
	
	if (!$_POST['file'] || $_POST['level'])
		$error = "You must enter a file name and specify a level";
	
	$map = loadMap($_POST['file'], $_POST['level']);
	
	//select the ID of the map with this name
	$result = $database->select("id", "map", "WHERE name='" . $_POST['file'] . "'");

	if (!$result['id']) //map file name wasn't found in the database
		$id = $database->insert("map", "name", "'" .  $_POST['name'] . "'"); //add a map with this file name
	else
		$id = $result['id']; //we found the right id with the database select
	
	//now we update or add all the data from the map
	for ($i = 0; $i <= height($map); $i++)
		for ($j = 0; $j <=  width($map); $j++)
		{
			//see if this row is already in the database
			$result = $database->select("id", "mapdata", "WHERE mapid=$id AND x=$i and y=$j and z=" . $_POST['level'] . "");
			
			//we found this row already in the database, so just update it
			if ($result['id'])
				$database->update("mapdata", "value='" . $map[$i][$j] . "'", "WHERE mapid=$id and x=$i and y=$j and z=" . $_POST['level'] . "");
			else //we haven't found a row like this
				$database->insert("mapdata", "x, y, z, value, mapid", "'$i', '$j', '" . $_POST['level'] . "', '" . $map[$i][$j] . "', '$id'");
		}
	
		$error = "Map has been added or updated!";
}

?>
<html>
<head>
<title>Pits of Doom -- Map File Database Importer</title>
</head>
<body>
<form action="#" method="post">
<center><b>Map File Database Importer</b><br/><br/>
<?php if ($error) echo $error . "<br/>"; ?></center>

Map Name: <input type="text" name="file" value="<?php echo $_POST['file']; ?>" /><br/><br/>
Level: <input type="text" name="level" value="<?php echo $_POST['level']; ?>" /><br/>
<center><input type="submit" name="submit" value="Import File To Database"/></center>
</form>
</body>
</html>