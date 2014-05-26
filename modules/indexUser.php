<?php 

//require_once(__DIR__."/../lib/pages/ProfilPage.php");

if(!isset($_GET['action']) || !ctype_digit($_GET['action'])) header("Location: ./");

$profilPage = new ProfilPage($_GET['action']);

$user = $profilPage->getUserData();

$pagetitle = "Profil von ".$user->vorname." ".$user->nachname;

// Header Datei einbinden
include("includes/header.php");

?>

<div id="mapWrapper">

  <div id="map"></div>

  <div id="mapOverlay">
    <?php 
    if($user != false)
    { 
      if($user->avatar == 0) $avatar = "default.jpg";
      else $avatar = $user->id.".jpg";

    ?>
       <img class="avatar" src="design/images/profilbilder/<?php echo $avatar; ?>" alt="<?php echo "Profil von ".$user->vorname." ".$user->nachname;?>" title="<?php echo "Profil von ".$user->vorname." ".$user->nachname;?>">
    <?php } ?>
  </div>

</div>

<div id="content" class="container">

  <?php 

  if($session->isLoggedIn() && $_GET['action'] == $_COOKIE['userID']) echo '<a href="./User/edit" class="button"><span class="glyphicon glyphicon-pencil"></span> Bearbeiten</a>';

  if($user == false) echo 'Dieser Benutzer existiert nicht!';
  elseif($user->is_visible == 0 && $_GET['action'] != $_COOKIE['userID']) echo 'Dieses Profil ist nicht sichtbar.';
  
  else
  { 

  	if($user->is_visible == 0 && $_GET['action'] == $_COOKIE['userID']) echo 'Dieses Profil ist für andere Benutzer nicht sichtbar.';

  }	

    ?>

<?php 

if($session->isLoggedIn())
{

  echo '<h2>Profil von '.$user->vorname.' '.$user->nachname.'</h2>';

  // Alter
  if($user->geburtsdatum > 0)
    echo '<h4>'.intval((time() - $user->geburtsdatum) / (3600 * 24 * 365.25)).' Jahre alt</h4>';

  $vereineAktiv = $profilPage->getVereineAktiv(); 

  if(sizeof($vereineAktiv) > 0)
  {

    echo '<h3>Aktiv bei folgenden Vereinen</h3>';

    foreach($vereineAktiv as $verein)
    {
      echo '<a href="Verein/'.$verein->id.'">'.$verein->name.'</a>';
      if ($verein != end($vereineAktiv)) echo ', ';
    }

  }

  else echo $user->vorname.' '.$user->nachname.' ist derzeit bei keinem Verein aktiv.';

  ?>



  <?php

  $vereine = $profilPage->getVereine(); 

  if(sizeof($vereine) > 0)
  {

    echo '<h3>Zuvor bei folgenden Vereinen aktiv:</h3>';

    foreach($vereine as $verein)
    {
      echo '<a href="./Verein/'.$verein->id.'">'.$verein->name.'</a>';
      if ($verein != end($vereine)) echo ', ';
    }

  }



  ?>
    
  <?php
}



else
{
  echo 'Userprofile sind nur für eingeloggte User sichtbar.';
}

  ?>

	

</div>