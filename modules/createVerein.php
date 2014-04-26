<?php 

require_once("lib/pages/vereinPage.php");

$pagetitle = "Verein erstellen";

$verein = new VereinPage();

if(isset($_POST['submit']))
{
	$params = array();

	foreach($_POST as $key => $value)
	{
		$params[$key] = htmlspecialchars($value);
	}

	$id = $verein->createVerein($params); 	

	header("Location: index.php?page=Verein&action=".$id);

} 

include("includes/header.php");

?>

<form method="POST">
	
	<label for="name">Vereinsname</label>
	<input name="name" id="name" class="form-control">

	<label for="gruendung">Gründungsjahr</label>
	<input name="gruendung" id="gruendung" class="form-control">

	<label for="sportart">Sportart</label>
	<select name="sportart" id="sportart" class="form-control">
		<?php 
		$sportarten = get_sportarten(); 

		foreach($sportarten as $sportart)
		{
			echo '<option value="'.$sportart->id.'">'.$sportart->name.'</option>';
		}

		?>
	</select>

	<label for="strasse">Adresse</label>
	<input name="strasse" placeholder="Straßenname" id="strasse" class="form-control"/> <input name="strasse_nr" placeholder="Nr." class="form-control"><br>
	<input name="plz" placeholder="PLZ" class="form-control"> <input name="ort" placeholder="Ort" class="form-control">

	<input type="submit" name="submit" value="Jetzt kostenlos Verein registrieren!" />

</form>

<?php

?>