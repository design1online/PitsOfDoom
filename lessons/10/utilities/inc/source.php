<?php
/****************
* File: source.php
* Date: 3.25.2009
* Author: jade@design1online.com
* Purpose: functions for utility/viewsource.php
*****************/

/****************
* Purpose: show the contents of this file
* Precondition: file must be chmod 0755
* Postcondition: file contents displayed as long
*	as the file exists
* WARNING: if you remove the security measure
*	provided by this function you are opening your
*	server to potential malicious attacks by leaking
*	configuration settings and passwords. Please
*	do not remove the security measure for your
*	own safety. This will ensure that all viewable
*	files must be CHMOD 0755 before anyone can
*	view them with this function.
*****************/
function showFileCode($file)
{
	echo "<h3>Viewing $file</h3>";
	
	if (!file_exists($file))
		echo errorMsg("File not found");
	else if (substr(sprintf('%o', fileperms($file)), -4) != 755) // SECURITY MEASURE, DO NOT REMOVE!!
		echo errorMsg("You don't have permission to access this file, permissions currently set to " .
			substr(sprintf('%o', fileperms($file)), -4));
	else
	//display our code with nifty syntax color highlighting
	highlight_file($file);
}

/****************
* Purpose: chmod all files in this directory and sub directory 0755
* Precondition: $root location
* Postcondition: 
*****************/
function chmod0755($root)
{
	if (!$root)
		$root = "../";
		
	$array = scandir($root); //get all the files and directories
	
	//loop through everything
	foreach ($array as $key => $name)
	{
		if ($key > 1 && $name != "images") //exclude back directories and image files
		{
			//check file path
			if (is_file($root . "/" . $name) && !is_file($root . $name))
				$slash = "/";
			else
				$slash = "";
				
			if (is_file($root . $name) || is_file($root . "/" . $name))
				chmod($root . $slash . $name, 0755);
			
			//check subdirectories
			if (is_dir($root . $name))
				chmod0755($root . $name);
			else if (is_dir($name))
				chmod0755($root . "/" . $name);
		}
	}
}

/****************
* Purpose: show the file tree
* Precondition: $root must be defined and must be relative
*	to the location of the viewsource.php file
* Postcondition: directories and files displayed
*****************/
function displayDirectoryFiles($root)
{
	if (!$root)
		$root = "../"; //this path
		
	$array = scandir($root); //get all the files and directories
	
	//loop through everything
	foreach ($array as $key => $name)
	{
		if ($key > 1 && $name != "images") //exclude back directories and image file
		{
			//check file path
			if (is_file($root . "/" . $name) && !is_file($root . $name))
				$slash = "/";
			else
				$slash = "";
				
			//display files
			if (is_file($root . $name) || is_file($root . "/" . $name))
				echo "--<a href=\"?file=" . $root . $slash . $name . "\">$name</a>";
			
			//check subdirectories
			if (is_dir($root . $name))
			{
				echo $root . $name;
				echo "<blockquote>";
				displayDirectoryFiles($root . $name);
				echo "</blockquote>";
			}
			else if (is_dir($name))
			{
				echo $root . "/" . $name;
				echo "<blockquote>";
				displayDirectoryFiles($root . "/" . $name);
				echo "</blockquote>";
			}
			else
				echo "<br/>";
			
		}
	}
}
?>