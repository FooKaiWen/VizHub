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
session_start();
$likearray = $_SESSION['likes'];
$lovearray = $_SESSION["love"];
$hahaarray = $_SESSION["haha"];
$wowarray = $_SESSION["wow"];
$sadarray = $_SESSION["sad"];
$angryarray = $_SESSION["angry"];
$timearray = $_SESSION['time'];
?>

<p>Toggle for graph!</p>
<label class="switch">
    <input type="checkbox" id="togLBtn" onclick='plot("chart",<?php echo json_encode($likearray) ?>,<?php echo json_encode($timearray) ?>)'>
    <div class="slider round">
        <span class="on">Like</span><span class="off">Like</span>
    </div>
</label>

<div style="height: 500px;width: 50%;background-color: azure;float:right;">
<canvas id="chart"float="right"></canvas>
</div>

<p> </p>
<label class="switch">
    <input type="checkbox" id="togRBtn" onclick='plotReaction("chart",<?php echo json_encode($likearray) ?>,<?php echo json_encode($lovearray) ?>,<?php echo json_encode($hahaarray) ?>,<?php echo json_encode($wowarray) ?>,<?php echo json_encode($sadarray) ?>,<?php echo json_encode($angryarray) ?>)'>
    <div class="slider round">
        <span class="on">Reaction</span><span class="off">Reaction</span>
    </div>
</label>

    <p>We are still improving our visualization functionality!</p>
</div>
</body>
</html>