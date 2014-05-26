<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

header("Content-type: image/png");


$files = scandir('../design/images/background/');
$file = $files[rand(2,sizeof($files)-1)];

$im = imagecreatefrompng("../design/images/background/".$file);
imagepng($im);
imagedestroy($im);

?>