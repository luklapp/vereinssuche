<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

session_start();

require_once("3rd-party/HTMLPurifier/HTMLPurifier.auto.php");
require_once("lib/Functions.php");

$session = new Session();
// HTMLPurifier
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);

// Passende PHP Dateien laden
if(!isset($_GET['action']) || ctype_digit($_GET['action'])) $module = "index";
else $module = $_GET['action'];

if(isset($_GET['page']) && $_GET['page'] != "") $module .= $_GET['page'];
else $module .= 'Start';

if(file_exists("modules/".$module.".php"))
{
	include("modules/".$module.".php");
}
else
{
	include("modules/404.php");
}

// Footer einbinden
include("includes/footer.php");

?>