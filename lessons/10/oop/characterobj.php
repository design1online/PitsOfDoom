<?php
/****************
* File: characterobj.php
* Date: 3.9.2009
* Author: jade@design1online.com
* Purpose: character class
*****************/

class character
{
	//class variables
    var $id;
    var $memberid;
    var $firstname;
    var $lastname;
    var $type;
	var $typename;
    var $monster;
    var $money;
    var $health;
    var $speed;
    var $intelligence;
    var $strength;
    var $agility;
    var $magic;
    var $fightlvl;
    var $magiclvl;
    var $magicamount;
    var $weapon;
    var $spell;
    var $armor;
    var $shield;
    var $mapid;
    var $x;
    var $y;
    var $z; //level they're on, 1 is surface level
    var $active;
    var $attacking;
	var $database;

   //these values are for our database queries
   var $table;
   var $where;
   
   /**************
   * Default constructor, load this characters's information
   **************/
   function character($id)
   {
	  //we need to get access to the database class
	  global $_SESSION;
	  $this->database = $_SESSION['database'];
	
      if (!$id || !is_Numeric($id)) //don't try to do any of this if there's no id number
         return null;
		 
	  if ($_SESSION['debug'])
		echo "Creating new character object s#$id.<br>";

      $this->id = $id;
      $this->table = "characters";
      $this->where = "WHERE id='$this->id'";

      //now we use the database class we made to pull in all the information about this member
      $this->id = $this->database->single("id", $this->table, $this->where);
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
      $this->magiclvl = $this->database->single("magiclvl", $this->table, $this->where);
	  $this->magic = $this->database->single("magic", $this->table, $this->where);
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

   } //end default constructor

	/**************
	* Purpose: Move around the map (does this look familiar?!?)
	* Precondition: the direction they're moving
	* Postcondition: new  ocation or error message
	**************/
   function move($direction)
   {
	
	//we need to get the character's current location
	$newx = $this->x;
	$newy = $this->y;
	
	switch($direction) //we want to change what we're checking
					  //depending on the direction the character is moving
	{
		case "right": $newy++; //add one to the y value
			break;
		case "left": $newy--; //subtract one from the y value
			break;
		case "back": $newx++; //add one to x vaue
			break;
		case "forward": $newx--; //subtract one from the x value
			break;
	}
	
	//they are on a ladder
	if (getValue($this->x, $this->y, $this->mapid, $this->z) == "L")
	{
		//if they hit the up direction, move them up a level (if not at highest level)
		if ($direction == "forward" && $this->z != 1)
		{
			$message = successMsg("You moved up the ladder");
			
			//move them up a level
			$this->z = $this->z - 1;
	
			//set the character's starting position in the NEW map
			$this->x = startPositionX($this->mapid, $this->z);
			$this->y = startPositionY($this->mapid, $this->z);
			
			//now we save their position to the database so if they log off
			//or leave the game the character is still in this position when
			//they come back and play later
			$this->database->update($this->table, "x=$this->x, y=$this->y, z=$this->z", $this->where);
		
		}
		//if they hit the down direction, move them down a level (if not at lowest level)
		else if ($direction == "back" && $this->z != 5)
		{
			$message = successMsg("You moved down the ladder");
			
			//move them down a level
			$this->z = $this->z + 1;
	
			//set the character's starting position in the NEW map
			$this->x = startPositionX($this->mapid, $this->z);
			$this->y = startPositionY($this->mapid, $this->z);
					
			//update their position
			$this->database->update($this->table, "x=$this->x, y=$this->y, z=$this->z", $this->where);
		}
		
		else
		{
				//let them move some other direction
				if (getValue($newx, $newy, $this->mapid, $this->z) == "T")
				{
					//the treasure is in this direction
					$message = successMsg("You found the treasure");
					
					$this->x = $newx;
					$this->y = $newy;
					
					//update their position
					$this->database->update($this->table, "x=$this->x, y=$this->y", $this->where);
					
				}

				else if (getValue($newx, $newy, $this->mapid, $this->z) == "W")
				{
					//don't update their position, they can't move here
					$message = errorMsg("You hit a wall");
				}
					
				else if (getValue($newx, $newy, $this->mapid, $this->z) == "E")
				{
					//empty space, move them to this new location
					$message = successMsg("You moved $direction one space");
					$this->x = $newx;
					$this->y = $newy;
					
					//update their position
					$this->database->update($this->table, "x=$this->x, y=$this->y", $this->where);
				}
				
				else if (getValue($newx, $newy, $this->mapid, $this->z) == "S")
				{
					//starting location, move them to this new location
					$message = successMsg("You moved $direction one space");
					$this->x = $newx;
					$this->y = $newy;
					
					//update their position
					$this->database->update($this->table, "x=$this->x, y=$this->y", $this->where);
				}
					
				else if (getValue($newx, $newy, $this->mapid, $this->z) == "X")
				{
					//they found a pit
					$message = errorMsg("You fell into a pit and dropped down a level");
						
					//move them down a level
					$this->z = $this->z + 1;
				
					$this->x = startPositionX($this->mapid, $this->z);
					$this->y = startPositionY($this->mapid, $this->z);
					
					//update their position
					$this->database->update($this->table, "x=$this->x, y=$this->y, z=$this->z", $this->where);
				}
				
				else if (getValue($newx, $newy, $this->mapid, $this->z) == "L")
				{
					//they found a ladder
					$message = questionMsg("You found a ladder. Move up or down");
						
					//move them to the position on the map that has the ladder
					//but don't change which level they're on
					$this->x = $newx;
					$this->y = $newy;
					
					//update their position
					$this->database->update($this->table, "x=$this->x, y=$this->y", $this->where);
				}
		}
	}
	
	else if (getValue($newx, $newy, $this->mapid, $this->z) == "T")
	{
		//the treasure is in this direction
		$message = successMsg("You found the treasure");
		
		$this->x = $newx;
		$this->y = $newy;
					
		//update their position
		$this->database->update($this->table, "x=$this->x, y=$this->y", $this->where);
		
	}

	else if (getValue($newx, $newy, $this->mapid, $this->z) == "W")
	{
		//don't update their position, they can't move here
		$message = errorMsg("You hit a wall");
	}
		
	else if (getValue($newx, $newy, $this->mapid, $this->z)== "E")
	{
		//empty space, move them to this new location
		$message = successMsg("You moved $direction one space");
		
		$this->x = $newx;
		$this->y = $newy;
					
		//update their position
		$this->database->update($this->table, "x=$this->x, y=$this->y", $this->where);
	}
	
	else if (getValue($newx, $newy, $this->mapid, $this->z) == "S")
	{
		//starting location, move them to this new location
		$message = successMsg("You moved $direction one space");
		
		$this->x = $newx;
		$this->y = $newy;
					
		//update their position
		$this->database->update($this->table, "x=$this->x, y=$this->y", $this->where);
	}
		
	else if (getValue($newx, $newy, $this->mapid, $this->z) == "X")
	{
		//they found a pit
		$message = errorMsg("You fell into a pit and dropped down a level");
			
		//move them down a level
		$this->z = $this->z + 1;
	
		//set the character's starting position in the NEW map
		$this->x = startPositionX($this->mapid, $this->z);
		$this->y = startPositionY($this->mapid, $this->z);
					
		//update their position
		$this->database->update($this->table, "x=$this->x, y=$this->y, z=$this->z", $this->where);
	}
	
	else if (getValue($newx, $newy, $this->mapid, $this->z) == "L")
	{
		//they found a ladder
		$message = questionMsg("You found a ladder. Move up or down");
			
		//move them to the position on the map that has the ladder
		//but don't change which level they're on
		$this->x = $newx;
		$this->y = $newy;
					
		//update their position
		$this->database->update($this->table, "x=$this->x, y=$this->y", $this->where);
	}
	
	//display the message to the member
	return $message;
  }   
  
	/**************
	* Purpose: move the character to a random map location
	* Precondition: none
	* Postcondition: character has been moved
	**************/
	function randomLocation()
	{
		if (!$this->mapid)
			$this->mapid = 1; //set them to the default map
			
		if (!$this->z)
			$this->z = rand(1, depth($this->mapid)); //cannot be lower than the depth of the map
			
		//get the limits of the map they're on
		$mapheight = height($this->mapid, $this->z);
		$mapwidth = width($this->mapid, $this->z);
		
		$this->x = 0;
		$this->y = 0;
		
		//try to place them randomly around the map, make sure it's an empty spot
		while (getValue($this->x, $this->y, $this->mapid, $this->z) != "E") 
		{
			$this->x = rand(0, $mapwidth);
			$this->y = rand(0, $mapheight);
		}
	}

	/**************
	* Purpose: attack the monster at this location
	* Precondition: the character is still alive
	* Postcondition: the monster has been attacked
	**************/
	function attack()
	{
		//make sure there is a monster at this location
		if (hasMonster($this->x, $this->y, $this->z, $this->mapid))
		{
			$monster_dead = false;
			$character_dead = false;

			//load the monster
			$monster = new monster(hasMonster($this->x, $this->y, $this->z, $this->mapid));

			//calculate the monster's attack
			$monster_attack = $monster->calculateAttack();

			//calculate this character's attack
			$character_attack = $this->calculateAttack();

			//randomly select which one hits first
			$firstblow = rand(0, 1);

			if ($firstblow) //the character attacks first
			{
				$monster->setHealth($monster->health -= $character_attack);

				//if the monster is dead
				if ($monster->health <= 0)
				{
					//remove the monster from the game
					$monster_dead = true;
					$monster->death();
				}
			}
			else //the monster attacks first
			{
				$this->health -= $monster_attack;

				//if the character has died
				if ($this->health <= 0)
				{
					//restore their health for right now, later we can change this
					$this->health = 100;
					$character_dead = true;

					//move the character to a new level and x,y coordinates
					$this->x = startPositionX($this->mapid, $this->z);
					$this->y = startPositionY($this->mapid, $this->y);
					$this->database->update($this->table, "x=$this->x, y=$this->y", $this->where);

					//eventually we'll remove the character as well but for right now they can stay
					return "You took $monster_attack damage and died. You have been restored to full health and re-spawned at a new location.";
				}
				
			}

			//both the character and the monster are still alive, now we land the second blow
			if (!$character_dead && !$monster_dead)
			{
				if ($firstblow) //the character struck first now it's the monsters turn
				{
					$this->health -= $monster_attack;

					//if the character has died
					if ($this->health <= 0)
					{
					
						//restore their health for right now, later we can change this
						$character_dead = true;
						$this->health = 100;

						//move the character to a new level and x,y coordinates
						$this->x = startPositionX($this->mapid, $this->z);
						$this->y = startPositionY($this->mapid, $this->y);
						$this->database->update($this->table, "x=$this->x, y=$this->y", $this->where);

						//eventually we'll remove the character as well but for right now they can stay
						return "You took $monster_attack damage and died. You have been restored to full health and re-spawned at a new location.";
					}

				}
				else //the monster struck first, now it's the character's turn
				{
					$monster->setHealth($monster->health -= $character_attack);

					//if the monster is dead
					if ($monster->health <= 0)
					{
						//remove the monster from the game
						$monster_dead = true;
						$monster->death();

					}
				}
			}

			//if the character is dead put their health back to 100 for right now
			//later on we'll make them create a new character if one dies
			if ($character_dead)
			{
				$this->health = 100;

				//move the character to a new level and x,y coordinates
				$this->x = startPositionX($this->mapid, $this->z);
				$this->y = startPositionY($this->mapid, $this->y);
				$this->database->update($this->table, "x=$this->x, y=$this->y", $this->where);

				return "You took $monster_attack damage and died. You have been restored to full health and re-spawned at a new location.";
				
			}
			else
			{
				if ($monster_dead)
					return successMsg("You killed the monster");
				else
					return successMsg("You caused  " . number_format($character_attack) . " damage");
			}
			
			//update the character's health
			$this->database->update($this->table, "health=$this->health", $this->where);
			
		}
		else
			return errorMsg("You flail your weapon about but there's no monster to attack");
	}

	/**************
	* Purpose: generate a number for how much attack damage this monster can cause
	* Precondition: none
	* Postcondition: returns attack damage value
	**************/
	function calculateAttack()
	{
		if ($this->fightlvl > 1)
			return rand(($this->speed + $this->intelligence + $this->strength + $this->agility)/($this->fightlvl/.5), ($this->speed + $this->intelligence + $this->strength + $this->agility)/($this->fightlvl/1.5));
		else
			return rand(($this->speed + $this->intelligence + $this->strength + $this->agility)/4, ($this->speed + $this->intelligence + $this->strength + $this->agility)/5);

	}

} //end the character class
?>