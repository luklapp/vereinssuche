<?php

/**
 * @author Lukas Klappert <lklappert.mmt-b2013@fh-salzburg.ac.at>
 * Vereinssuche ist ein MultiMediaProjekt 1 des Studiengangs MultimediaTechnology der Fachhochschule Salzburg.
 */

$pagetitle = "Startseite";

// Header Datei einbinden
include("includes/header.php");

$startPage = new StartPage();

?>

<div id="mapWrapper">

  <div id="map"></div>

  <div id="mapOverlay">
    <div class="button">
      <a href="Suche/" data-toggle="modal" data-target="#filterModal" data-remote="false">
        <span class="glyphicon glyphicon-search"></span> Jetzt Verein finden!
      </a>
    </div>
  </div>

</div>

<div id="content" class="container">

	<h1>Startseite</h1>


  
  <div id="startDescription">

    <?php

    if($session->checkLoginStatus()) $startPage->printVereinNews();
    else $startPage->printStartseite();

    ?>


  </div>

<div id="vereinList" style="display:none;">

<a class="button" href="Suche/" data-toggle="modal" data-target="#filterModal" data-remote="false">
  <span class="glyphicon glyphicon-search"></span> Neue Suche starten
</a>

<p>Folgende Vereine haben wir in deiner Nähe gefunden:</p>

  <table id="vereinListTable">
    <thead>
      <tr>
        <td>Vereinsname</td>
        <td>Entfernung in km</td>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>

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
    getOrt(lat,lng);
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

    getOrt(lat,lng);

}

google.maps.event.addDomListener(window, 'load', initialize(lat,lng));


function newMarker(map, lat, lng, titel, id) {
    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(lat, lng),
        map: map,
        title: titel
    });
    google.maps.event.addListener(marker, 'click', function () {
      window.location.href = 'Verein/'+id+'';
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

function getOrt(lat,lng)
{
  $.getJSON( "http://maps.googleapis.com/maps/api/geocode/json?latlng="+lat+","+lng+"&amp;sensor=false", function( data )
    {
     if(data.status == "OK")
     {
      $('#adresse').val(data.results[0].formatted_address);
     }
    });
}

function loadMarkers(radius,sportart,name)
{

  if(name != "") var url = "ajax/ajax.php?lat="+lat+"&lng="+lng+"&name="+name+"&sportart="+sportart;
  else var url = "ajax/ajax.php?lat="+lat+"&lng="+lng+"&radius="+radius+"&sportart="+sportart;
  
    var i = 0;
    var lat1 = 0;
    var lng1 = 0;

    // Damit lat1 + lng1 zurückgegeben werden können
    $.ajaxSetup({'async': false});

    $.getJSON( url , function( data ) {

      $.each(data, function (index, verein) {
         if(i == 0){lat1 = verein.lat; lng1 = verein.lng;}
         newMarker(map,verein.lat,verein.lng,verein.name,verein.id);
         i++;
      });    
  
    });

  return {lat: lat1, lng: lng1};

}

function loadVereinList(radius,sportart,name)
{
  
  $("#startDescription").fadeOut( "fast" );
  $("#vereinList").fadeIn( "slow" );

  if(name != "")
  {
    var url = "ajax/ajax.php?lat="+lat+"&lng="+lng+"&name="+name;
  }
  else
  {
    var url = "ajax/ajax.php?lat="+lat+"&lng="+lng+"&radius="+radius+"&sportart="+sportart;
  }

  $.getJSON( url , function( data ) {
  
    node = document.querySelector("#vereinListTable > tbody");

    while (node.firstChild) {
      node.removeChild(node.firstChild);
    }

    $.each(data, function (index, verein) {

      currentRow = document.createElement("tr");
      currentCell = document.createElement("td"); 
      currentCell2 = document.createElement("td"); 
      
      currentCell.innerHTML = '<a href="Verein/'+verein.id+'">'+verein.name+'</a>';
      currentCell2.innerHTML = verein.distance.substring(0, verein.distance.indexOf('.') + 2) + " km";

      currentRow.appendChild(currentCell);
      currentRow.appendChild(currentCell2);
      node.appendChild(currentRow);

    });
  
  });

}

function loadMap()
{
  var address  = document.getElementById('adresse').value;
  var radius   = document.getElementById('umkreis').value;
  var sportart = document.getElementById('sportart').value;
  var name = document.getElementById('name').value;

  if(radius != parseInt(radius))
  {
    alert("Ungültiger Radius!");
  }
  else if(sportart != parseInt(sportart))
  {
      alert("Ungültige Sportart!");
  }
  else
  {
    
      geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {

            if(radius <= 10) map.setZoom(11);
            else if(radius <= 20) map.setZoom(10);
            else map.setZoom(9);
          
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

          deleteMarkers();
          var pos = loadMarkers(radius,sportart,name);

          if(pos.lat == 0)
            alert("Leider keine Ergebnisse gefunden!");
          else
          {
            if(name == "")
              cityCircle = new google.maps.Circle(circleOptions); 



            if(name != "" && pos.lat != 0 && pos.lng != 0)
              map.setCenter(new google.maps.LatLng(pos.lat,pos.lng));
            else
              map.setCenter(new google.maps.LatLng(lat,lng));

            $( "#mapOverlay" ).fadeOut( "slow" );   

            loadVereinList(radius,sportart,name);            
          }          



        } 
        else 
        {
          if(status == "ZERO_RESULTS") alert("Für diesen Ort wurden leider keine Ergebnisse gefunden.");
          else alert('Fehler: ' + status);
        }
      });    
  }

}
</script>