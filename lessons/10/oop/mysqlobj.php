<?php
/**************
* File: mysqlobj.php
* Date: 3.10.2009
* Author: jade@design1online.com
* Purpose: database class
*****************/
class database
{
	//variables for this class
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
	function database($host, $username, $password, $database)
	{
		global $_SESSION;
		
		if (!$username)
			return errorMsg("You must enter a username");
			
		if ($username != "root" && !$password)
			return errorMsg("You must enter a password");
			
		if (!$database)
			return errorMsg("You must enter a database");
	
		if (!$host)
			$this->host = "localhost";
		else
			$this->host = $host;
			
		if ($_SESSION['debug'])
			echo "Creating new mysql object for $host.<br>";
			
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
		mysql_connect($this->host, $this->username, $this->password)
		or die ($this->classerror . mysql_error());
		
		mysql_select_db($this->database)
		or die ($this->classerror . " cannot connect to the database because " . mysql_error());
		
		$this->connected = true;
	}
	
	/**************
	* Purpose: end connection to the database
	* Precondition: none
	* Postcondition: close database connection
	**************/
	function disconnect()
	{
		mysql_close();
		$this->connected = false;
	}
	
	/**************
	* Purpose: check for connected to database
	* Precondition: none
	* Postcondition: connected to the database
	**************/
	function checkconnection()
	{
		//if (!$this->connected)
			$this->connect();
	}
	
	/**************
	* Purpose: query the database
	* Precondition: query to run
	* Postcondition: returns query data
	**************/
	function query($sql)
	{
		if (!$sql)
			return errorMsg("You must enter a query");
			
		$this->checkconnection();
		
		$result = mysql_query($sql)
		or die ($this->classerror . " cannot select $fields from table $table because " . mysql_error());
		
		return $result;
	}
	
	/**************
	* Purpose: selection query
	* Precondition: fields, table, where
	* Postcondition: returns query data
	**************/
	function select($fields, $table, $where)
	{
		if (!$fields)
			return errorMsg("You must enter a field");
			
		if (!$table)
			return errorMsg("You must enter a table");
		
		$this->checkconnection();
			
		$result = mysql_query("SELECT $fields FROM $table $where")
		or die ($this->classerror . " cannot select $fields from table $table because " . mysql_error());
		
		$row = mysql_fetch_assoc($result);

		return $row;
	}
	
	/**************
	* Purpose: selection a single field
	* Precondition: fields, table, where
	* Postcondition: returns query data
	**************/
	function single($field, $table, $where)
	{
		if (!$field)
			return errorMsg("You must enter a field");
			
		if (!$table)
			return errorMsg("You must enter a table");
		
		$this->checkconnection();
		
		$result = mysql_query("SELECT $field FROM $table $where")
		or die ($this->classerror . " cannot select $fields from table $table because " . mysql_error());
		
		$row = mysql_fetch_assoc($result);

		return $row[$field];
	}
	
	/**************
	* Purpose: return today's date
	* Precondition: none
	* Postcondition: returns the current datetime
	**************/
	function date()
	{
		$result = mysql_query("SELECT NOW() as date");
		
		$row = mysql_fetch_array($result);
		return $row['date'];
	}
	
	/**************
	* Purpose: update query
	* Precondition: table, fields, where
	* Postcondition: field has been updated
	**************/
	function update($table, $fields, $where)
	{
		if (!$fields)
			return errorMsg("You must enter a field");
			
		if (!$table)
			return errorMsg("You must enter a table");
			
		$this->checkconnection();
			
		mysql_query("UPDATE $table SET $fields $where")
		or die ($this->classerror . " cannot select $fields from table $table because " . mysql_error());
		
		return $field;
	}
	
	/**************
	* Purpose: delete query
	* Precondition: table, where
	* Postcondition: row in table has been deleted
	**************/
	function delete($table, $where)
	{
		if (!$table)
			return errorMsg("You must enter a table");
		
		if (!$where)
			return errorMsg("You must enter a where condition");
			
		$this->checkconnection();
			
		mysql_query("DELETE FROM $table $where")
		or die ($this->classerror . " cannot select $fields from table $table because " . mysql_error());
	}
	
	/**************
	* Purpose: insert query
	* Precondition: table, values
	* Postcondition: row in table has been deleted
	**************/
	function insert($table, $fields, $values)
	{

		if (!$table)
			return errorMsg("You must enter a table");
		
		if (!$values)
			return errorMsg("You must enter values in the table");
			
		$this->checkconnection();
			
		mysql_query("INSERT INTO $table ($fields) VALUES ($values)")
		or die ($this->classerror . " cannot insert $fields from table $table because " . mysql_error());
		
		//id of the row just inserted
		return mysql_insert_id();
	}
	
	/**************
	* Purpose: find objects in the database then load them into an array
	* Precondition: field, table, and object
	* Postcondition: returns query data
	**************/
	function loadArray($field, $table, $where, $object)
	{
		$loop = mysql_query("SELECT $field FROM $table $where")
				or die ('cannot load object data from table $table: ' . mysql_error());
				
		$customarray = array();
		
		while ($row = mysql_fetch_array($loop))
			array_push($customarray, new $object($row[$field]));
			
		return $customarray;
	}
	
	/**************
	* Purpose: delete everything in a table
	* Precondition: table
	* Postcondition: all fields in table have been deleted
	**************/
	function truncate($table)
	{
		if (!$table)
			return errorMsg("You must enter a table");
			
		$this->checkconnection();
			
		mysql_query("TRUNCATE $table")
		or die ($this->classerror . " cannot truncate table $table because " . mysql_error());
	}
}
?>