<?php 

$pagetitle = "Startseite - Vereinssuche";

// Header Datei einbinden
include("includes/header.php");

?>

<div id="mapWrapper">

  <div id="map"></div>

  <div id="mapOverlay">
    <div class="button">
      <a href="#" data-toggle="modal" data-target="#filterModal">
        <span class="glyphicon glyphicon-search"></span> Jetzt Verein suchen!
      </a>
    </div>
  </div>

</div>

<div id="content" class="container">

	<h1>Startseite</h1>

  <?php 

  /*

  $db = Database::getDB();

  $lat = 47.723818;
  $lng = 13.0869397;

  $sth = $db->prepare("SELECT name, SQRT((111.3 * cos(RADIANS((adresse_lat+".$lat.")/2)) * (adresse_lng - ".$lng.")) * (111.3 * cos(RADIANS((adresse_lat+".$lat.")/2)) * (adresse_lng - ".$lng.")) + (111.3 * (adresse_lat - ".$lat."))*(111.3 * (adresse_lat - ".$lat."))) as distance FROM vs_vereine WHERE adresse_lat != '' && adresse_lng != ''");

  $sth->execute();
  $result = $sth->fetchAll();

  echo '<pre>';
  var_dump($result);
  echo '</pre>';

  $sth = $db->prepare("SELECT name, SQRT( (71.5 * (adresse_lng - ".$lng."))*(71.5 * (adresse_lng - ".$lng.")) + (111.3 * (adresse_lat - ".$lat."))*(111.3 * (adresse_lat - ".$lat."))                      ) as distance FROM vs_vereine WHERE adresse_lat != '' && adresse_lng != ''");

  $sth->execute();
  $result = $sth->fetchAll();

  echo '<pre>';
  var_dump($result);
  echo '</pre>';

    $sth = $db->prepare("SELECT name, (6367.41 * SQRT(2*(1-cos(RADIANS(adresse_lat)) * cos(RADIANS($lat)) * (sin(RADIANS(adresse_lng)) * sin(RADIANS($lng)) + cos(RADIANS(adresse_lng)) * cos(RADIANS($lng))) - sin(RADIANS(adresse_lat)) * sin(RADIANS($lat))))) as distance FROM vs_vereine WHERE adresse_lat != '' && adresse_lng != ''");

  $sth->execute();
  $result = $sth->fetchAll();

  echo '<pre>';
  var_dump($result);
  echo '</pre>';

  */

  ?>

</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="Vereinssuche" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Vereinssuche</h4>
      </div>
      <div class="modal-body">
        <input id="adresse" name="adresse" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="javascript:loadMap(15);">Suche starten</button>
      </div>
    </div>
  </div>
</div>

<script>
  
  var lat = 47.723818;
  var lng = 13.0869397;
  var map;
  var markers = [];
  var geocoder;
  var cityCircle;

  if (navigator.geolocation)
  {
      navigator.geolocation.getCurrentPosition(getPosition);
  }
  else
  {
    initialize(lat,lng); 
  }

  function getPosition(position){
    lat = position.coords.latitude;
    lng = position.coords.longitude;
    map.setCenter(new google.maps.LatLng(lat,lng));
  }

function initialize(lat,lng) {
  geocoder = new google.maps.Geocoder();

  var latLng  = new google.maps.LatLng(lat,lng);

  var mapOptions = {
    zoom: 10,
    center: latLng
  };
  map = new google.maps.Map(document.getElementById('map'),
      mapOptions);

    var circleOptions = {
      strokeColor: '#888888',
      strokeOpacity: 0,
      strokeWeight: 2,
      fillColor: '#888888',
      fillOpacity: 0,
      map: map,
      center: latLng,
      radius: 0
    }

    cityCircle = new google.maps.Circle(circleOptions);	

}

google.maps.event.addDomListener(window, 'load', initialize(lat,lng));


function newMarker(map, lat, lng, titel, id) {
    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(lat, lng),
        map: map,
        title: titel
    });
    google.maps.event.addListener(marker, 'click', function () {
      window.location.href = 'Verein/'+id+'/';
    });    
    markers.push(marker);
}

function deleteMarkers()
{
  if (markers) {
    for (i in markers) {
      markers[i].setMap(null);
    }
    markers = [];
  }  
}

</script>

<script>

function loadMarkers(radius)
{

console.log("Load Markers: " + lat + ", " + lng + " im Umkreis von " + radius);

$.getJSON( "ajax/ajax.php?lat="+lat+"&lng="+lng+"&radius="+radius, function( data ) {
  
  $.each(data, function (index, verein) {
     newMarker(map,verein.lat,verein.lng,verein.name,verein.id);
  });

});

}

function loadMap(radius)
{
  var address = document.getElementById('adresse').value;
  geocoder.geocode( { 'address': address}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      
      lat = results[0].geometry.location.lat();
      lng = results[0].geometry.location.lng();

      cityCircle.setMap(null);

      var circleOptions = {
        strokeColor: '#888888',
        strokeOpacity: 0.4,
        strokeWeight: 2,
        fillColor: '#888888',
        fillOpacity: 0.1,
        map: map,
        center: new google.maps.LatLng(lat,lng),
        radius: radius*1000
      }

      cityCircle = new google.maps.Circle(circleOptions); 

      map.setCenter(new google.maps.LatLng(lat,lng));

      deleteMarkers();
      loadMarkers(radius);
      
      $( "#mapOverlay" ).fadeOut( "slow" );    
    } 
    else 
    {
      if(status == "ZERO_RESULTS") alert("Für diesen Ort wurden leider keine Ergebnisse gefunden.");
      else alert('Geocode was not successful for the following reason: ' + status);
    }
  });


}


</script>

<?php

try {
$db = Database::getDB();	
}
catch(Exception $e)
{
	echo $e;
}

?>