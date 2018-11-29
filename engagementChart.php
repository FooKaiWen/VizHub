<!DOCTYPE html>
<html lang="en">
<head>
  <title>VizHub</title>
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
$likedata = $connection->executeQuery('fb.post_detail', $query);

$likearray = array();
$timearray = array();
$typearray = array();
$temporary = 0;

foreach ($likedata as $row) {   
    // $likearray [] = [];
    // echo $row->message;
    $likearray [] = $row->like->summary->total_count;
    $temporary = $row->like->summary->total_count;
    // print($temporary . " ");
    
    // print($row->like->summary->total_count);
    // print($highestLikes);
    // print(" ");
    $timearray [] = $row->created_time;
    $typearray [] = $row->type;
}

$query = new MongoDB\Driver\Query([]);
$reactdata = $connection->executeQuery('fb.post_detail', $query);

$lovearray = array();
$hahaarray = array();
$wowarray = array();
$sadarray = array();
$angryarray = array();
$num_comment = array();
$num_share = array();
$post_type = array();
$i = 0;
foreach($reactdata as $row){
    $lovearray [] = $row->love->summary->total_count;
    $hahaarray [] = $row->haha->summary->total_count;
    $wowarray [] = $row->wow->summary->total_count;
    $sadarray [] = $row->sad->summary->total_count;
    $angryarray [] = $row->angry->summary->total_count;
    $num_comment [] = $row->comments->summary->total_count;
    if($i < 50){
        $post_type [] = $row->status_type;
    }
    $i++;
    // print($row->status_type . " ");
    if(!isset($row->shares)){
            $num_share [] = 0;
        }
        else 
        {
            $num_share [] = $row->shares->count;           
        }
}

$post_type_count = array_count_values($post_type);
$postdetails = $connection->executeQuery('fb.post_detail', $query);
foreach($post_type_count as $type => $count){
    $postType [] = $type;
    $postCount [] = $count;
}

$user_details = $connection->executeQuery('fb.userdetail', $query);

foreach($user_details as $row){

    $num_friends = $row->friends->summary->total_count;    
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
    <input type="checkbox" id="togAllBtn" onclick='plotAll("chart",<?php echo json_encode($likearray) ?>,<?php echo json_encode($num_comment) ?>,<?php echo json_encode($num_share) ?>, <?php echo json_encode($timearray)?>, <?php echo json_encode($typearray)?>)'>
    <div class="slider round">
        <span class="on">Reach</span><span class="off">Reach</span>
    </div>
</label>

<!-- <div style="width:25%;">
    <select id ="topReactId" name="topReactId" 
    onchange="plotTop('chart','<?php echo json_encode($likearray) ?>,<?php echo json_encode($lovearray) ?>,<?php echo json_encode($hahaarray) ?>,<?php echo json_encode($wowarray) ?>,<?php echo json_encode($sadarray) ?>,<?php echo json_encode($angryarray)?>,<?php echo json_encode($timearray)?>')">
    <option value="">Select One...</option>
    <option value="5">5</option>
    <option value="10">10</option>
    <option value="15">15</option>
    </select>
</div> -->

<label class="switch">
    <input type="checkbox" id="togTotBtn" onclick='plotTotal("chart",<?php echo json_encode($lovearray) ?>,<?php echo json_encode($hahaarray) ?>,<?php echo json_encode($wowarray) ?>,<?php echo json_encode($sadarray) ?>,<?php echo json_encode($angryarray)?>,<?php echo json_encode($timearray)?>)'> 
    <div class="slider round">
        <span class="on">Other Reaction </span><span class="off">Other Reaction</span>
    </div>
</label>

<!-- <label class="switch">
    <input type="checkbox" id="togFriBtn" onclick='plotFriend("chart",<?php echo json_encode($likearray) ?>,<?php echo json_encode($lovearray) ?>,<?php echo json_encode($hahaarray) ?>,<?php echo json_encode($wowarray) ?>,<?php echo json_encode($sadarray) ?>,<?php echo json_encode($angryarray)?>,<?php echo json_encode($timearray)?>,<?php echo json_encode($num_friends)?>)'>
    <div class="slider round">
        <span class="on">Friend</span><span class="off">Friend</span>
    </div>
</label> -->

<label class="switch">
    <input type="checkbox" id="togTypBtn" onclick='plotType("chart",<?php echo json_encode($typearray) ?>,<?php echo json_encode($timearray) ?>)'>

    <div class="slider round">
        <span class="on">Type of Post</span><span class="off">Type of Post</span>
    </div>
</label>

<label class="switch">
    <input type="checkbox" id="togTypeBtn" onclick='plotType("chart",<?php echo json_encode($postCount) ?>,<?php echo json_encode($postType)?>)'>
    <div class="slider round">
        <span class="on">Post Type</span><span class="off">Post Type</span>
    </div>
</label>

<!-- <div style="width:25%;">
<label>Time Selection: </label>

<?php
echo '<select id="timeSelect" onchange=  style=" height: 25px; width: 100px">';

foreach($timearray as $time){
    echo '<option value="' . htmlspecialchars($time) . '">'
        . htmlspecialchars($time) . '</option>';
}
echo '</select> ';
?>
</div> -->

<p>We are still improving our visualization functionality!</p>

<<<<<<< HEAD

<div class ="informMessage"  >
<div id ="topInfo" style = "display:none;">
<button class="tabcontent" >1st</button>
<button class="tabcontent">2nd</button>
<button class="tabcontent">3rd</button>
<button class="tabcontent">4th</button>
<button class="tabcontent">5th</button>

</div>


=======
<label for="informMessage" class="title"><i>Insight</i></label>
<div class ="informMessage">
    <div id ="topInfo" style="display:none;">
        <button class="tabcontent">1st</button>
        <button class="tabcontent">2nd</button>
        <button class="tabcontent">3rd</button>
        <button class="tabcontent">4th</button>
        <button class="tabcontent">5th</button>
    </div>
>>>>>>> 4725aca6256f9ad79d64bb898d65c3117e6bb3cc
</div>



<script type="text/javascript" src="chart.js"></script>
</body>
</html>