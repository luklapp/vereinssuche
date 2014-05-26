<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

if($session->isLoggedIn()){ header("Location: ./User/".$session->getUserID()); exit; }

if(isset($_POST['submitPassword']))
{
	if($_POST['password'] != $_POST['password2']) header("Location: ?mail=".$_POST['mail']."&key=".$_POST['key']."&fehler=3");
	
	$session->resetPassword($_POST['key'],$_POST['mail'],$_POST['password']);

	$session->userLogin($_POST['mail'],$_POST['password'],0);

	header("Location: ./");
}

if(isset($_POST['submit']))
{

	if (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)){ header("Location: ?fehler=1"); exit; }
	elseif($_POST['x'] != $_POST['test']){ header("Location: ?fehler=2"); exit; }
  else
	{
		$session->forgotPassword($_POST['mail']);
    header("Location: ?success=true");
    exit;
	}

}

$pagetitle = "Passwort zurücksetzen";

include("includes/header.php"); 

?>

<div id="mapWrapper">

  <div id="map"></div>

  <div id="mapOverlay"></div>

</div>

<div id="content" class="container">

<?php 
if(isset($_GET['fehler']) && $_GET['fehler'] == 1) echo '<div class="alert alert-danger">Ein Benutzer mit dieser Mailadresse existiert nicht.</div>'; 
if(isset($_GET['fehler']) && $_GET['fehler'] == 2) echo '<div class="alert alert-danger">Die Sicherheitsfrage wurde falsch beantwortet. Bitte versuche es erneut!</div>'; 
elseif(isset($_GET['success'])) echo '<div class="alert alert-success">Du erhältst in wenigen Minuten eine Mail.</div>';

if(isset($_GET['key']) && isset($_GET['mail']) && filter_var($_GET['mail'], FILTER_VALIDATE_EMAIL))
{ 
	$params = array(
		':email' => $_GET['mail'],
		':key' => $_GET['key']
	);

    $query = Database::getDB()->prepare("SELECT COUNT(*) FROM ".DB_PREFIX."_user WHERE email = :email && forgot_password = :key");
    $query->execute($params);

    if($query->fetchColumn() < 1 && !isset($_POST['submitPassword'])) header("Location: ?fehler=2");

?>

<h2>Passwort zurücksetzen</h2>

<?php if(isset($_GET['fehler']) && $_GET['fehler'] == 3) echo '<div class="alert alert-danger">Die Passwörter stimmen nicht überein!</div>'; 


?>

<form method="POST">   

      <input type="hidden" name="key" value="<?php echo $_GET["key"];?>">
      <input type="hidden" name="mail" value="<?php echo $_GET["mail"];?>">

      <div class="form-group">
        <label>Passwort</label>
        <input type="password" name="password" class="form-control" required>
      </div>

      <div class="form-group">
        <label>Passwort bestätigen</label>
        <input type="password" name="password2" class="form-control" required>
      </div>

      <input type="submit" name="submitPassword" value="Neues Passwort speichern">

</form>

<?php		
}

else
{ 

  $x = rand(1,5);
  $y = rand(1,5);

  ?>

<h2>Passwort vergessen</h2>

<form method="POST">

      <input type="hidden" name="x" value="<?php echo $x+$y;?>">

      <div class="form-group">
        <label>Mailadresse</label>
        <input type="mail" name="mail" class="form-control" placeholder="E-Mail-Adresse" required>
      </div>

      <div class="form-group">
        <label>Was ist <?php echo $x.' + '.$y;?>?</label>
        <input name="test" class="form-control" required>
      </div>

      <input type="submit" name="submit" value="Passwort zurücksetzen">

</form>

<?php } ?>