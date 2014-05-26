<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

$pagetitle = "Registrieren";

$registerPage = new RegisterPage();
$session = new Session();

if(isset($_POST['submit']))
{
   if(isset($_POST['vorname']) && isset($_POST['nachname']) && isset($_POST['mail']) && isset($_POST['passwort']) && isset($_POST['passwort2']) && isset($_POST['isVisible']))
   {
      if($_POST['x'] != $_POST['test']){ header("Location: ?fehler=5"); exit; }

      if($_POST['passwort'] != $_POST['passwort2']){ header("Location: ?fehler=2"); exit; }
      if(!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)){ header("Location: ?fehler=3"); exit; }

      $registerUser = $registerPage->registerUser($_POST['vorname'],$_POST['nachname'],$_POST['mail'],$_POST['passwort'],$_POST['isVisible']);
      
      if($registerUser == -1){ header("Location: ?fehler=4"); exit; }

      $session->userLogin($_POST['mail'],$_POST['passwort'],0);
      header("Location: ./");
            

   }
   else header("Location: ?fehler=1");

}
else 
{

// Header Datei einbinden
include("includes/header.php");

?>

<div id="mapWrapper">

  <div id="map"></div>

  <div id="mapOverlay"></div>

</div>

<div id="content" class="container">

	<h1>Registrieren</h1>

  <?php 

  $x = rand(1,5);
  $y = rand(1,5);

  if(isset($_GET['fehler']))
  {
    if($_GET['fehler'] == 1) echo '<div class="alert alert-danger">Bitte alle Felder ausfüllen!</div>';
    if($_GET['fehler'] == 2) echo '<div class="alert alert-danger">Die Passwörter müssen übereinstimmen!</div>';
    if($_GET['fehler'] == 3) echo '<div class="alert alert-danger">Bitte eine korrekte E-Mail-Adresse angeben!</div>';
    if($_GET['fehler'] == 4) echo '<div class="alert alert-danger">Ein Account mit dieser Mail-Adresse existiert bereits!</div>';
    if($_GET['fehler'] == 5) echo '<div class="alert alert-danger">Die Sicherheitsfrage wurde falsch beantwortet!</div>';
  }
  ?>

  <form action="" method="POST">

  <input type="hidden" name="x" value="<?php echo $x+$y;?>">
    
      <div class="form-group">
        <label>Vorname</label>
        <input name="vorname" class="form-control" placeholder="Vorname" required>
      </div>
      <div class="form-group">
        <label>Nachname</label>
        <input name="nachname" class="form-control" placeholder="Nachname" required>
      </div>
      <div class="form-group">
        <label>E-Mail-Adresse</label>
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
      <div class="form-group">
        Mein Profil soll 
        <select name="isVisible" required>
          <option value="1">Öffentlich</option>
          <option value="0">nicht öffentlich</option>
        </select>
        sein
      </div>
      <div class="form-group">
        <label>Was ist <?php echo $x.' + '.$y;?>?</label>
        <input name="test" class="form-control" required>
      </div>

    <input name="submit" value="Jetzt kostenlos registrieren!" type="submit">
  </form>

</div>

<?php

}

try {
$db = Database::getDB();	
}
catch(Exception $e)
{
	echo $e;
}

?>