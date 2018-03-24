<?php
/**************
* File: charactertypeobj.php
* Purpose: character types
**************/

class charactertype
{

    var $id;
    var $name;
    var $minhealth;
    var $maxhealth;
    var $minspeed;
    var $maxspeed;
    var $minintelligence;
    var $maxintelligence;
    var $minstrength;
    var $maxstrength;
    var $minagility;
    var $maxagility;
    var $minmagic;
    var $maxmagic;
	var $database;

   //these values are for our database queries
   var $table;
   var $where;
   
  /**************
   * Default constructor, load character type
   **************/
   function charactertype($id)
   {
		//we need to get access to the database class
		global $_SESSION;
		$this->database = $_SESSION['database'];
	
      if (!$id || !is_Numeric($id)) //don't try to do any of this if there's no id number
         return null;
		 
	  if ($_SESSION['debug'])
		echo "Creating new character type object #$id.<br>";

      $this->id = $id;
      $this->table = "character_types"; //the name of the table we're using in our database when we
                                //run any queries on members
      $this->where = "WHERE id='$this->id'"; //we always want records from members with this ID

      //now we use the database class we made to pull in all the information about this member
      $this->name = $this->database->single("name", $this->table, $this->where);
      $this->minhealth = $this->database->single("minhealth", $this->table, $this->where);
      $this->maxhealth = $this->database->single("maxhealth", $this->table, $this->where);
      $this->minspeed = $this->database->single("minspeed", $this->table, $this->where);
      $this->maxspeed = $this->database->single("maxspeed", $this->table, $this->where);
      $this->minintelligence = $this->database->single("minintelligence", $this->table, $this->where);
      $this->maxintelligence = $this->database->single("maxintelligence", $this->table, $this->where);
      $this->minstrength = $this->database->single("minstrength", $this->table, $this->where);
      $this->maxstrength = $this->database->single("maxstrength", $this->table, $this->where);
      $this->minagility = $this->database->single("minagility", $this->table, $this->where);
      $this->maxagility = $this->database->single("maxagility", $this->table, $this->where);
      $this->minmagic = $this->database->single("minmagic", $this->table, $this->where);
      $this->maxmagic = $this->database->single("maxmagic", $this->table, $this->where);

   } //end default constructor

} //end the character type class
?>