<!DOCTYPE html>
<html>
  <head>
  <link rel="stylesheet" href="https://bootswatch.com/4/superhero/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <style>
       /* Set the size of the div element that contains the map */
      #map {
        height: 450px;  /* The height is 400 pixels */
        width: 95%;  /* The width is the width of the web page */
        border-style: solid;
        border-color :black; 
        border-radius: 25px;
        border-width: 2px;
        margin: auto;
        margin-top: -15px;
      
       }

      .mark:hover{
        background: rgb(26, 62, 87); 
      }

      .mark{
        background: none;
      }
    </style>
    <title>Check-Ins</title>
    <?php

require_once  "vendor/autoload.php";

$dbHost ='localhost';
$dbPort ='27017';
$client = new MongoDB\Client;
$connection = new MongoDB\Driver\Manager("mongodb://$dbHost:$dbPort");
$query = new MongoDB\Driver\Query([]);

$placeId = $connection->executeQuery('fb.locationDetail', $query);

$id = array();
foreach($placeId as $row){
  if (isset($row->place->location->latitude,$row->place->location->longitude))
  {
  $id[] = $row->place->id;
  }
}

$vals = array_count_values($id);
$most = max($vals);
$least = min($vals);

$tagPlaces = $connection->executeQuery('fb.locationDetail', $query);
foreach($tagPlaces as $tagPlace){ 
  if (isset($tagPlace->place->location->latitude,$tagPlace->place->location->longitude)){
    $lat [] = $tagPlace->place->location->latitude;
    $long []= $tagPlace->place->location->longitude;
    $placeName [] =$tagPlace->place->name;
    $placeId= $tagPlace->place->id;
    $time [] =$tagPlace->created_time;
    $count [] = $vals["$placeId"];

    if(!isset($tagPlace->place->location->city)){
      $city [] = "-";
    }else{
      $city [] = $tagPlace->place->location->city;
    }
  
    if(!isset($tagPlace->place->location->country)){
      $country [] = "-";
    }else{
      $country [] = $tagPlace->place->location->country;
    }  
  }
}

?>


<script type="text/javascript">
    $(window).on('load',function(){
        $('#myModal').modal('show');
    });
    
</script>

<script>


var latitude = <?php echo json_encode($lat);?>;
var longitude = <?php echo json_encode($long);?>;
var placeName = <?php echo json_encode($placeName);?>;
var visitCount = <?php echo json_encode($count);?>;
var visitDate = <?php echo json_encode($time);?>;
var city = <?php echo json_encode($city);?>;
var country = <?php echo json_encode($country);?>;
var most = <?php echo $most;?>;
var least = <?php echo $least;?>;

var dateValue = new Array();
var temp = new Array();
for(var i =0;i<visitDate.length;i++){
    visitDate[i] = visitDate[i].slice(0,10);
    dateValue[i] = Date.parse(visitDate[i]);
    temp[i] = Date.parse(visitDate[i]);
}
  
temp.sort(function(a, b){return b-a});
recent = temp[0];

var visitType = new Array();
for(var i =0;i<temp.length;i++){
  if (visitCount[i] == most)
  {
    visitType[i] = "Most";
  } else if (visitCount [i] == least)
  {
    visitType[i] = "Least";
  }else 
  {
    visitType[i] = "Average";
  }

  if(dateValue[i] == recent)
  {
    visitType[i] = "Recent";
    if (visitCount[i] == most )
      visitType[i] = "Most_Recent";
  }
}

var leastData = markerTypeCount("Least",visitType);
var averageData = markerTypeCount("Average",visitType);
var mostData  = markerTypeCount("Most",visitType);
var recentData = markerTypeCount("Recent",visitType);
var mostRecentData = markerTypeCount("Most_Recent",visitType);

 function markerTypeCount(object,visitType){
   var data = new Array();
   var j =0;
   for(var i = 0; i<visitType.length;i++)
   {
    if(visitType[i] == object){
        data[j] = i;
        j++;
    }
   }
   return data;
 }

function drawMap(data,selection) {

var spanId = ["most","least","average","recent","recent_most","all"];

for(var i =0; i<spanId.length;i++)
{
  if( spanId[i] == selection)
  {
    document.getElementById(spanId[i]).style.backgroundColor = " rgb(49, 58, 146) ";
  }else{
    document.getElementById(spanId[i]).style.backgroundColor = "";
  }
}

var map = new google.maps.Map(
  document.getElementById('map'), {zoom: 2, center: {lat: 5.285153, lng: 100.456238}});
// The marker, positioned at Uluru

var icons = {
       Most : {
        icon: 'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin_star|'+most+'|07E8BB|000000|D82C2C'
      },
       Average : {
        icon:  'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin||FCFC65|000000|'
      },
      Least : {
        icon:  'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin|'+least+'|DF6458|000000'
      },
      Recent : {
        icon:  'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin|R|1758B6|000000'
      },
      Most_Recent : { 
        icon:  'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin_star|'+most+'|1758B6|000000|D82C2C'
      }
    };

var infowindow = new google.maps.InfoWindow();

for (var i= 0; i<=data.length; i++){
    // console.log(data[i]);
    var feature = {position: new google.maps.LatLng(latitude[data[i]], longitude[data[i]]), type: visitType[data[i]]}
    var marker= new google.maps.Marker({position: feature.position, icon: icons[feature.type].icon, map: map , title: placeName[data[i]] });

    google.maps.event.addListener(marker, 'click', (function (marker, i) {
            return function () {
                var contentString  = '<div id="content">'+
  '<div id="siteNotice">'+
  '</div>'+
  '<h2 style = "color:black;" id="firstHeading" class="firstHeading">'+ placeName[data[i]] + '</h2>' +
  '<div id="bodyContent">'+
  '<p style = "color:black;"><b>City</b> : ' + city[data[i]] + '</p>' +
  '<p style = "color:black;"><b>Country</b> : ' + country[data[i]] + '</p>' +
  '<p style = "color:black;"><b>Number of Visit</b> : ' + visitCount[data[i]] + '</p>' +
  '<p style = "color:black;"><b>Visit Date (YYYY-MM-DD)</b> : ' + visitDate[data[i]] + '</p>' +
  '</div>'+
  '</div>';
                infowindow.setContent(contentString);
                infowindow.open(map, marker);
            }
        })(marker, i)); 
    }
  

}


// Initialize and add the map
function initMap() {

var spanId = ["most","least","average","recent","recent_most","all"];

for(var i =0; i<spanId.length;i++)
{
  if( spanId[i] == "all")
  {
    document.getElementById(spanId[i]).style.backgroundColor = " rgb(49, 58, 146) ";
  }else{
    document.getElementById(spanId[i]).style.backgroundColor = "";
  }
}

var map = new google.maps.Map(
  document.getElementById('map'), {zoom: 2, center: {lat: 5.285153, lng: 100.456238}});
// The marker, positioned at Uluru

var icons = {
       Most : {
        icon: 'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin_star|'+most+'|07E8BB|000000|D82C2C'
      },
       Average : {
        icon:  'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin||FCFC65|000000|'
      },
      Least : {
        icon:  'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin|'+least+'|DF6458|000000'
      },
      Recent : {
        icon:  'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin|R|1758B6|000000'
      },
      Most_Recent : { 
        icon:  'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin_star|'+most+'|1758B6|000000|D82C2C'
      }
    };

var infowindow = new google.maps.InfoWindow();

for (var i= 0; i<=latitude.length; i++){
    
    var feature = {position: new google.maps.LatLng(latitude[i], longitude[i]), type: visitType[i]}
    var marker= new google.maps.Marker({position: feature.position, icon: icons[feature.type].icon, map: map , title: placeName[i] });

    google.maps.event.addListener(marker, 'click', (function (marker, i) {
            return function () {
                var contentString  = '<div id="content">'+
  '<div id="siteNotice">'+
  '</div>'+
  '<h2 style = "color:black;" id="firstHeading" class="firstHeading">'+ placeName[i] + '</h2>' +
  '<div id="bodyContent">'+
  '<p style = "color:black;"><b>City</b> : ' + city[i] + '</p>' +
  '<p style = "color:black;"><b>Country</b> : ' + country[i] + '</p>' +
  '<p style = "color:black;"><b>Number of Visit</b> : ' + visitCount[i] + '</p>' +
  '<p style = "color:black;"><b>Visit Date (YYYY-MM-DD)</b> : ' + visitDate[i] + '</p>' +
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
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDycJODMgrTMd6ir8-glqdvhKKRpm0fwjY&callback=initMap">
</script> 

  </head>
  <body >
    <h3 align="center" style ="margin-top:10px;">Check-Ins</h3>
    <!--The div element for the map -->
    
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Guides</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" align="center">
        <p> <span style ="color:black;"><b>Ctrl + Scroll</b></span>    or   <span style ="color:black;"><b>Double left/right click</b></span>  to <span style ="color:black;"><b>zoom</b></span> in/out the map </p>
        <p><span style ="color:black;"><b>Click</b></span> the <span style ="color:black;"><b>marker</b></span>for more in<span style ="color:black;"><b>info</b></span> fo of the visited place</p>
        <p><img src="https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin|1|FFFFFF|000000" alt="Number Indicator">
               <img src="https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin|2|FFFFFF|000000" alt="Number Indicator">
               Number found in markers indicate the visit count</p>  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
       

<div class="jumbotron jumbotron-fluid " style ="max-width: 100%; height:700px;  border-radius: 25px; border-width: 1px; border-style: solid;border-color :black; " >


    <div id="map"></div>
    
    <div class = "container-fluid"  style="margin:auto; margin-top:15px;">
      

      <div class="row" align ="center" style = "margin-bottom:5px; margin-right:25px;">
        <div class="col-sm-6" style ="margin:auto; color:black;">
          <h4><u><b>Click The Markers Below</b></u></h4>
        </div>
      </div>

      <div class="row">

        <div class="col-sm-3" style ="margin-left:100px; color:black;">
          <p><span onclick = "drawMap(mostData,'most')"><img src="https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin_star||07E8BB|000000|D82C2C"  alt="Most Visited Place"><span class="mark" id="most" > Most Visited Places</span></span></p>
          <p><span onclick = "drawMap(recentData,'recent')"><img src="https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin|R|1758B6|000000"  alt="Recently Visited Place"> <span class="mark" id="recent" >Recently Visited Place</span></span></p>
        </div>

        <div class="col-sm-3" style ="margin-left:100px; color:black;">
          <p><span onclick = "drawMap(averageData,'average')"><img src="https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin||FCFC65|000000|"  alt="Regular Visited Places"> <span class="mark" id="average" >Regular Visited Places</span></span></p>
          <p><span onclick = "drawMap(mostRecentData,'recent_most')"><img src="https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin_star||1758B6|000000|D82C2C"  alt="recent_most"><span class="mark" id="recent_most" > Recently & Most Visited Place</span></span></p>       
        </div>

        <div class="col-sm-3" style ="margin-left:90px; color:black;">
            <p><span onclick = "drawMap(leastData,'least')" ><img src="https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin||DF6458|000000" alt="Least Visited Places"><span class="mark" id="least" > Least Visited Places</span></span></p>
            <p><span onclick = "initMap()"><img src="https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin||FFFFFF|000000"  alt="Display All"> <span class="mark" id="all" >Display All Visited Places</span></span></p>  
      </div>
        </div>


      </div>
    </div>


</div>

  </body>
</html>