<!DOCTYPE html>
<html>
  <head>
  <link rel="stylesheet" href="https://bootswatch.com/4/superhero/bootstrap.min.css">
    <style>
       /* Set the size of the div element that contains the map */
      #map {
        height: 400px;  /* The height is 400 pixels */
        width: 100%;  /* The width is the width of the web page */
        border-style: solid;
        border-color :black; 
        border-radius: 25px;
        border-width: 1px;
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
  $time [] =$tagplace->created_time;
  $city [] = $tagplace->place->location->city;
  $country[] = $tagplace->place->location->country;

  $count [] = $vals["$p_id"];
  $count1 =$vals["$p_id"];
    if ($count1 == $most){
        $marker[] = "Most";
    } elseif ($count1 == $least){
        $marker[] = "Least";
    }else{
        $marker[] = "Average";
    }

}

if ($marker[sizeof($marker)-1] == "Most")
{
    $marker[sizeof($marker)-1] = "Most_Recent";
}
else{
    $marker[sizeof($marker)-1] = "Recent";
}

?>

 <script>


var latitude = <?php echo json_encode($lat);?>;
var longitude = <?php echo json_encode($long);?>;
var place_name = <?php echo json_encode($place_name);?>;
var visit_count = <?php echo json_encode($count);?>;
var visit_type = <?php echo json_encode($marker);?>;
var visit_date = <?php echo json_encode($time);?>;
var city = <?php echo json_encode($city);?>;
var country = <?php echo json_encode($country);?>;


for(var i =0;i<visit_date.length;i++){
    visit_date[i] = visit_date[i].slice(0,10);
}

console.log(visit_date);
var most = <?php echo $most;?>;
var least = <?php echo $least;?>;

// Initialize and add the map
function initMap() {

var map = new google.maps.Map(
  document.getElementById('map'), {zoom: 4, center: {lat: 5.285153, lng: 100.456238}});
// The marker, positioned at Uluru

var icons = {
       Most : {
        icon: 'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin_star|'+most+'|07E8BB|000000|D82C2C'
      },
       Average : {
        icon:  'https://chart.googleapis.com/chart?chst=d_map_spin&chld=0.5|0|FCFC65|12|b|2'
      },
      Least : {
        icon:  'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin|'+least+'|DF6458|000000'
      },
      Recent : {
        icon:  'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin|'+least+'|1758B6|000000'
      },
      Most_Recent : { 
        icon:  'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin_star|'+most+'|1758B6|000000|D82C2C'
      }
    };

var infowindow = new google.maps.InfoWindow();

for (var i= 0; i<=latitude.length; i++){
    
    var feature = {position: new google.maps.LatLng(latitude[i], longitude[i]), type: visit_type[i]}
    var marker= new google.maps.Marker({position: feature.position, icon: icons[feature.type].icon, map: map , title: place_name[i] });

    google.maps.event.addListener(marker, 'click', (function (marker, i) {
            return function () {
                var contentString  = '<div id="content">'+
  '<div id="siteNotice">'+
  '</div>'+
  '<h2 style = "color:black;" id="firstHeading" class="firstHeading">'+ place_name[i] + '</h2>' +
  '<div id="bodyContent">'+
  '<p style = "color:black;">City : ' + city[i] + '</p>' +
  '<p style = "color:black;">Country : ' + country[i] + '</p>' +
  '<p style = "color:black;">Number of Visit : ' + visit_count[i] + '</p>' +
  '<p style = "color:black;">Date (Last Visit) : ' + visit_date[i] + '</p>' +
  '</div>'+
  '</div>';
                infowindow.setContent(contentString);
                infowindow.open(map, marker);
            }
        })(marker, i)); 

    }
}
</script>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDsBbRh_m8mtqypfsC0LDKZz8OHKqDlLXU&callback=initMap">
</script>

  </head>
  <body >
    <h3>My Google Maps Demo</h3>
    <!--The div element for the map -->
    
        
       

<div class="jumbotron jumbotron-fluid" style ="max-width: 100%;">
  <div class="container" style ="max-width: 100%;" >
    <div id="map"></div>
  </div>
</div>

   

  </body>
</html>