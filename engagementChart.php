<!DOCTYPE html>
<html lang="en">
<head>
  <title>Engagement</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="chart.css">
  <link rel="stylesheet" href="https://bootswatch.com/4/superhero/bootstrap.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
  <script src="http://cdnjs.cloudflare.com/ajax/libs/p5.js/0.5.6/p5.js"></script>
  <script src ="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script> 
  
</head>
<body>
    <h3 align="center" style ="margin-top:10px;">Engagement Visualization</h3>
    <hr style="border-width:7px; border-color:black;">

<?php

require_once  "vendor/autoload.php";

$dbHost ='localhost';
$dbPort ='27017';

$client = new MongoDB\Client;
$connection = new MongoDB\Driver\Manager("mongodb://$dbHost:$dbPort");

$query = new MongoDB\Driver\Query([]);
$postDetail = $connection->executeQuery('fb.postDetail', $query);

$likeArray = array();
$timeArray = array();
$typeArray = array();
$loveArray = array();
$hahaArray = array();
$wowArray = array();
$sadArray = array();
$angryArray = array();
$numComment = array();
$numShare = array();
$numType = array();
$i = 0;
$linkCount = 0;
$photoCount = 0;
$statusCount = 0;
$videoCount = 0;
$eventCount = 0;
foreach ($postDetail as $data) {   
    $likeArray [] = $data->like->summary->total_count;
    $timeArray [] = $data->created_time;

    $loveArray [] = $data->love->summary->total_count;
    $hahaArray [] = $data->haha->summary->total_count;
    $wowArray [] = $data->wow->summary->total_count;
    $sadArray [] = $data->sad->summary->total_count;
    $angryArray [] = $data->angry->summary->total_count;
    $numComment [] = $data->comments->summary->total_count;
    if($i < 50){
        $numType [] = $data->type;
    }
    $i++;
    if(!isset($data->shares)){
        $numShare [] = 0;
    } else {
        $numShare [] = $data->shares->count;           
    }

    if($data->type == "link"){
        $linkCount += $data->like->summary->total_count;
    } elseif($data->type == "photo"){
        $photoCount += $data->like->summary->total_count;
    } elseif($data->type == "status"){
        $statusCount += $data->like->summary->total_count;
    } elseif($data->type == "video"){
        $videoCount += $data->like->summary->total_count;
    } elseif($data->type == "event"){
        $eventCount += $data->like->summary->total_count;
    }
}

$postTypeCount = array_count_values($numType);

foreach($postTypeCount as $type => $count){
    $postType [] = $type;
    $postCount [] = $count;
}

$userDetails = $connection->executeQuery('fb.userDetail', $query);

foreach($userDetails as $data){
    $numFriends = $data->friends->summary->total_count;    
}

?>


<label for="triggerMessage" class="title"><i>Info</i></label>
<div class="triggerMessage" id="triggerMessage" >**Try CLICK on the Parameter !!
    <p id="chartInfo"></p>
</div> 

<label for="" class="title"><i>Chart</i></label>
<div class ="plot">
    <canvas id="chart" float="right" width="300" height="150" ></canvas>
</div>

<h5 style="margin-left:15px;width:25%;">Toggle for graph!</h5>

<div class="numselect">
    <select id="selected">
        <option value="10">10</option>
        <option value="20">20</option>
        <option value="30">30</option>
        <option value="40">40</option>
        <option value="50" selected>50</option>
    </select>
</div>

<label class="switch">
    <input type="checkbox" id="togAllBtn" onclick='friendNumber(<?php echo json_encode($numFriends) ?>);plotReachChart("chart",<?php echo json_encode($likeArray) ?>,<?php echo json_encode($numComment) ?>,<?php echo json_encode($numShare) ?>, <?php echo json_encode($timeArray)?>);'>
    <div class="slider round">
        <span class="on">Reach</span><span class="off">Reach</span>
    </div>
</label>

<label class="switch">
    <input type="checkbox" id="togTotBtn" onclick='plotReactChart("chart",<?php echo json_encode($loveArray) ?>,<?php echo json_encode($hahaArray) ?>,<?php echo json_encode($wowArray) ?>,<?php echo json_encode($sadArray) ?>,<?php echo json_encode($angryArray)?>,<?php echo json_encode($timeArray)?>)'> 
    <div class="slider round">
        <span class="on">Other Reaction </span><span class="off">Other Reaction</span>
    </div>
</label>

<label class="switch">
    <input type="checkbox" id="togTypBtn" onclick='sortHighestCount(<?php echo json_encode($linkCount) ?>,<?php echo json_encode($videoCount) ?>,<?php echo json_encode($photoCount) ?>,<?php echo json_encode($eventCount) ?>,<?php echo json_encode($statusCount) ?>,"postType");plotPostTypeChart("chart",<?php echo json_encode($postCount) ?>,<?php echo json_encode($postType)?>)'>
    <div class="slider round">
        <span class="on">Post Type</span><span class="off">Post Type</span>
    </div>
</label>

<p>We are still improving our visualization functionality!</p>


<label for="informMessage" class="title"><i>Insight</i></label>
<div class ="informMessage"  >
    <div id ="topInfo" style = "display:none;">
    </div>
</div>

<script type="text/javascript" src="chart.js"></script>
</body>
</html>