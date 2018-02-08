<?php
/**************
* File: Mapeditor.php (Version 2.0 with Database)
* Creates map files quickly and efficently
* for Pits of Doom
**************/
session_start();
include('mapsymbols.php'); //all the symbols
include('mysqlobj.php'); //make sure we have the database class
include('functions.php'); //all of our map functions

//creating a new map file
if ($_POST['export']) {	

  // your database connection information
  $host = "localhost"; //usually localhost or 127.0.0.1
  $username = "root"; //your database username
  $password = ""; //your database password
  $usedatabase = "lesson6"; //the name of the database

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
          "'$i','$j', '" . $_SESSION['level'] . "','" . $_POST[$i . "_" . $j]  . "', '$id'"
        );
      }
    }
	}
	
	$error = "Map changes saved!";
	
	unset($_SESSION);
	unset($_POST);
}

if ($_POST['reset']) {
	unset($_SESSION);
}

//start creating our map, setup our map variables
if ($_POST['startmap']) {
	if (!$_POST['width'] || $_POST['width'] <= 0) {
		$error = "This map must have a valid width.";
  } else {
		$_SESSION['width'] = $_POST['width'];
  }
  
	if (!$_POST['height'] || $_POST['height'] <= 0) {
		$error = "This map must have a valid height.";
  } else {
		$_SESSION['height'] = $_POST['height'];
  }
		
	if (!$_POST['name']) {
		$_SESSION['name'] = "Default Map";
  } else {
		$_SESSION['name'] = $_POST['name'];
  }
  
	if (!$_POST['level']) {
		$_SESSION['level'] = 1;
  } else {
		$_SESSION['level'] = $_POST['level'];
  }
} 

// if we're not trying to export it, show the map editor
if (!$_POST['export']) {
?>
<html>
  <head>
    <title>Pits of Doom - Map Editor</title>
    <link href="editorstyle.css" rel="stylesheet" type="text/css">
    <script language="javascript">
      // this changes the background color of the cell
      function setColor(object) {
        object.style.backgroundColor = object.options[object.selectedIndex].style.backgroundColor;
        object.style.color = object.options[object.selectedIndex].style.color;
      }

      // this resets the colors
      function resetColors(object) {
        var i;

        for (i = 0; i < (object.length-2); i++) {
            setColor(object[i]);
        }
      }
    </script>
  </head>
  <body
    <?php
    if ($_SESSION['width'] && $_SESSION['height']) {
      echo "onLoad=\"resetColors(form)\"";
    }
    ?>>
    <form action="#" method="post" name="form">
    <?php
    //first they have to select the map information
    if (!$_SESSION['width'] && !$_SESSION['height']) {
    ?>
      <center>
      <b>Map Settings</b>
      <?php if ($error) { echo "<br/>Alert: $error<br/><br/>"; } ?>
      </center>
      Name: <input type="text" name="name" /><br/><br/>
      Level: <input type="text" name="level" /><br/><br/>
      Width: <input type="text" name="width" /><br/><br/>
      Height: <input type="text" name="height" />
      <center><input type="submit" name="startmap" value="Start Editor" /></center>
    <?php
    } else { //display the map editor

      echo '<p align="center">
        <strong>Creating Map For ' . $_SESSION['name'] . ' Level ' . $_SESSION['level'] . '</strong><br/>
        Dimensions: ' . $_SESSION['width'] . ' X ' . $_SESSION['height'] . '</p><br/><br/>
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
          echo "<td><select name=\"" . $i . "_" . $j . "\" onChange=\"setColor(this);\">";

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

            if ($_POST[$i . "_" . $j] == $mapsymbols[$r]) {
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
            
          } //end dropdown for loop

          echo "</select></td>";
          
        } //end width for loop

        echo "</tr>";

      } //end height for loop

      echo "</table>
      <p align=\"center\">
        <input type=\"submit\" name=\"export\" value=\"Update Database\"/>
        <input type=\"submit\" name=\"reset\" value=\"Start A New Map\" onClick=\"return confirm('Are you sure?')\" />
      </p>";
    } //end display map editor else
    ?>
    </form>
  </body>
</html>
<?php
} //end exporting a file