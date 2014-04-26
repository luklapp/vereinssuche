<?php 

require_once("lib/pages/vereinPage.php");

$vereinPage = new VereinPage();

// ID wurde übergeben
if(isset($_GET['action']) && ctype_digit($_GET['action']))
{
	
	$verein = $vereinPage->getVereinData($_GET['action']);

	$pagetitle = $verein->name;

	include("includes/header.php"); 

	?>

	<h2>Vereinsseite von <?php echo $verein->name;?></h2>

	<ul class="nav nav-tabs" id="vereinTabs">
	  <li class="active"><a href="#home" data-toggle="tab">Home</a></li>
	  <li><a href="#profile" data-toggle="tab">Profile</a></li>
	  <li><a href="#messages" data-toggle="tab">Messages</a></li>
	  <li><a href="#settings" data-toggle="tab">Settings</a></li>
	</ul>

	<div class="tab-content">
	  <div class="tab-pane active" id="home">1</div>
	  <div class="tab-pane" id="profile">2</div>
	  <div class="tab-pane" id="messages">3</div>
	  <div class="tab-pane" id="settings">4</div>
	</div>


	<?php

}

// Keine ID übergeben
// Generiere Liste der letzten 10 Vereine
else
{
	
	$vereine = $vereinPage->getVereinList();

	$pagetitle = "Vereinsliste";

	include("includes/header.php");

?>

	<table class="table">
		<thead>
			<tr>
				<td>Name</td>
				<td>Sportart</td>
				<td>Adresse</td>
				<td></td>
			</tr> 
		</thead>
		<tbody>

<?php

	foreach($vereine as $verein)
	{ ?>
			<tr>
				<td><?php echo $verein->name;?></td>
				<td><?php echo $verein->sportart; ?></td>
				<td><?php echo $verein->adresse_strasse." ".$verein->adresse_strasse_nr.", ".$verein->adresse_plz." ".$verein->adresse_ort;?></td>
				<td><a href="/mmp1/Verein/<?php echo $verein->id;?>/">&raquo; Vereinsseite ansehen</a></td>
			</tr>
	<?php
	} ?>

		</tbody>
	</table>

<?php

}

?>