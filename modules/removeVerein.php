<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

$vereinPage = new VereinPage();

if(isset($_COOKIE['userID']) && !isset($_GET['saved']))
{

	if(ctype_digit($_GET['id']))
	{
		$remove = $session->removeVerein($_GET['id']);
		if($remove < 1) header("Location: ?saved=false");
	}

	else header("Location: ?saved=false");

}

	$pagetitle = "Verein entfernen";

	include("includes/header.php"); 

	?>

<div id="mapWrapper">

  <div id="map"></div>

  <div id="mapOverlay"></div>

</div>

<div id="content" class="container">

<?php 

if(isset($_GET['saved']) && $_GET['saved'] == "false") echo '<div class="alert alert-danger">Speichern fehlgeschlagen. Bitte versuche es erneut!</div>'; 

else echo '<div class="alert alert-success">Du bist nun nicht mehr Mitglied in diesem Verein.</div>';

?>