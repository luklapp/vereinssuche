<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

/**
 * Autoload Funktion
 * @param $class
 * @return bool
 */
function autoload($class) {

    if (HTMLPurifier_Bootstrap::autoload($class)) return true;
	if(file_exists(__dir__."/".$class.".php")) require ($class.".php");
	elseif(file_exists(__dir__."/pages/".$class.".php")) require ("pages/".$class.".php");
	else
	{
	 
		echo "Fehler! Klasse ".$class." wurde nicht gefunden.";
		return false;
	}	

    return true;
}

// Autoload-Funktion registrieren
spl_autoload_register('autoload');

/**
 * Liefert alle Sportarten aus der Datenbank als Array zurück
 * @return Array Array mit Sportarten
 */
function get_sportarten()
{
	$db = Database::getDB();

	$sth = $db->prepare("SELECT * FROM ".DB_PREFIX."_sportarten ORDER BY name ASC");
	$sth->execute();

	$data = $sth->fetchAll();

	return $data;
}

?>