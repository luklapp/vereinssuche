<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

$pagetitle = "Profil bearbeiten";

$profileEditPage = new ProfilPage($_COOKIE['userID']);

if(isset($_POST['submit']))
{
  
  $config->set('HTML.Allowed',''); 
  foreach($_POST as $key => $value)
  {
    $_POST[$key] = $purifier->purify($value);
  }

   if(isset($_POST['vorname']) && isset($_POST['nachname']) && isset($_POST['isVisible']))
   {

      $user = $session->getUserData();

      if($_POST['gb_jahr'] != 0 && ctype_digit($_POST['gb_tag']) && ctype_digit($_POST['gb_monat']) && ctype_digit($_POST['gb_jahr']))
      {
        $birthday = new DateTime($_POST['gb_tag'].'.'.$_POST['gb_monat'].'.'.$_POST['gb_jahr']); 
        $birthday = $birthday->getTimestamp();
      }
      else
        $birthday = 0;

      $editUser = $profileEditPage->editUser($_POST['vorname'],$_POST['nachname'],$user->email,$_POST['passwort'],$_POST['isVisible'],$birthday);
      
      header("Location: ../User/".$user->id);

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

  <?php $user = $session->getUserData(); ?>

  <a class="button" href="./User/editAvatar">Avatar bearbeiten</a>

	<h1>Profil bearbeiten</h1>

  <?php 
  if(isset($_GET['fehler']))
  {
    if($_GET['fehler'] == 1) echo '<div class="alert alert-danger">Bitte alle Felder ausfüllen!</div>';
    if($_GET['fehler'] == 2) echo '<div class="alert alert-danger">Die Passwörter müssen übereinstimmen!</div>';
    if($_GET['fehler'] == 3) echo '<div class="alert alert-danger">Bitte eine korrekte E-Mail-Adresse angeben!</div>';
  }
  ?>

  <form action="" method="POST"> 

      <div class="form-group">
        <label>Vorname</label>
        <input name="vorname" class="form-control" value="<?php echo $user->vorname;?>" required>
      </div>
      <div class="form-group">
        <label>Nachname</label>
        <input name="nachname" class="form-control" value="<?php echo $user->nachname;?>" required>
      </div>
      <div class="form-group">
        <label>Geburtsdatum</label><br>
        <select name="gb_tag"><option value="0">-</option>
        <?php for($i = 1; $i <= 31; $i++) if($i == date('d',$user->geburtsdatum) && $user->geburtsdatum != 0) echo '<option selected>'.$i.'</option>'; else echo '<option>'.$i.'</option>'; ?>.
        </select>
        <select name="gb_monat"><option value="0">-</option>
        <?php for($i = 1; $i <= 12; $i++) if($i == date('m',$user->geburtsdatum) && $user->geburtsdatum != 0) echo '<option selected>'.$i.'</option>'; else echo '<option>'.$i.'</option>'; ?>.
        </select>
        <select name="gb_jahr"><option value="0">-</option>
        <?php for($i = date('Y'); $i >= 1960; $i--) if($i == date('Y',$user->geburtsdatum) && $user->geburtsdatum != 0) echo '<option selected>'.$i.'</option>'; else echo '<option>'.$i.'</option>'; ?>.
        </select>
      </div>
      <div class="form-group">
        <label>Passwort</label>
        <input name="passwort" type="password" class="form-control">
      </div>
      <div class="form-group">
        <label>Passwort wiederholen</label>
        <input name="passwort2" type="password" class="form-control">
      </div>

      <div class="form-group">
        Mein Profil soll 
        <select name="isVisible" required>
          <option value="1" <?php if($user->is_visible == 1) echo 'selected';?>>Öffentlich</option>
          <option value="0" <?php if($user->is_visible == 0) echo 'selected';?>>nicht öffentlich</option>
        </select>
        sein
      </div>

    <input name="submit" value="Profil speichern" type="submit" >
  </form>

</div>

<?php

}

?>