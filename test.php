<!DOCTYPE html>
<html>
  <head>
    <style>
       /* Set the size of the div element that contains the map */
      #map {
        height: 400px;  /* The height is 400 pixels */
        width: 100%;  /* The width is the width of the web page */
       }
    </style>
    <?php

require_once  "vendor/autoload.php";

$dbhost ='localhost';
$dbport ='27017';
$client = new MongoDB\Client;
$connection = new MongoDB\Driver\Manager("mongodb://$dbhost:$dbport");
$query = new MongoDB\Driver\Query([]);

$place_id = $connection->executeQuery('fb.place', $query);
$id = array();

foreach($place_id as $row){
  $id[] = $row->place->id;
}

$vals = array_count_values($id);
$most = max($vals);
$least = min($vals);

$tagplaces = $connection->executeQuery('fb.place', $query);
$big = array();

foreach($tagplaces as $tagplace){
  $lat [] = $tagplace->place->location->latitude;
  $long []= $tagplace->place->location->longitude;
  $place_name [] =$tagplace->place->name;
  $p_id= $tagplace->place->id;
  $count [] = $vals["$p_id"];

//   if ($count == $most){
//     $marker = "Most";
//   } elseif ($count == $least){
//     $marker = "Least";
//   }else{
//     $marker = "Average";
//   }

//   $temp_Holder = array ($lat,$long,$place_name,$count);
//   array_push($big,$temp_Holder);

}
// echo json_encode($lat);
// echo json_encode($place_name);
?>

  </head>
  <body onload="initMap()">
    <h3>My Google Maps Demo</h3>
    <!--The div element for the map -->
    <div id="map"></div>
    <script>


    var latitude = <?php echo json_encode($lat);?>;
    var longitude = <?php echo json_encode($long);?>;
    var place_name = <?php echo json_encode($place_name);?>;
    var visit_count = <?php echo json_encode($count);?>;

    // console.log(place_name);
    // console.log(visit_count);
    

// Initialize and add the map
function initMap() {
  // The location of Uluru
  var uluru = {lat: -25.344, lng: 131.036};
  var sohai = {lat: -35.344, lng: 145.036};
  // The map, centered at Uluru
  var map = new google.maps.Map(
      document.getElementById('map'), {zoom: 4, center: {lat: 5.285153, lng: 100.456238}});
  // The marker, positioned at Uluru



//  var contentString  = '<div id="content">'+
//       '<div id="siteNotice">'+
//       '</div>'+
//       '<h1 id="firstHeading" class="firstHeading">'+ place_name[i] + '</h1>' +
//       '<div id="bodyContent">'+
//       '<p>Number of Visit : ' + visit_count[i] + '</p>' +
//       '</div>'+
//       '</div>';
    var infowindow = new google.maps.InfoWindow();

    for (var i= 0; i<=latitude.length; i++){
        
        var marker= new google.maps.Marker({position: {lat: latitude[i], lng: longitude[i]}, map: map , title: place_name[i] });
        // content = place_name[i] + "\n" + " hi"

        google.maps.event.addListener(marker, 'click', (function (marker, i) {
                return function () {
                    var contentString  = '<div id="content">'+
      '<div id="siteNotice">'+
      '</div>'+
      '<h2 id="firstHeading" class="firstHeading">'+ place_name[i] + '</h2>' +
      '<div id="bodyContent">'+
      '<p>Number of Visit : ' + visit_count[i] + '</p>' +
      '</div>'+
      '</div>';
                    infowindow.setContent(contentString);
                    infowindow.open(map, marker);
                }
            })(marker, i)); 
        }


       
    
    // console.log(place_name);
    // console.log(marker);
    // marker[i].addListener('click', function() {infowindow.open(map, marker[i])});
//    marker[0].addListener('click', function() {
//     infowindow.open(map, marker[0]);
//   });
  
//   marker[1].addListener('click', function() {
//     infowindow1.open(map, marker[1]);
//   });



//   var markerCluster = new MarkerClusterer(map, marker,
//             {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
     

}
    </script>
    <!--Load the API from the specified URL
    * The async attribute allows the browser to render the page while the API loads
    * The key parameter will contain your own API key (which is not needed for this tutorial)
    * The callback parameter executes the initMap() function
    -->

<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js">
</script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDsBbRh_m8mtqypfsC0LDKZz8OHKqDlLXU&callback=initMap">
    </script>

  </body>
</html>