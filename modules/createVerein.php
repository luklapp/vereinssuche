<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

$pagetitle = "Verein erstellen";

$verein = new VereinPage();

if(isset($_POST['submit']))
{
	if($_POST['x'] != $_POST['test']){ header("Location: ?fehler=1"); exit; }

	$id = $verein->createVerein($_POST); 	

	if($id < 1) header("Location: ?fehler=".$id*-1);
	else header("Location: ../Verein/".$id);

} 

include("includes/header.php");

?>

<div id="mapWrapper">

  <div id="map"></div>

  <div id="mapOverlay"></div>

</div>

<div id="content" class="container">

<?php 
if(isset($_GET['fehler']) && $_GET['fehler'] == 1) echo '<div class="alert alert-danger">Die Sicherheitsfrage wurde falsch beantwortet.</div>';
elseif(isset($_GET['fehler'])) echo '<div class="alert alert-danger">Ein unbekannter Fehler ist aufgetreten. Bitte versuche es erneut.</div>'; 

  $x = rand(1,5);
  $y = rand(1,5);

?>

<h1>Verein registrieren</h1>

<form method="POST">

<input type="hidden" name="x" value="<?php echo $x+$y;?>">

<?php

if(!isset($_COOKIE['userID']))
{ ?>

      <div class="form-group">
        <label >Dein Vorname</label>
        <input name="vorname" class="form-control" placeholder="Vorname" required>
      </div>
      <div class="form-group">
        <label>Dein Nachname</label>
        <input name="nachname" class="form-control" placeholder="Nachname" required>
      </div>
      <div class="form-group">
        <label>Deine E-Mail-Adresse</label>
        <input name="mail" type="email" class="form-control" placeholder="mail@provider.tld" required>
      </div>
      <div class="form-group">
        <label>Passwort</label>
        <input name="passwort" type="password" class="form-control" placeholder="" required>
      </div>
      <div class="form-group">
        <label>Passwort bestätigen</label>
        <input name="passwort2" type="password" class="form-control" placeholder="" required>
      </div>
      <input type="hidden" name="isVisible" value="0">

	<hr>   

<?php } ?>
	
	<div class="form-group">
		<label>Vereinsname</label>
		<input name="name" class="form-control">
	</div>

	<div class="form-group">
		<label>Gründungsjahr</label>
		<input name="gruendung" class="form-control">
	</div>

	<div class="form-group">
		<label>Sportart</label>
		<select name="sportart" class="form-control">
			<?php 
			$sportarten = get_sportarten(); 

			foreach($sportarten as $sportart)
			{
				echo '<option value="'.$sportart->id.'">'.$sportart->name.'</option>';
			}

			?>
		</select>
	</div>

	<div class="form-group">
		<label>Adresse</label>
		<div class="form-group">
		<input name="strasse" placeholder="Straßenname" class="form-control" style="float:left; width:75%;"> <input name="strasse_nr" placeholder="Nr." class="form-control" style="width:25%;">
		</div>
		<div class="form-group">
		<input name="plz" placeholder="PLZ" class="form-control" style="float:left; width:25%;"> <input name="ort" placeholder="Ort" class="form-control" style="width:75%;" required>
		</div>
	</div>
      <div class="form-group">
        <label>Was ist <?php echo $x.' + '.$y;?>?</label>
        <input name="test" class="form-control" required>
      </div>
	<input type="submit" name="submit" value="Jetzt kostenlos Verein registrieren!" />

</form>