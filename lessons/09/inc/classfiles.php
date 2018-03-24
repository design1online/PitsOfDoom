<?php
/**************
* File: classfiles.php
* Date: 3.10.2009
* Author: jade@design1online.com
* Purpose: includes all our important class files
* NOTE: This file MUST be included before you connect to the database
*****************/

//turn on error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// the title of this lesson
$_sitetitle = "Pits of Doom -- Lesson 8";

include('oop/coordinateobj.php');
include('oop/characterobj.php');
include('oop/monsterobj.php');
include('oop/charactertypeobj.php');
include('oop/mapobj.php');
include('oop/memberobj.php');
include('oop/mysqlobj.php');