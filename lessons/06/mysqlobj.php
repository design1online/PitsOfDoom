<?php
/**************
* File: mysqlobj.php
* Purpose: database class
**************/

class database
{
	//variables for this class
  var $mysqli;
	var $database;
	var $host;
	var $username;
	var $password;
	var $classerror;
	var $connected;
	
	/**************
	* Purpose: default constructor, is called every time we create an object of this class
	* Precondition: host, username & password for the database, database we're using
	**************/
	function __construct($host, $username, $password, $database)
	{
		if (!$username) {
			return errorMsg("You must enter a username");
    }
			
		if ($username != "root" && !$password) {
			return errorMsg("You must enter a password");
    }
			
		if (!$database) {
			return errorMsg("You must enter a database");
    }
	
		if (!$host) {
			$this->host = "localhost";
    } else {
			$this->host = $host;
    }
			
		$this->username = $username;
		$this->password = $password;
		$this->database = $database;
		$this->classerror = "Database Error: ";
		
		//automatically connect to the database
		$this->connect();
	}
	
	/**************
	* Purpose: connect to the database
	* Precondition: none
	* Postcondition: connected to the database
	**************/
	function connect()
	{
		$this->mysqli = mysqli_connect(
      $this->host,
      $this->username,
      $this->password,
      $this->database
   );
		
		if ($this->mysqli->connect_errno) {
      die($this->classerror . $this->mysqli->connect_error . $this->mysqli->error);
    }
		
		$this->connected = true;
	}
	
	/**************
	* Purpose: end connection to the database
	* Precondition: none
	* Postcondition: close database connection
	**************/
	function disconnect()
	{
		$this->mysqli->close();
		$this->connected = false;
	}
	
	/**************
	* Purpose: check for connected to database
	* Precondition: none
	* Postcondition: connected to the database
	**************/
	function checkconnection()
	{
		if (!$this->connected) {
			$this->connect();
    }
	}
	
	/**************
	* Purpose: query the database
	* Precondition: query to run
	* Postcondition: returns query data
	**************/
	function query($sql)
	{
		if (!$sql) {
			return errorMsg("You must enter a query");
    }
    
		$this->checkconnection();
		$result = $this->mysqli->query($sql)
      or die ($this->classerror . $this->mysqli->error);
    
    return $result;
	}
	
	/**************
	* Purpose: selection query
	* Precondition: fields, table, where
	* Postcondition: returns query data
	**************/
	function select($fields, $table, $where)
	{
		if (!$fields) {
			return errorMsg("You must enter a field");
    }
    
		if (!$table) {
			return errorMsg("You must enter a table");
    }
    
		$this->checkconnection();
			
		$result = $this->mysqli->query("SELECT $fields FROM $table $where")
		  or die ($this->classerror . $this->mysqli->error);
		
		return $result->fetch_assoc();
	}
	
	/**************
	* Purpose: selection a single field
	* Precondition: fields, table, where
	* Postcondition: returns query data
	**************/
	function single($field, $table, $where)
	{
		if (!$field) {
			return errorMsg("You must enter a field");
    }
			
		if (!$table) {
			return errorMsg("You must enter a table");
    }
    
		$this->checkconnection();
			
		$result = $this->mysqli->query("SELECT $field FROM $table $where")
		  or die ($this->classerror . $this->mysqli->error);
		
		$row = $result->fetch_assoc($result);
		return $row[$field];
	}
	
	/**************
	* Purpose: return today's date and time
	**************/
	function date()
	{
		$result = $this->mysqli->query("SELECT NOW() AS date");
		$row = $result->fetch_assoc();
		return $row['date'];
	}
	
	/**************
	* Purpose: update query
	* Precondition: table, fields, where
	* Postcondition: field has been updated
	**************/
	function update($table, $fields, $where)
	{
		if (!$fields) {
			return errorMsg("You must enter a field");
    }
			
		if (!$table) {
			return errorMsg("You must enter a table");
    }
			
		$this->checkconnection();
			
		$result = $this->mysqli->query("UPDATE $table SET $fields $where")
		  or die ($this->classerror . $this->mysqli->error);
		
		return $result;
	}
	
	/**************
	* Purpose: delete query
	* Precondition: table, where
	* Postcondition: row in table has been deleted
	**************/
	function delete($table, $where)
	{
		if (!$table) {
			return errorMsg("You must enter a table");
    }
		
		if (!$where) {
			return errorMsg("You must enter a where condition");
    }
			
		$this->checkconnection();
			
		$result = $this->mysqli->query("DELETE FROM $table $where")
		  or die ($this->classerror . $this->mysqli->error);
    
    return $result;
	}
	
	/**************
	* Purpose: insert query
	* Precondition: table, values
	* Postcondition: row in table has been deleted
	**************/
	function insert($table, $fields, $values)
	{
		if (!$table) {
			return errorMsg("You must enter a table");
    }
		
		if (!$values) {
			return errorMsg("You must enter values in the table");
    }
			
		$this->checkconnection();
			
		$this->mysqli->query("INSERT INTO $table ($fields) VALUES ($values)")
		  or die ($this->classerror . $this->mysqli->error);
		
		//id of the row just inserted
		return $this->mysqli->insert_id;
	}
	
	/**************
	* Purpose: find objects in the database then load them into an array
	* Precondition: field, table, and object
	* Postcondition: returns query data
	**************/
	function loadArray($field, $table, $where, $object)
	{
		$result = $this->mysqli->query("SELECT $field FROM $table $where")
				or die ('cannot load object data from table $table: ' . $this->mysqli->error);
				
		$customarray = array();
		
		while ($row = $result->fetch_array()) {
			array_push($customarray, new $object($row[$field]));
    }
			
		return $customarray;
	}
	
	/**************
	* Purpose: delete everything in a table
	* Precondition: table
	* Postcondition: all fields in table have been deleted
	**************/
	function truncate($table)
	{
		if (!$table) {
			return errorMsg("You must enter a table");
    }
			
		$this->checkconnection();
			
		$this->mysqli->query("TRUNCATE $table")
		  or die ($this->classerror . $this->mysqli->error);
	}
}
