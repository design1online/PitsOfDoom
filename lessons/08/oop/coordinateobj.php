<?php
/**************
* File: coordinateobj.php
* Purpose: coordinate class
**************/

class coordinateobj
{

	var $id;
	var $mapid;
	var $x;
	var $y;
	var $z;
	var $value;
	var $itemid;
	var $maplink; //map ID they will be taken to
	var $maplink_level; //map level on that map ID

	var $table;
	var $where;
	var $database;
   
	/**************
	* Default constructor
	**************/
	function __construct($x, $y, $z, $mapid)
	{
		global $_SESSION;
	  
	  //make sure we have accurate values for x, y, z, and mapid
    if ((!is_numeric($x)) || 
		  (!is_numeric($y)) || 
		  (!is_numeric($x)) || 
		  (!$mapid || !is_numeric($mapid))) {
         return null;
		}
		 
	  if ($_SESSION['debug']) {
			echo "Creating new coordinate object $x $y $z $mapid.<br>";
		}

	  $this->database = &$_SESSION['database'];
	  
    $this->table = "mapdata"; 
    $this->where = "WHERE x='$x' and y='$y' and z='$z' and mapid='$mapid'";
	  $this->x = $x;
	  $this->y = $y;
	  $this->z = $z;
	  $this->mapid = $mapid;
	 
	  $this->id = $this->database->single("id", $this->table, $this->where);
	  $this->value = $this->database->single("value", $this->table, $this->where);
	  //$this->itemid = $this->database->single("itemid", $this->table, $this->where);
	  //$this->maplink = $this->database->single("maplink", $this->table, $this->where);
	  //$this->maplink_level = $this->database->single("maplink_level", $this->table, $this->where);

   } //end default constructor

} //end the coordinate class