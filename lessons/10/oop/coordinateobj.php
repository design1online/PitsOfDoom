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
	var $tile_x;
	var $tile_y;
	
   var $table;
   var $where;
   var $database;
   
  /**************
   * Default constructor
   **************/
   function coordinateobj($x, $y, $z, $mapid)
   {
	  global $_SESSION;
	  
	  //make sure we have accurate values for x, y, z, and mapid
      if ((!is_Numeric($x)) || 
		  (!is_Numeric($y)) || 
		  (!is_Numeric($x)) || 
		  (!$mapid || !is_Numeric($mapid)))
         return null;
		 
	  if ($_SESSION['debug'])
		echo "Creating new coordinate object $x $y $z $mapid.<br>";

	  $this->database = &$_SESSION['database'];
	  
      $this->table = "mapdata"; 
      $this->where = "WHERE x='$x' AND y='$y' AND z='$z' AND mapid='$mapid'";
	  $this->x = $x;
	  $this->y = $y;
	  $this->z = $z;
	  $this->mapid = $mapid;
	 
	  $this->id = $this->database->single("id", $this->table, $this->where);
	  $this->value = $this->database->single("value", $this->table, $this->where);
	  $this->tile_x = $this->database->single("tile_x", $this->table, $this->where);
	  $this->tile_y = $this->database->single("tile_y", $this->table, $this->where);

   } //end default constructor

} //end the coordinate class
?>