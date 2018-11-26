<?php

require_once  "vendor/autoload.php";

$dbhost ='localhost';
$dbport ='27017';

$client = new MongoDB\Client;
$connection = new MongoDB\Driver\Manager("mongodb://$dbhost:$dbport");
$query = new MongoDB\Driver\Query([]);

$userprofdatas = $connection->executeQuery('fb.userprofile', $query);

foreach($userprofdatas as $userprofdata){
$url = $userprofdata->data->url;
}

$userdatas = $connection->executeQuery('fb.userdetail', $query);

foreach($userdatas as $userdata){
$name = $userdata->name;
$id = $userdata->id;
}

$newdb = $client->selectDatabase('fb');
$temp = $newdb->selectCollection('predictMessage');
$temp->drop();
$messagecol = $newdb->selectCollection('predictMessage');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>VizHub</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="design.css">
  <link rel="stylesheet" href="https://bootswatch.com/4/superhero/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="chart.js"></script>
</head>
<body>

 <script>
      function copyText() {
        var copyText = document.getElementById("message");
        copyText.select();
        document.execCommand("copy");
        alert("Copied the text: " + copyText.value);
      }
</script>

<div class="alert alert-dismissible alert-success">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>Well done!</strong> You successfully logged in! 👍</a>.
</div>

<div class="jumbotron jumbotron-fluid">
  <div class="container">
    <img src="<?php echo $url; ?>" alt="Profile Picture">
    <h2 id="detail-name">Name: <?php echo $name; ?><br/></h2>
    <h2 id="detail-id">ID: <?php echo $id; ?></h2>
  </div>
</div>

<div style="width: 50%; float:left; height:30px;">
  <p>Click the button below to view the number of likes of your recent Facebook posts!</p>
</div>
<div style="width: 50%; float:right; height:30px;">
  <p>Click the button below to view your Facebook tagged places in Google Maps!</p>
</div>

<form action="engagementChart.php" target="_blank">
  <button style="width: 50%; float:left; height:150px; background:rgb(78, 210, 214); margin:0px">Engagement Visualization</button>
</form>
<form action="MapChart.php" target="_blank">
  <button style="width: 50%; float:right; height:150px; background:rgb(184, 184, 41); margin:0px">Location Vizualization</button>
</form>

<form method="POST" action="">


<div class="container">
    <div class="form-group" >
      <label for="Message" style="margin-top :15px;"><i>Message:</i></label>
      <textarea class="form-control" style ="border: 3px solid rgb(47, 52, 78); " name="predictM" rows="3" id="message" placeholder="Type Your Message Here For Like Prediction . . . . . ."></textarea>
      <div style ="text-align:center;">  
        <button class ="copyText" onclick="copyText()">Copy text</button>
        <button class ="predict" type="submit" name="submit_btn">Predict likes</button>
      </div>
    </div>
  </div>
</form>

<?php
if(isset($_REQUEST['submit_btn'])){

  $selection = $_POST["selected"];
  $message = $_POST["predictM"];

  $messagecol->insertOne(
    [
      // '_id'=>'message',
    'selection'=>"$selection",
    'pmessage'=>"$message"]);
  shell_exec("python readtext.py");
  $upperboundary = $messagecol->findOne()->likesRange;
  $accuracy = $messagecol->findOne()->accuracy;

  echo '<div style="margin:auto; width:50%;border: 3px solid green;padding: 10px;">The range of the number of likes is ' .htmlspecialchars($lowerboundary).' to '.htmlspecialchars($upperboundary);
  echo ' with an accuracy of '.htmlspecialchars(number_format((float)$accuracy,2,'.','')). '%';
  echo '</div>';   

}
?>

</body>
</html>


