<?php
 require_once  'Facebook/autoload.php';
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

session_start();
$logoutUrl = $_SESSION['logoutUrl'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>VizHub</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="design.css">
  <link rel="stylesheet" type="text/css" href="home.css">
  <link rel="stylesheet" href="https://bootswatch.com/4/superhero/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
  <!-- <script type="text/javascript" src="chart.js"></script> -->

</head>
<body>

<!-- <div class="alert alert-dismissible alert-success">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>Well done!</strong> You successfully logged in! 👍</a>.
</div> -->

<div class="jumbotron jumbotron-fluid">
  <div class="container">
    <img src="<?php echo $url; ?>" alt="Profile Picture">
    <h2 id="detail-name">Name: <?php echo $name; ?><br/></h2>
    <h2 id="detail-id">ID: <?php echo $id; ?></h2>
    <button style = "margin-top:30px;"class ="Btn Btn--facebook" onclick="logout()">Log Out</button>
  </div>
</div>

<div style="width: 50%; float:left; height:30px;">
  <p>Click the button below to view the number of likes of your recent Facebook posts!</p>
</div>
<div style="width: 50%; float:right; height:30px;">
  <p>Click the button below to view your Facebook tagged places in Google Maps!</p>
</div>

<form action="engagementChart.php" target="_blank">
  <button id="vizbutton" style="width: 50%; float:left; height:150px; background:#738299; margin:0px">Engagement Visualization</button>
</form>
<form action="MapChart.php" target="_blank">
  <button id="vizbutton" style="width: 50%; float:right; height:150px; background:#738299; margin:0px">Location Vizualization</button>
</form>
<?php
if(isset($_REQUEST['submit_btn'])){

  echo 
  '<div id="modelsummary">
    <label for="summary" style="margin-top:15px; margin-left:30px;"><i>Model Summary Report</i></label>
    <div id="summary">
      <p>The machine model was trained with <b>1008</b> instances of data and tested with dataset of <b>336</b> instances. 
         It was evaluated with confusion matrix as below: </p>
      <table style="width:75%">
        <tr>
          <th>Confusion Matrix</th>
        </tr>
        <tr>
          <th>Number of Likes</th><th>Precision(%)</th><th>Recall(%)</th><th>F1-score(%)</th><th>Support</th>
        </tr>
        <tr>
          <td>0-50</td><td>44</td><td>31</td><td>36</td><td>68</td>
        </tr>
        <tr>
          <td>51-100</td><td>30</td><td>54</td><td>38</td><td>83</td>
        </tr>
        <tr>
          <td>101-150</td><td>38</td><td>25</td><td>30</td><td>65</td>
        </tr>
        <tr>
          <td>151-200</td><td>31</td><td>15</td><td>20</td><td>67</td>
        </tr>
        <tr>
          <td>201-250</td><td>30</td><td>36</td><td>33</td><td>53</td>
        </tr>
        <tr><td></td></tr>
        <tr>
          <td>weighted average</td><td>35</td><td>33</td><td>32</td><td>336</td>
        </tr>
      </table>
      <p><br/>In Layman\'s terms, the machine can predict the correct number of likes with one-third chance. Although it is not the exact
          number, it still gives the range of the predicted likes.</p>
      <p>We are still improving our machine model!</p>
    </div>
  </div>';

  $message = $_POST["predictM"];
  if($message != ""){
    $messagecol->insertOne(
      [
      // '_id'=>'message',
      'pmessage'=>"$message"]);
    shell_exec("python readtext.py");
    $upperboundary = $messagecol->findOne()->likesRange;
    if($upperboundary == 50){
      $lowerboundary = 0;
    } else {
      $lowerboundary = $upperboundary - 49;
    }

    echo '
    <label for="summary" style="margin-top:15px; margin-left:30px;"><i>Result</i></label>
    <div id="summary" style="font-size:20px;">The machine predicted that the number of likes is somewhat around <b>';
  
    if($upperboundary == 250){
      echo htmlspecialchars($upperboundary).' and above</b>';
    } else {
      echo htmlspecialchars($lowerboundary).' to '.htmlspecialchars($upperboundary).'</b>';
    }
    
    echo ' for the message: '.htmlspecialchars($message).'</div>';   
  } else {
    echo '
    <label for="summary" style="margin-top:15px; margin-left:30px;"><i>Error</i></label>
    <div id="summary" style="font-size:20px;">Invalid input! Please re-type the message.</div>';
  }
}
?>

  <form method="POST" action="">
  <div class="container">
    <div class="form-group" >
      <label for="Message" style="margin-top :15px;"><i>Message for Like Prediction:</i></label>
      <textarea class="form-control" style ="border: 3px solid rgb(47, 52, 78); " name="predictM" rows="3" id="message" 
        placeholder="Type Your Message Here For Like Prediction . . . . . ."><?php if(isset($_REQUEST['submit_btn'])){echo htmlspecialchars($message);}?></textarea>
      <div style ="text-align:center;">  
<<<<<<< HEAD
        <button class ="copyText" onclick="copyMessage()">Copy Message</button>
=======
        <button class ="copyText" onclick="copyText();return false;">Copy text</button>
>>>>>>> 238c32ff9dacad7c5dba66040cefcbd81a38f921
        <button class ="predict" type="submit" name="submit_btn">Predict likes</button>
      </div>
    </div>
  </div>
</form>


 <script>
      function copyMessage() {
        var copyText = document.getElementById("message");
        copyText.select();
        document.execCommand("copy");
        alert("Copied the text: " + copyText.value);
      }

      function logout(){
        var logoutUrl = <?php echo json_encode($logoutUrl);?>;

        window.location = logoutUrl;
      }
      
</script>

</body>
</html>


