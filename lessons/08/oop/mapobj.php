<?php
/**************
* File: mapobj.php
* Purpose: map class
**************/

class mapobj
{

	var $id;
	var $name;
	var $width;
	var $height;
	var $level; //current level data has been loaded for
	var $depth; //maximum depth of the map

	var $table;
	var $where;
	var $database;

	//all the coordinates on this map
	var $coordinates;
   
	/**************
	* Default constructor
	**************/
	function __construct($id, $level)
	{
		global $_SESSION;
	  
		if (!$id || !is_numeric($id)) {
			return null;
		}
		
	  if ($_SESSION['debug']) {
			echo "Creating new map object #$id - level $level.<br>";
		}
		
    $this->id = $id;
	  $this->level = $level;
	  $this->database = &$_SESSION['database'];
	  
		$this->table = "map"; 
		$this->where = "WHERE id='$this->id'";

		$this->width = width($this->id, $this->level);
	  $this->height = height($this->id, $this->level);
	  $this->name = $this->database->single("name", $this->table, $this->where);
	  $this->depth = $this->database->single("depth", $this->table, $this->where);

      //load all the coordinates for this map level
	  for ($i = 0; $i <= $this->height; $i++) {
			for ($j = 0; $j <= $this->width; $j++) {
				$this->coordinates[$i][$j][$this->level] = new coordinateobj($i, $j, $this->level, $this->id); 
			}
		}

   } //end default constructor
   
	/**************
	* Return the value of the map at this coordinate
	**************/
	function value($x, $y, $z)
	{
		if (isset($this->coordinates[$x][$y][$z])) {
			return $this->coordinates[$x][$y][$z]->value;
		}
	}	

} //end the map class