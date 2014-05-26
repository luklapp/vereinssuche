<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

$pagetitle = "Login";

$loginPage = new LoginPage();

if(isset($_POST['submit']))
{
   if(isset($_POST['mail']) && isset($_POST['passwort']))
   {
      $userLogin = $session->userLogin($_POST['mail'],$_POST['passwort'],$_POST['keepLoggedIn']);
      var_dump($userLogin);    
   
      if($userLogin == false) header("Location: ?fehler=1");
      else header("Location: ./");

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

<?php if(isset($_GET['fehler'])) echo '<div class="alert alert-danger">Benutzername oder Passwort falsch.</div>'; ?>


	<h1>Login</h1>
  
  <?php

  $loginPage->printLoginForm();

  ?>

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