<?php
include('mysqlobj.php');
include('functions.php');

//REMEMBER!! your maps must be in the directory specified by the loadmap function
//Trying to load map into the database
if (isset($_POST['submit'])) {
  
	//database connection info
	$host = "localhost"; //usually localhost or 127.0.0.1
	$username = "root"; // database username
	$password = ""; // database password
	$database = "lesson8"; //database name

	//we have to start our database connection
	$database = new database($host, $username, $password, $database);
	
  //make sure they have a file name and a level
	if (!isset($_POST['file']) || isset($_POST['level'])) {
		$error = "You must enter a file name and specify a level";
  }
	
  //try and load the map with that name and level
	$map = loadMap($_POST['file'], $_POST['level']);
	
	//select the ID of the map with this name
	$result = $database->select("id", "map", "WHERE name='" . $_POST['file'] . "'");

	if (!$result['id']) { //map file name wasn't found in the database
		$id = $database->insert("map", "name", "'" .  $_POST['name'] . "'"); //add a map with this file name
  } else {
		$id = $result['id']; //we found the right id with the database select
  }
	
	//now we update or add all the data from the map
	for ($i = 0; $i <= height($map); $i++) {
		for ($j = 0; $j <=  width($map); $j++) {
			//see if this row is already in the database
			$result = $database->select(
        "id",
        "mapdata",
        "WHERE mapid=$id AND x=$i and y=$j and z=" . $_POST['level'] . ""
      );
			
			//we found this row already in the database, so just update it
			if ($result['id']) {
				$database->update(
          "mapdata",
          "value='" . $map[$i][$j] . "'",
          "WHERE mapid=$id and x=$i and y=$j and z=" . $_POST['level'] . ""
       );
      } else { //we haven't found a row like this
				$database->insert(
          "mapdata",
          "x, y, z, value, mapid",
          "'$i', '$j', '" . $_POST['level'] . "', '" . $map[$i][$j] . "', '$id'"
        );
      }
		}
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
      <p align="center">
        <strong>Map File Database Importer</strong><br/><br/>
        <?php if ($error) echo $error . "<br/>"; ?>
      </p>
      Map Name: <input type="text" name="file" value="<?php echo $_POST['file']; ?>" /><br/><br/>
      Level: <input type="text" name="level" value="<?php echo $_POST['level']; ?>" /><br/>
      <p align="center">
        <input type="submit" name="submit" value="Import File To Database"/>
      </p>
    </form>
  </body>
</html>