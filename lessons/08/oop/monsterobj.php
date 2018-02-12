<?php
/****************
* File: monsterobj.php
* Date: 3.10.2009
* Author: jade@design1online.com
* Purpose: monsters in the game
*****************/

/**************
* A monster is really just a special character
* so we're going to use all the elements of the
* of the character class and then add a few of
* our own new functions to the mix
**************/
class monster extends character
{
	
	/**************
	* Purpose: we've setup this function so we can pass in values if we want
	* 	to make a specific type of monster at a certain location, otherwise
	* 	create one with random specs
	* Precondition: id of the monster or type, mapid, and xyz location of monster we
	*	are trying to create
	* Postconditon: monster object loaded, must use create()  to add the new monster
	*	to the database if it's being generated
	**************/
	function __construct($id = null, $type = null, $mapid = null, $x = null, $y = null, $z = null)
	{
		global $_SESSION;
		$this->database = $_SESSION['database'];
		
		if ($id) {
			$this->id = $id;
			$this->table = "characters";
			$this->where = "WHERE id=$this->id";
			$this->memberid = $this->database->single("memberid", $this->table, $this->where);
			$this->firstname = $this->database->single("firstname", $this->table, $this->where);
			$this->lastname = $this->database->single("lastname", $this->table, $this->where);
			$this->type = $this->database->single("type", $this->table, $this->where);
			$this->monster = $this->database->single("monster", $this->table, $this->where);
			$this->money = $this->database->single("money", $this->table, $this->where);
			$this->health = $this->database->single("health", $this->table, $this->where);
			$this->speed = $this->database->single("speed", $this->table, $this->where);
			$this->intelligence = $this->database->single("intelligence", $this->table, $this->where);
			$this->strength = $this->database->single("strength", $this->table, $this->where);
			$this->agility = $this->database->single("agility", $this->table, $this->where);
			$this->fightlvl = $this->database->single("fightlvl", $this->table, $this->where);
			$this->magic = $this->database->single("magic", $this->table, $this->where);
			$this->magiclvl = $this->database->single("magiclvl", $this->table, $this->where);
			$this->magicamount = $this->database->single("magicamount", $this->table, $this->where);
			$this->weapon = $this->database->single("weapon", $this->table, $this->where);
			$this->spell = $this->database->single("spell", $this->table, $this->where);
			$this->armor = $this->database->single("armor", $this->table, $this->where);
			$this->shield = $this->database->single("shield", $this->table, $this->where);
			$this->mapid = $this->database->single("mapid", $this->table, $this->where);
			$this->x = $this->database->single("x", $this->table, $this->where);
			$this->y = $this->database->single("y", $this->table, $this->where);
			$this->z = $this->database->single("z", $this->table, $this->where);
			$this->active = $this->database->single("active", $this->table, $this->where);
			$this->attacking = $this->database->single("attacking", $this->table, $this->where);
			$this->typename = $this->database->single("name", "character_types", "WHERE id='$this->type'");
		}
		
		//no type passed in or in the database
		if (!$type && !$this->type) {
			$this->type = $this->database->single("id", "character_types", "ORDER BY RAND() LIMIT 1");
			$this->typename = $this->database->single("name", "character_types", "WHERE id='$this->type'");
		} else {
			$this->type = $type;
		}

		//no map passed in or in the database
		if (!$mapid && !$this->mapid) {
			$this->mapid = 1; //set them to the default map
		} else {
			$this->mapid = $mapid;
		}
			
		//no location passed in or in the database
		if ((!$x && !$this->x) || (!$y && !$this->y) || (!$z && !$this->z)) { //no location, give them one	
			$this->randomLocation();
		} else {
			$this->x = $x;
			$this->y = $y;
			$this->z = $z;
		}
			
		//if this is a new monster we need to give it random statistics based on the character type
		if (!$this->speed) {
			$this->speed = characterRandAttribute($this->type, "speed");
		}
			
		if (!$this->intelligence) {
			$this->intelligence = characterRandAttribute($this->type, "intelligence");
		}
			
		if (!$this->strength) {
			$this->strength = characterRandAttribute($this->type, "strength");
		}
			
		if (!$this->agility) {
			$this->agility = characterRandAttribute($this->type, "agility");
		}
			
		if (!$this->magic) {
			$this->magic = characterRandAttribute($this->type, "magic");
		}
			
		if (!$this->health) {
			$this->health = characterRandAttribute($this->type, "health");
		}

		//give them a fighting level based on the depth of the map they're on
		if (!$this->fightlvl) {
			$low = rand(1, $this->z * 5);
			$high = rand($low, $low * 2);
			$this->fightlvl = rand($low, $high);
		}
		
		//give them a magic level based on the fight level and magic skill
		if (!$this->magiclvl) {
			$low = rand(1, ($this->magic/3));
			$high = rand($low, ($this->fightlvl/2) * ($this->magic/5));
			$this->magiclvl = rand($low, $high);
		}

		//set other settings
		$this->monster = 1; //flag this as a monster, set its name to the character type
		$this->active = 1; //flag as being active in the game
		
		if (!$this->firstname) {
			$this->firstname = $this->database->single("name", "character_types", "WHERE id='" . $this->type . "'");
		}
		
		if (!$this->money) {
			$this->money = rand($this->fightlvl-1, $this->fightlvl * 2);
		}
			
		if (!$this->magicamount) {
			$this->magicamount = rand(0, $this->fightlvl * 50);
		}
		
		//we'll take care of these once we add in items
		$this->weapon; //give them a weapon
		$this->armor; //give them some armor
		$this->spell; //give them an active spell
		$this->shield; //give them some kind of shield
	}
	
	/**************
	* Purpose: save the monster we've generated to the database
	* Precondition: monster statistics have already been set, monster
	*	object is being created, not loaded
	* Postcondition: monster has been added to the database
	**************/
	function create()
	{
		if (!$this->id) {
			$this->id = $this->database->insert(
				$this->table,
				"type, mapid, x, y, z, speed, intelligence,
				strength, agility, magic, health, fightlvl, magiclvl, monster, active, firstname, money,
				magicamount, weapon, armor, spell, shield",
				"'$this->type', '$this->mapid', 
				'$this->x', '$this->y', '$this->z', '$this->speed', '$this->intelligence', '$this->strength', 
				'$this->agility', '$this->magic', '$this->health','$this->fightlvl', '$this->magiclvl', 
				'$this->monster', '$this->active', '$this->firstname', '$this->money', '$this->magicamount', 
				'$this->weapon', '$this->armor', '$this->spell', '$this->shield'");
		}
	}
}

/**************
* Purpose: generate more monsters for the game
* Precondition: number of monsters to generate
* Postcondition: monsters created and scattered around the game
**************/
function generateMonsters($number)
{
	for ($i = 1; $i <= $number; $i++) {
		$monster = new monster(0);
		$monster->create();
	}
	
	return successMsg("$number monsters have been generated");
}
