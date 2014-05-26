<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

header('Content-type: application/json');

require_once("../lib/Database.php");

error_reporting(0);

$db = Database::getDB();

$lng      = floatval($_GET['lng']);
$lat 	    = floatval($_GET['lat']);
$radius   = floatval($_GET['radius']);
$sportart = intval($_GET['sportart']);

if($sportart != 0) $whereSportart = " && sportartID = :sportart ";

if($_GET['name'] != "")
    $sql = "SELECT id,name,adresse_lng,adresse_lat,SQRT((111.3 * cos(RADIANS((adresse_lat+:lat)/2)) * (adresse_lng - :lng)) * (111.3 * cos(RADIANS((adresse_lat+:lat)/2)) * (adresse_lng - :lng)) + (111.3 * (adresse_lat - :lat))*(111.3 * (adresse_lat - :lat))) as distance FROM vs_vereine WHERE adresse_lat != '' && adresse_lng != '' && name LIKE :suchbegriff $whereSportart ORDER BY distance ASC";
else
    $sql = "SELECT id,name,adresse_lng,adresse_lat,SQRT((111.3 * cos(RADIANS((adresse_lat+:lat)/2)) * (adresse_lng - :lng)) * (111.3 * cos(RADIANS((adresse_lat+:lat)/2)) * (adresse_lng - :lng)) + (111.3 * (adresse_lat - :lat))*(111.3 * (adresse_lat - :lat))) as distance FROM vs_vereine WHERE adresse_lat != '' && adresse_lng != '' $whereSportart HAVING distance <= :radius ORDER BY distance ASC";

$sth = Database::getDB()->prepare($sql);
$sth->bindValue(':suchbegriff', "%{$_GET['name']}%", PDO::PARAM_STR);
$sth->bindValue(':lat', $lat, PDO::PARAM_STR);
$sth->bindValue(':lng', $lng, PDO::PARAM_STR);
$sth->bindValue(':radius', $radius, PDO::PARAM_STR);
$sth->bindValue(':sportart', $sportart, PDO::PARAM_STR);
$sth->bindValue(':suchbegriff', "%{$_GET['name']}%", PDO::PARAM_STR);

$sth->execute();

$result = $sth->fetchAll();

$i = 0;

echo'[';

foreach($result as $verein)
{
    echo '{';
    echo '"id" : "'.$verein->id.'",';
    echo '"name" : "'.$verein->name.'",';
    echo '"lng" : "'.$verein->adresse_lng.'",';
    echo '"lat" : "'.$verein->adresse_lat.'",';
    echo '"distance" : "'.$verein->distance.'"';

    if($i < sizeof($result)-1) echo '},';
    else echo '}';

    $i++;
}

echo']';

?>