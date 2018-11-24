<!DOCTYPE html>
<html>
<head>

 <title>VizHub</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- <link rel="stylesheet" type="text/css" href="design.css">
  <link rel="stylesheet" href="https://bootswatch.com/4/superhero/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="chart.js"></script> -->




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
  $lat = $tagplace->place->location->latitude;
  $long = $tagplace->place->location->longitude;
  $place_name = $tagplace->place->name;
  $p_id= $tagplace->place->id;
  $count = $vals["$p_id"];

  if ($count == $most){
    $marker = "Most";
  } elseif ($count == $least){
    $marker = "Least";
  }else{
    $marker = "Average";
  }

  $temp_Holder = array ($lat,$long,$place_name,$marker);
  array_push($big,$temp_Holder);

}
// print_r($vals);
// print_r ($big);
?>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['map'],
      // Note: you will need to get a mapsApiKey for your project.
      // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
      'mapsApiKey': ''
      // 'mapsApiKey': 'AIzaSyDsBbRh_m8mtqypfsC0LDKZz8OHKqDlLXU' // I included my testing project API, by FKW
    });
    google.charts.setOnLoadCallback(drawMap);

    function drawMap () {
      
      var tempArray = <?php echo json_encode($big);?>;
      var tempCount = <?php print_r (json_encode($vals));?>;
      var most = <?php echo $most;?>;
      var least = <?php echo $least;?>;

      // var o = tempCount;
      // var count = [];
      // for(var i in o)
      // {
      //    count.push(o[i]);
      // }

      // function removeDuplicateUsingSet(arr){
      //   let unique_array = Array.from(new Set(arr))
      //   return unique_array
      // }
      
      // count.sort(function(a, b){return b-a});
      // count = removeDuplicateUsingSet(count);
      

      var data = new google.visualization.DataTable();
      data.addColumn('number', 'Long');
      data.addColumn('number', 'Lat');
      data.addColumn('string', 'Name');
      data.addColumn('string', 'Marker')
  
      data.addRows(tempArray);
     
      
      var options = {
        zoomLevel: 6,
        showTooltip: true,
        showInfoWindow: true,
        useMapTypeControl: true,
        icons: {
           Most : {
            normal:   'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin_star|'+most+'|07E8BB|000000|D82C2C',
            selected: 'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin_star|'+most+'|294CE6|000000|31E425',
          },
           Average : {
            normal:    'https://chart.googleapis.com/chart?chst=d_map_spin&chld=0.5|0|FCFC65|12|b|2',
            selected:  'https://chart.googleapis.com/chart?chst=d_map_spin&chld=0.5|0|FCC300|12|b|2',
          },
          Least : {
            normal:    'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin|'+least+'|DF6458|000000',
            selected:  'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld=pin|'+least+'|E32A0B|000000',
          }
        }
      };
      
      var myLatlng = {lat: 5.285153, lng: 100.456238};
      var map = new google.visualization.Map(document.getElementById('map_div'),{
        center: myLatlng
      });

      map.draw(data, options);
    }
  </script>

</head>
<body>
  <p>We put markers on where you have tagged in Facebook and now you know it.</p>
  <p>Scroll to look for the markers!</p>
  <div id="map_div" style="height: 500px; width: 100%"></div>
  <div>
    <p>We are still improving our visualization functionality!</p>
</div>
</body>
</html>

    