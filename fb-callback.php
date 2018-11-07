<!DOCTYPE html>
<html lang="en">
<head>
  <title>VizHub</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="design.css">
  <link rel="stylesheet" href="https://bootswatch.com/4/superhero/bootstrap.min.css">
  
  <script type="text/javascript" src="chart.js"></script>
</head>
<body>
<div class="alert alert-dismissible alert-success">
  <button type="button" class="close" data-dismiss="alert">&times;</button>
  <strong>Well done!</strong> You successfully logged in! 👍</a>.
</div>

<?php
require_once  'Facebook/autoload.php';
require_once  "vendor/autoload.php";

if(!session_id()) {
    session_start();
}

$dbhost ='localhost';
$dbport ='27017';

$client = new MongoDB\Client;

$temp = $client->selectDatabase('test');
$newdb = $temp->drop();
$newdb = $client->selectDatabase('test');
$col = $newdb->selectCollection('user');
$collection = $newdb->selectCollection('post');
$location_col = $newdb->selectCollection('location');
$place = $newdb->selectCollection('place');


// $insertManyResult = $col->insertMany([
//     [
//         'username' => 'admin',
//         'email' => 'admin@example.com',
//         'name' => 'Admin User',
//     ],
//     [
//         'username' => 'test',
//         'email' => 'test@example.com',
//         'name' => 'Test User',
//     ],
// ]);

// printf("Inserted %d document(s)\n", $insertManyResult->getInsertedCount());

// var_dump($insertManyResult->getInsertedIds());
$connection = new MongoDB\Driver\Manager("mongodb://$dbhost:$dbport");

// // $con = new MongoDB\Client;
// // var_dump($con);

// $db = $connection->test;

// // $db = $conn->admin;

// echo "connected";

$fb = new Facebook\Facebook([
  'app_id' => '267157010556839', // Replace {app-id} with your app id
  'app_secret' => 'cb8559fb855dcb5a73a624df4fdf58f5',
  'default_graph_version' => 'v3.1',

    ]);
  
$helper = $fb->getRedirectLoginHelper();
  
if(isset($_GET['state'])){
  $helper->getPersistentDataHandler()->set('state',$_GET['state']);
}

try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}
  
if (! isset($accessToken)) {
  if ($helper->getError()) {
    header('HTTP/1.0 401 Unauthorized');
    echo "Error: " . $helper->getError() . "\n";
    echo "Error Code: " . $helper->getErrorCode() . "\n";
    echo "Error Reason: " . $helper->getErrorReason() . "\n";
    echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } else {
    header('HTTP/1.0 400 Bad Request');
    echo 'Bad request';
  }
  exit;
}
  
  // Logged in
  //echo '<h3>Access Token</h3>';
  //var_dump($accessToken->getValue());
  
  // The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();
  
  // Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
  //echo '<h3>Metadata</h3>';
  //var_dump($tokenMetadata);
  
  // Validation (these will throw FacebookSDKException's when they fail)
  // $tokenMetadata->validateAppId(''); // Replace {app-id} with your app id
  // If you know the user ID this access token belongs to, you can validate it here
  //$tokenMetadata->validateUserId('123');
$tokenMetadata->validateExpiration();
  
if (! $accessToken->isLongLived()) {
  // Exchanges a short-lived access token for a long-lived one
  try {
    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
  } catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
    exit;
  }
  //echo '<h3>Long-lived</h3>';
  //var_dump($accessToken->getValue());
}
  
$_SESSION['fb_access_token'] = (string) $accessToken;

// getting all posts id published by user
try {
    $posts_request = $fb->get('/me?fields=posts.limit(10){id}',$accessToken);
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

$graphNode = $posts_request->getGraphNode();
$insertManyResult = $col->insertOne(json_decode($graphNode));
$cursor = $col->distinct("posts.id");


foreach ($cursor as $doc) {
  try {
    $reactions_request = $fb->get("/$doc?fields=created_time,message,reactions.type(LIKE).limit(0).summary(1).as(like),reactions.type(LOVE).limit(0).summary(1).as(love),reactions.type(HAHA).limit(0).summary(1).as(haha),reactions.type(WOW).limit(0).summary(1).as(wow),reactions.type(SAD).limit(0).summary(1).as(sad),reactions.type(ANGRY).limit(0).summary(1).as(angry)",$accessToken);
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
  } catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}
  $ReactionNode = $reactions_request->getDecodedBody();
  $insertManyResult = $collection->insertOne($ReactionNode);
}

$query = new MongoDB\Driver\Query([]); 
     
$rows = $connection->executeQuery('test.post', $query);


$likearray = array();
$timearray = array();
foreach ($rows as $row) {
  $likearray [] = [];
  if(!isset($row->message)){
  // $msg = $row->message;  
   $curr_id = $row->id;
   $collection->updateOne(
    [ 'id' => "$curr_id" ],
    [ '$set' => [ 'message' => " " ]]);
  }
  // echo $row->message;
  $likearray [] = $row->like->summary->total_count;
  $timearray [] = $row->created_time;
}
// $likearray [] = [1,2,3,4,5,6];
// print_r($likearray);
$_SESSION["likes"] = $likearray;
$_SESSION["time"] = $timearray;


//Get tagged place
try {
  $location_request = $fb->get('/me?fields=tagged_places.limit(10)',$accessToken);
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

$graphNode = $location_request->getGraphNode();
$insertManyResult = $location_col->insertOne(json_decode($graphNode));

$tagged = $location_col->distinct("tagged_places.id");
foreach ($tagged as $doc) {
  try {
    $place_request = $fb->get("/$doc?fields=place",$accessToken);
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
  } catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}
  $PlaceNode = $place_request->getDecodedBody();
  $insertManyResult = $place->insertOne($PlaceNode);
}



$tests = $connection->executeQuery('test.place', $query);
//Array for to put on table for visualization
$big = array();
foreach($tests as $test){
  $lat = $test->place->location->latitude;
  $long = $test->place->location->longitude;
  $place_name = $test->place->name;
  $temp_Holder = array ($lat,$long,$place_name);
  array_push($big,$temp_Holder);
}

$_SESSION["location"]=$big;
// print_r($big);
// foreach ($big as $b){
//   print("out");
//   print_r($b);
//   echo nl2br ("\n");
//   foreach($b as $bs){
//     print_r($bs);
//     echo nl2br ("\n");
//   }
// }
     
    // $rows = $connection->executeQuery('test.post', $query);
    // $pops = $connection->executeQuery('test.post', $query);
    // $newCsvData = array();

    // $handle = fopen("testfile.csv", "w");
    // foreach ($rows as $row) {
    //   $try = $row->message;  
    //   fputcsv($handle, array($try));
    // } 

    // $newCsvData = array();

    // if (($handle = fopen("testfile.csv", "r")) !== FALSE) {
    //     while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    //       //foreach ($pops as $pop) {
    //         $try2 = $pop->created_time;
    //         $data[] = "$try2";
    //         $newCsvData[] = $data;
    //       //} 
    //     }
    //     fclose($handle);
    // }

    // $handle = fopen("testfile.csv", "w");
    // foreach ($newCsvData as $line) {
    //   fputcsv($handle, $line);
    // }

    // fclose($handle);

// foreach ($cursor as $doc) {

//   $likes= $col->find(
//     ['id' => "$doc"],
//     ['projection' => ['love.summary.total_count' => 1, '_id' => 0]]
//   );
  

//   foreach ($likes as $like) {

//     echo (json_encode($like));
    
//   }

// }


// $cursor = $col->distinct("like.summary.total_count");
// foreach ($cursor as $doc) {
//   echo "$doc\n";
// }

// $bson = MongoDB\BSON\fromPHP(array($graphNode));

// printf("Inserted %d document(s)\n", $insertManyResult->getInsertedCount());


// $insertManyResult = $col->insertOne(json_decode($graphNode));


// printf("Inserted %d document(s)\n", $insertManyResult->getInsertedCount());


// $whatIWant = substr((string)$graphNode, strpos((string)$graphNode, "_") + 1);    

// echo $whatIWant;


// $total_posts = array();
// $posts_response = $posts_request->getGraphEdge();
// if($fb->next($posts_response)) {
//     $response_array = $posts_response->asArray();
//     $total_posts = array_merge($total_posts, $response_array);
//     while ($posts_response = $fb->next($posts_response)) {
//         $response_array = $posts_response->asArray();
//         $total_posts = array_merge($total_posts, $response_array);
//     }
//     print_r($total_posts);
// } else {
//     $posts_response = $posts_request->getGraphEdge()->asArray();
//     print_r($posts_response);
// }
  
  // User is logged in with a long-lived access token.
  // You can redirect them to a members-only page.
  //header('Location: https://example.com/members.php');

  try {
    // Returns a `FacebookFacebookResponse` object
    $pictureNode = $fb->get("/me/picture?type=large&redirect=false",$accessToken);
    $userDetailNode = $fb->get("/me?fields=id,name",$accessToken);
  } catch(FacebookExceptionsFacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
  } catch(FacebookExceptionsFacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
  }
  $graphNode = $pictureNode->getGraphNode();
  $url = $graphNode->getField('url');
  $userDetail = $userDetailNode->getGraphNode();
  $name = $userDetail->getField('name');
  $id = $userDetail->getField('id');
?>

<div class="jumbotron jumbotron-fluid">
  <div class="container">
    <img src="<?php echo $url; ?>" alt="Profile Picture">
    <h2 id="detail-name">Name: <?php echo $name; ?><br/></h2>
    <h2 id="detail-id">ID: <?php echo $id; ?></h2>
    <!-- <form method="post" action="#"> -->
        <!-- <input type="submit" name="Extract Data" value="Extract Data" class="extractionButton"> -->
     <!-- </form> -->
     <button class="extractionButton">Extract Data</button>
  </div>
</div>

<div style="width: 50%; float:left; height:30px;">
  <p>Click the button below to view the number of likes of your last 10 recent posts!</p>
</div>
<div style="width: 50%; float:right; height:30px;">
  <p>Click the button below to view your last 10 tagged places in Google Maps!</p>
</div>

<form action="engagementChart.php" target="_blank">
  <button style="width: 50%; float:left; height:150px; background:rgb(78, 210, 214); margin:0px">Engagement Visualization</button>
</form>
<form action="MapChart.php" target="_blank">
<button style="width: 50%; float:right; height:150px; background:rgb(184, 184, 41); margin:0px">Location Vizualization</button>
</form>

<canvas id="chart1" float="left" width="100" height="100"></canvas>

<!-- <canvas id="chart2" float="right" width="400" height="400"></canvas>
<script>plot(<?php echo json_encode($likearray) ?>,<?php echo json_encode($timearray) ?>)</script> -->

<!-- <form action="featurex.py">
  <fieldset>
    Post Message:<br>
    <input type="text" hint="Type your message here">
    <input type="submit" value="Submit">
  </fieldset>
</form> -->

</body>
</html>


