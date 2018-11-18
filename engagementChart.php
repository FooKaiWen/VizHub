<!DOCTYPE html>
<html lang="en">
<head>
  <title>VizHub</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="chart.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
  <script src="http://cdnjs.cloudflare.com/ajax/libs/p5.js/0.5.6/p5.js"></script>
  <script src ="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
  <script src="sketch.js"></script>  
  <script type="text/javascript" src="chart.js"></script>
</head>
<body>

<?php
// session_start();
// $likearray = $_SESSION['likes'];
// $lovearray = $_SESSION["love"];
// $hahaarray = $_SESSION["haha"];
// $wowarray = $_SESSION["wow"];
// $sadarray = $_SESSION["sad"];
// $angryarray = $_SESSION["angry"];
// $timearray = $_SESSION['time'];
?>

<?php

require_once  "vendor/autoload.php";

$dbhost ='localhost';
$dbport ='27017';

$client = new MongoDB\Client;
$connection = new MongoDB\Driver\Manager("mongodb://$dbhost:$dbport");

$query = new MongoDB\Driver\Query([]);
$likedata = $connection->executeQuery('fb.post', $query);

$likearray = array();
$timearray = array();

foreach ($likedata as $row) {
    $likearray [] = [];
    // echo $row->message;
    $likearray [] = $row->like->summary->total_count;
    $timearray [] = $row->created_time;
}

$query = new MongoDB\Driver\Query(['created_time'=>'2017-02-10T14:25:34+0000']);
$reactdata = $connection->executeQuery('fb.post', $query);

foreach($reactdata as $row){
    $lovearray = $row->love->summary->total_count;
    $hahaarray = $row->haha->summary->total_count;
    $wowarray = $row->wow->summary->total_count;
    $sadarray = $row->sad->summary->total_count;
    $angryarray = $row->angry->summary->total_count;
}
?>

<div style="height: 500px;width: 50%;background-color: azure;float:right;">
<canvas id="chart"float="right"></canvas>
</div>

<p>Toggle for graph!</p>
<label class="switch">
    <input type="checkbox" id="togLBtn" onclick='plot("chart",<?php echo json_encode($likearray) ?>,<?php echo json_encode($timearray) ?>)'>
    <div class="slider round">
        <span class="on">Like</span><span class="off">Like</span>
    </div>
</label>

<div style="width:25%;">
    <select>
    <option value="5">5</option>
    <option value="10">10</option>
    <option value="15">15</option>
    <option value="20">20</option>
    </select>
</div>

<label class="switch">
    <input type="checkbox" id="togRBtn" onclick='plotReaction("chart",<?php echo json_encode($likearray) ?>,<?php echo json_encode($lovearray) ?>,<?php echo json_encode($hahaarray) ?>,<?php echo json_encode($wowarray) ?>,<?php echo json_encode($sadarray) ?>,<?php echo json_encode($angryarray) ?>)'>
    <div class="slider round">
        <span class="on">Reaction</span><span class="off">Reaction</span>
    </div>
</label>

<div style="width:25%;">
<?php
echo '<select id="timeSelect">';
foreach($timearray as $time){
    echo '<option value="' . htmlspecialchars($time) . '">'
        . htmlspecialchars($time) . '</option>';
}
echo '</select>';
?>
</div>

<p>We are still improving our visualization functionality!</p>

</body>
</html>