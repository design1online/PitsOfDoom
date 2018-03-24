<?php
/**************
* File: tile.php
* Purpose: tile sheet library
* Author: design1online.com
* Date: 2.25.2011
* NOTE: THIS GOES IN THE IMAGES FOLDER, MOVED HERE SO ITS ACCESSIBLE
*		TO THE VIEW SOURCE UTILITY
**************/
require_once('../oop/mysqlobj.php'); //database object
require_once('../inc/functions.php'); //include the functions						   
require_once('../inc/dbconnect.php'); //connect to the database
session_start();

class tilesheet
{

	var $image;
	var $image_height;
	var $image_width;
	var $tile_height;
	var $tile_width;
	var $xoffset;
	var $yoffset;

	function tilesheet($image, $width, $height, $xoffset = 0, $yoffset = 0)
	{
		if (!$width || !$height || !$image)
			return;

		if (@fopen($image, 'r'))
			$size = getimagesize($image);
		else
			return;

		$this->image = $image;
		$this->image_height = $size[0];
		$this->image_width = $size[1];
		$this->tile_width = $width;
		$this->tile_height = $height;
		$this->xoffset = $xoffset;
		$this->yoffset = $yoffset;

	}

	function displayTile($x, $y)
	{
		$x = mysql_real_escape_string($x);
		$y = mysql_real_escape_string($y);

		if (!is_numeric($x) || !is_numeric($y) || !$this->tile_width || !$this->tile_height)	
			return;

		$dest = imagecreatetruecolor($this->tile_width, $this->tile_height);

		$tilex = ($x * $this->tile_width) + (($x+1) * $this->xoffset);
		$tiley = ($y * $this->tile_height) + (($y+1) * $this->yoffset);

		switch ($this->getExtension())
		{
			case "bmp": 
					header('Content-type: image/vnd.wap.wbmp');
					$src = imagecreatefromwbmp($this->image);
					imagecopy($dest, $src, 0, 0, $tilex, $tiley, $this->tile_width, $this->tile_height);
					imagewbmp($dest);
					imagedestroy($dest);
				break;
			case "gif": 
					header('content-type: image/gif');
					$src = imagecreatefromgif($this->image);
					imagecopy($dest, $src, 0, 0, $x, $y, $this->tile_width, $this->tile_height);
					imagegif($dest);
					imagedestroy($dest);
				break;
			case "jpg": 
			case "jpeg": 
					header('content-type: image/jpeg');
					$src = imagecreatefromjpeg($this->image);
					imagecopy($dest, $src, 0, 0, $x, $y, $this->tile_width, $this->tile_height);
					imagejpeg($dest);
					imagedestroy($dest);
				break;
			case "png": 
					header('Content-type: image/png');
					$src = imagecreatefrompng($this->image);
					imagecopy($dest, $src, 0, 0, $tilex, $tiley, $this->tile_width, $this->tile_height);
					imagepng($dest);
					imagedestroy($dest);
				break;
			default: return;
				break;
		}
	}

	function getExtension()
	{
		return substr($this->image, strrpos($this->image, ".")+1, strlen($this->image) - strrpos($this->image, "."));
	}


} //end class

/**
* Purpose: trying to display a tile
* Precondition: map id, x position and y position
**/
if (!$_GET['map'])
	echo "No Map";
else
{

	//they don't have map data or have moved to a new map
	if (!$_SESSION['mapid'] || $_SESSION['mapid'] != $_GET['map'] || !$_SESSION['map_sheet'])
	{
		$result = mysql_query("SELECT tile_width, tile_height, tile_xoffset, tile_yoffset, tile_sheet FROM map WHERE id='" . mysql_real_escape_string($_GET['map']) . "'")
			or die ('cannot load map data');

		$row = mysql_fetch_assoc($result);
		$_SESSION['mapid'] = mysql_real_escape_string($_GET['map']);
		$_SESSION['map_sheet'] = "../images/tiles/" . $row['tile_sheet'];
		$_SESSION['map_tile_width'] = $row['tile_width'];
		$_SESSION['map_tile_height'] = $row['tile_height'];
		$_SESSION['map_xoffset'] = $row['tile_xoffset'];
		$_SESSION['map_yoffset'] = $row['tile_yoffset'];
	}

	$tilesheet = new tilesheet($_SESSION['map_sheet'], $_SESSION['map_tile_width'], $_SESSION['map_tile_height'], $_SESSION['map_xoffset'], $_SESSION['map_yoffset']);
	$tilesheet->displayTile($_GET['x'],$_GET['y']);
}