<?php
/**************
* File: memberobj.php
* Purpose: member class
**************/

class member
{
    //these are named exactly the same as in the database
    //to make it easier when we use the $this->database class later
    var $id;
    var $username;
    var $password;
    var $email;
    var $lastlogin;
    var $online;
    var $money;
    var $curcharacter;
    var $banned;
	  var $database;

   //these values are for our database queries
   var $table;
   var $where;

   //these are arrays we keep for this member
   var $characters = array(); //a list of all characters they own
   var $map = array(); //this is the current map they're on in the game
					   //with whatever character they're using to play
   
  /**************
   * Default constructor, load this member's information
   * Remember, this always has to match the name of the class
   **************/
   function __construct($id)
   {
	    global $_SESSION;
	  
      if (!$id || !is_Numeric($id)) { //don't try to do any of this if there's no id number
         return null;
      }

      $this->id = $id;
	    $this->database = &$_SESSION['database']; //now we have the database connection
      $this->table = "members"; //the name of the table we're using in our database when we
                                //run any queries on members
      $this->where = "WHERE id='$this->id'"; //we always want records from members with this ID
	 
      //now we use the database class we made to pull in all the information about this member
      $this->username = $this->database->single("username", $this->table, $this->where);
      $this->password = $this->database->single("password", $this->table, $this->where);
      $this->email = $this->database->single("email", $this->table, $this->where);
      $this->lastlogin = $this->database->single("lastlogin", $this->table, $this->where);
      $this->online = $this->database->single("online", $this->table, $this->where);
      $this->money = $this->database->single("money", $this->table, $this->where);
      $this->curcharacter = $this->database->single("curcharacter", $this->table, $this->where);
      $this->banned = $this->database->single("banned", $this->table, $this->where);

     //last but not least, we have to load any characters they have
     //this uses a function in the database class I never talked about before
     //it goes through the database and finds the ID number of every character they own
     //then it creates a character object for each of those characters they own and puts
     //them into an array. Finally, it returns that array to the character class so we can
     //view all of the players characters.
     $this->characters = $this->database->loadArray("id", "characters", "WHERE memberid='$this->id'", "character");
	 
	   //!!!!! To really get an understanding of what this does, I suggest you uncomment the line
     //below and see what happens. It will print out all the information on each character the
     //member owns.

     //print_r($this->characters);
	 
	   //we know which character they were last on, make this character object active again
	   if ($this->curcharacter) {
		    $this->curcharacter = findObject($this->characters, $this->curcharacter);
     }
	 
	   //first time character has been loaded, they're on a wall spot, they need to move
	   if (getValue($this->curcharacter->x, $this->curcharacter->y, $this->curcharacter->mapid, $this->curcharacter->z) == "W") {
		    $this->curcharacter->x = startPositionX($this->curcharacter->mapid, $this->curcharacter->z);
		    $this->curcharacter->y = startPositionY($this->curcharacter->mapid, $this->curcharacter->z);
	   }
   } //end default constructor

   /**************
   * The member has supplied the correct login information, log them into the game
   **************/
   function login()
   {
      //have they ever logged in before
      if (!$this->lastlogin) {
        $this->sendPassword(); //if not, let's welcome them to our site!
      }
			
      //remember to update the object value
      //get the current date from mysql
      $this->lastlogin = $this->database->date();

      //and the database value for the field
      $this->database->update($this->table, "lastlogin='$this->lastlogin'", $this->where);

      $this->online = true;
      $this->database->update($this->table, "online='$this->online'", $this->where);

      //make all their character's active now that they're online
      $this->database->update("characters", "active='1'", "WHERE memberid='$this->id'");

      //set their current character to the top one in the array
      if (!$this->curcharacter->id) {
        $this->curcharacter = $this->characters[0];
        $this->database->update($this->table, "curcharacter=" . $this->curcharacter->id, $this->where);
      }
   }

   /**************
   * The member is leaving the game
   **************/
   function logout()
   {
       $this->online = false;
       $this->database->update($this->table, "online='$this->online'", $this->where);

       //make all their character's active now that they're online
       $this->database->update("characters", "active=0", "WHERE memberid='$this->id'");
   }
   
   /**************
   * The member forgot their login information
   **************/
   function sendPassword()
   {
      $subject = "Pits of Doom -- Lost Password";
      $from = "jade@design1online.com"; //our email address
      $from_name = "Jade"; //the name you want to show up next to your email address
      $to = $this->email; //we want to send this message to their email address
      $to_name = $this->username;

      //this gives them a nice reply-to option
      //and lets us put their name on the email "to" section
      $header = "From: $from_name <$from>\r\n
                To: $to_name <$to>\r\n"; 

      //this will be formatted exactly how it looks here in the email that's sent
      $message = "Welcome to Pits of Doom!
		
Username: $this->username
Password: $this->password

This is an automated message, please do not respond to it (oh and I hate spam so none of that either!)";
		
      //now we can use the built in php function to send out email
      //remember your server must have a mail service turned on
      //and setup in your php.ini file in order for this to work
      mail($to, $subject, $message, $header); //mail command :) 

      return successMsg("Your login information was sent to: $this->email");
  }
} //end the members class