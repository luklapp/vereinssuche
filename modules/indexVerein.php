<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

$vereinPage = new VereinPage();
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);	

// ID wurde übergeben
if(isset($_GET['action']) && ctype_digit($_GET['action']))
{

	$verein = $vereinPage->getVereinData($_GET['action']);

	if($verein != null)
        $pagetitle = $verein->name;
    else
        $pagetitle = "Unbekannter Verein";

	if(isset($_POST['submitNews']))
	{

		foreach($_POST as $key => $value)
		{
			$news[$key] = $purifier->purify($value);
		}

		if($vereinPage->createVereinNews($news)) header("Location: ?savedNews=true");
		else header("Location: ?saved=false");

	}

	if(isset($_POST['submitVerein']))
	{
		if(!ctype_digit($_POST['von']) || !ctype_digit($_POST['bis']) || !ctype_digit($_POST['vereinID'])) header("Location: ?saved=false");

		if($session->addVerein($_POST)) header("Location: ?saved=true");
		else header("Location: ?saved=false");

	}

	if(isset($_POST['submit']))
	{

		$config->set('HTML.Allowed','');

		foreach($_POST as $key => $value)
		{
			$vereinParam[$key] = $purifier->purify($value);
		}

		$vereinPage->save($vereinParam);

		if($vereinPage->save($vereinParam)) header("Location: ?saved=true");
		else header("Location: ?saved=false");

	}

	include("includes/header.php"); 

	?>

<div id="mapWrapper">

  <div id="map"></div>

  <div id="mapOverlay"></div>

</div>

<!-- News Modal -->
<div class="modal fade" id="newsModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <form method="POST">

        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">News schreiben</h4>
      </div>
      <div class="modal-body">

        <input type="hidden" name="vereinID" value="<?php echo $verein->id;?>">

        <div class="form-group">
          <label>Titel</label>
          <input class="form-control" name="titel" required placeholder="Titel der News">
        </div>

        <div class="form-group">
          <label>Text</label>
          <textarea id="editor1" class="ckeditor" name="text" required placeholder="Text"></textarea>
        </div>

      </div>


		<script>
		// Verhindert das schließen des Modals, wenn im CKEditor ein Input / Select angeklickt wird
		CKEDITOR.replace('editor1');

		$.fn.modal.Constructor.prototype.enforceFocus = function () {
		    modal_this = this
		    $(document).on('focusin.modal', function (e) {
		        if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length
				&&
		        !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select') && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
		            modal_this.$element.focus()
		        }
		    })
		};
		</script>
        
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
        <input type="submit" class="btn btn-primary" name="submitNews" value="News speichern">
      </div>

        </form>

    </div>
  </div>
</div>

<!-- News Modal -->
<div class="modal fade" id="vereinModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <form method="POST">
        <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Ich habe bei <?php echo  $verein->name;?> gespielt</h4>
      </div>
      <div class="modal-body">

        <input type="hidden" name="vereinID" value="<?php echo $verein->id;?>">

        <div class="form-group">
          <label>Von</label>
          <select name="von" class="form-control" >
          	<?php for($i = date('Y'); $i > date('Y')-25; $i--) echo '<option>'.$i.'</option>'; ?>
          </select>
        </div>

        <div class="form-group">
          <label>Bis</label>
          <select name="bis" class="form-control" >
          	<option value="0">Heute</option>
          	<?php for($i = date('Y'); $i > date('Y')-25; $i--) echo '<option>'.$i.'</option>'; ?>
          </select>
        </div>
       </div>
        
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
        <input type="submit" class="btn btn-primary" name="submitVerein" value="Verein beitreten">
      </div>
     </form>
    </div>
  </div>
</div>

<div id="content" class="container">

<?php

if($verein == null) echo 'Dieser Verein existiert nicht.';
else
{

if(isset($_GET['saved']) && $_GET['saved'] == "false") echo '<div class="alert alert-danger">Speichern fehlgeschlagen. Bitte versuche es erneut!</div>'; 
elseif(isset($_GET['saved']) && $_GET['saved'] == "true") echo '<div class="alert alert-success">Verein erfolgreich gespeichert.</div>';
if(isset($_GET['savedNews']) && $_GET['savedNews'] == "true") echo '<div class="alert alert-success">News erfolgreich gespeichert.</div>';

if($session->isLoggedIn() && $session->isInVerein($verein->id)) echo '<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span> Du bist Mitglied in diesem Verein. <a href="./Verein/remove/'.$verein->id.'">&raquo; Verein aus Profil löschen</a></div>';

if($session->isLoggedIn() && $vereinPage->isAdmin($_COOKIE['userID'],$verein->id)) echo '<a data-toggle="modal" data-target="#newsModal" data-remote="false" class="button"><span class="glyphicon glyphicon-pencil"></span> News schreiben</a>'; 
if($session->isLoggedIn() && !$session->isInVerein($verein->id)) echo '<a data-toggle="modal" data-target="#vereinModal" data-remote="false" class="button"><span class="glyphicon glyphicon-plus"></span> Verein beitreten</a>'; 

?>

	<h2>Vereinsseite von <?php echo $verein->name;?></h2>

	<ul class="nav nav-tabs" id="vereinTabs">
	  <li class="active"><a href="#start" data-toggle="tab">Startseite</a></li>
	  <li><a href="#data" data-toggle="tab">Daten</a></li>
	  <li><a href="#member" data-toggle="tab">Mitglieder</a></li>
	  <li><a href="#share" data-toggle="tab">Teilen</a></li>
	  <?php if($session->isLoggedIn() && $vereinPage->isAdmin($_COOKIE["userID"],$verein->id)) echo '<li><a href="#edit" data-toggle="tab">Bearbeiten</a></li>'; ?>
	</ul>

	<div class="tab-content">
	  <div class="tab-pane active" id="start">

	  <?php

	  $i = 0;

	  foreach($vereinPage->getVereinNews($verein->id) as $news)
	  {
	  	echo '<h2>'.$news->titel.'</h2>';
	  	echo '<span class="date"><span class="glyphicon glyphicon-time"></span> Geschrieben am '.date('d.m.Y, H:i',$news->time).' Uhr</span>';
	  	echo '<p>'.$news->text.'</p>';
	  	echo '<hr>';
	  	
	  	$i++;

	  }

	  if($i == 0) echo "Dieser Verein hat noch keine Neuigkeiten gepostet.";

	  ?>

	  </div>
	  <div class="tab-pane" id="data">

	  <table>
	  	<tr>
	  		<td>Vereinsname: </td>
	  		<td><?php echo $verein->name;?></td>
	  	</tr>
	  	<tr>
	  		<td>Gegründet: </td>
	  		<td><?php echo $verein->gruendung;?></td>
	  	</tr>
	  	<tr>
	  		<td>Adresse: </td>
	  		<td>
	  		<?php 
		  		if($verein->adresse_strasse != "") echo $verein->adresse_strasse." ".$verein->adresse_strasse_nr.", "; 
		  		echo $verein->adresse_plz." ".$verein->adresse_ort;
	  		?>
	  		</td>
	  	</tr>
	  </table>

		<script>
		function initialize(lat,lng) {

		  if(lat != 0 && lng != 0)
		  {
			  var latLng  = new google.maps.LatLng(lat,lng);

			  var mapOptions = {
			    zoom: 12,
			    center: latLng
			  };
			  map = new google.maps.Map(document.getElementById('map'),
			      mapOptions);

		    var marker = new google.maps.Marker({
		        position: new google.maps.LatLng(lat, lng),
		        map: map,
		        title: "<?php echo $verein->name;?>"
		    });

		    $( "#mapOverlay" ).fadeOut( "slow" );   		  	
		  }

		}

		google.maps.event.addDomListener(window, 'load', initialize(<?php echo $verein->adresse_lat;?>,<?php echo $verein->adresse_lng;?>));
		</script>

	  </div>
	  
	  <div class="tab-pane" id="member">

	  <?php 

	  if(!$session->isLoggedIn()) echo '<div class="alert alert-danger">Die Mitglieder sind nur für eingeloggte Benutzer sichtbar.</div>';
	  else
	  {
		  foreach($vereinPage->getVereinMembers($verein->id) as $member)
		  { 
		  	if($member->avatar == 0) $avatar = "default.jpg";
		  	else $avatar = $member->id.".jpg";

		  	?>
		  	
			<div class="userInfobox">
				<img class="avatar small" src="design/images/profilbilder/<?php echo $avatar; ?>" alt="Avatar">
				<p>
					<?php 
					if($member->is_visible == 0) echo $member->vorname." ".$member->nachname;
					else echo '<a href="User/'.$member->id.'">'.$member->vorname." ".$member->nachname.'</a>';
					echo '<br>';
					if($member->bis > 0) echo "<span class=\"bis\">Im Verein bis ".$member->bis."</span>";
					else echo "<span>Im Verein seit ".$member->von."</span>";
					?>
				</p>
			</div>

	  <?php
	   }

	  }
	  ?>

	  </div>

	  <div class="tab-pane" id="share">

  		<div data-social-share-privacy='true'></div>

		<script type="text/javascript">(function () {var s = document.createElement('script');var t = document.getElementsByTagName('script')[0];s.type = 'text/javascript';s.async = true;s.src = 'http://panzi.github.io/SocialSharePrivacy/javascripts/jquery.socialshareprivacy.min.autoload.js';t.parentNode.insertBefore(s, t);})();</script>  

	  </div>
	 
	  <?php if($session->isLoggedIn() && $vereinPage->isAdmin($_COOKIE["userID"],$verein->id)) { ?>
	  <div class="tab-pane" id="edit">
		<form method="POST">
			<input type="hidden" name="id" value="<?php echo $verein->id;?>">
			<div class="form-group">
				<label>Vereinsname</label>
				<input name="name" class="form-control" value="<?php echo $verein->name;?>">
			</div>

			<div class="form-group">
				<label>Gründungsjahr</label>
				<input name="gruendung" class="form-control" value="<?php echo $verein->gruendung;?>">
			</div>

			<div class="form-group">
				<label>Sportart</label>
				<select name="sportart" class="form-control">
					<?php 
					$sportarten = get_sportarten(); 

					foreach($sportarten as $sportart)
					{
						if($verein->sportartID == $sportart->id) echo '<option value="'.$sportart->id.'" selected>'.$sportart->name.'</option>';
						else echo '<option value="'.$sportart->id.'">'.$sportart->name.'</option>';
					}

					?>
				</select>
			</div>
			<div class="form-group">
				<label>Adresse</label>
				<div class="form-group">
				<input name="strasse" placeholder="Straße" value="<?php echo $verein->adresse_strasse;?>" class="form-control" style="float:left; width:75%;"> <input name="strasse_nr" placeholder="Nr." value="<?php echo $verein->adresse_strasse_nr;?>" class="form-control" style="width:25%;">
				</div>
				<div class="form-group">
				<input name="plz" placeholder="PLZ" value="<?php echo $verein->adresse_plz;?>" class="form-control" style="float:left; width:25%;"> <input name="ort" placeholder="Ort" value="<?php echo $verein->adresse_ort;?>" class="form-control" style="width:75%;">
				</div>
			</div>
			<input type="submit" name="submit" value="Verein Speichern" />

		</form>

	  </div>
	<?php } ?>

	</div>

</div>


	<?php

}

}

// Keine ID übergeben
// Zurück zur Startseite
else
{
	header('Location: ./');
}

?>

</div>