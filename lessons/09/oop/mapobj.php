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
	var $tile_width;
	var $tile_height;

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
		$this->tile_width = $this->database->single("tile_width", $this->table, $this->where); 
		$this->tile_height = $this->database->single("tile_height", $this->table, $this->where); 

		//load all the coordinates for this map level
		for($i = 0; $i <= $this->height; $i++) {
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
		return $this->coordinates[$x][$y][$z]->value;
	}
   
	function tile_x($x, $y, $z)
	{
		return $this->coordinates[$x][$y][$z]->tile_x;
	}

	function tile_y($x, $y, $z)
	{
		return $this->coordinates[$x][$y][$z]->tile_y;
	}

	function updateTilesheet($image, $width, $height, $xoffset = 0, $yoffset = 0)
	{
		if (!$image || !$width || !$height) {
			return;
		}

		$this->tile_sheet = mysql_real_escape_string($image);
		$this->tile_width = mysql_real_escape_string($width);
		$this->tile_height = mysql_real_escape_string($height);
		$this->tile_xoffset = mysql_real_escape_string($xoffset);
		$this->tile_yoffset = mysql_real_escape_string($yoffset);

		$this->database->update(
			$this->table, 
			"tile_sheet='{$this->tile_sheet}', 
			tile_width='{$this->tile_width}',
			tile_height='{$this->tile_height}',
			tile_xoffset='{$this->tile_xoffset}',
			tile_yoffset='{$this->tile_yoffset}'",
			$this->where
		);
	}

} //end the map class
