<!DOCTYPE html>
<html>
<head>
<?php
session_start();
$place = $_SESSION["location"];
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
      
      var tempArray = <?php echo json_encode($place);?>;
      
      
      var data = new google.visualization.DataTable();
      data.addColumn('number', 'Long');
      data.addColumn('number', 'Lat');
      data.addColumn('string', 'Name')
  
      data.addRows(tempArray);
      var url = 'https://icons.iconarchive.com/icons/icons-land/vista-map-markers/48/';

      var options = {
        zoomLevel: 2,
        showTooltip: true,
        showInfoWindow: true,
        useMapTypeControl: true,
        icons: {
          blue: {
            normal:   url + 'Map-Marker-Ball-Azure-icon.png',
            selected: url + 'Map-Marker-Ball-Right-Azure-icon.png'
          },
          green: {
            normal:   url + 'Map-Marker-Push-Pin-1-Chartreuse-icon.png',
            selected: url + 'Map-Marker-Push-Pin-1-Right-Chartreuse-icon.png'
          },
          pink: {
            normal:   url + 'Map-Marker-Ball-Pink-icon.png',
            selected: url + 'Map-Marker-Ball-Right-Pink-icon.png'
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

    