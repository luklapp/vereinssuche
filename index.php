<?php

session_start();

require_once("lib/Database.php");
require_once("lib/Session.php");
require_once("lib/Functions.php");

if(!isset($_GET['action']) || ctype_digit($_GET['action'])) $module = "index";
else $module = $_GET['action'];

if(isset($_GET['page'])) $module .= $_GET['page'];
else $module .= 'Start';

if(file_exists("modules/".$module.".php"))
{
	include("modules/".$module.".php");
}
else
{
	echo $module.".php existiert nicht.";
	include("modules/404.php");
}

// Footer einbinden
include("includes/footer.php");

?>