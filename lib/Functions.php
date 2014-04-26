<?php

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