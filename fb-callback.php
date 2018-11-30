<?php
  require_once  'Facebook/autoload.php';
  require_once  "vendor/autoload.php";


  if(!session_id()) {
    session_start();
  }

// $tempcol = $tempdb->selectCollection('post_id');
// $tempcol->drop();
// $tempcol = $tempdb->selectCollection('post_detail');
// $tempcol->drop();
// $tempcol = $tempdb->selectCollection('location_id');
// $tempcol->drop();
// $tempcol = $tempdb->selectCollection('location_detail');
// $tempcol->drop();
// $tempcol = $tempdb->selectCollection('userdetail');
// $tempcol->drop();

$dbhost ='localhost';
$dbport ='27017';

$connection = new MongoDB\Driver\Manager("mongodb://$dbhost:$dbport"); 
$query = new MongoDB\Driver\Query([]); 


$client = new MongoDB\Client;

$tempdb = $client->selectDatabase('fb');
$tempdb->drop();

$newdb = $client->selectDatabase('fb');

$usercol = $newdb->selectCollection('post_id');
$postcol = $newdb->selectCollection('post_detail');
$locationcol = $newdb->selectCollection('location_id');
$placecol = $newdb->selectCollection('location_detail');
$userdetailcol = $newdb->selectCollection('userdetail');

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
  
// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();
// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
  
// Validation (these will throw FacebookSDKException's when they fail)
// $tokenMetadata->validateAppId(''); // Replace {app-id} with your app id
// If you know the user ID this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId('123');
$tokenMetadata->validateExpiration();
  
// if (! $accessToken->isLongLived()) {
//   // Exchanges a short-lived access token for a long-lived one
//   try {
//     $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
//   } catch (Facebook\Exceptions\FacebookSDKException $e) {
//     echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
//     exit;
//   }
//   //echo '<h3>Long-lived</h3>';
//   //var_dump($accessToken->getValue());
// }
  
$_SESSION['fb_access_token'] = (string) $accessToken;


$logoutUrl = $helper->getLogoutUrl($accessToken, 'http://localhost/VizHub/');
$_SESSION['logoutUrl'] = $logoutUrl;
 
// getting all posts id published by user
try {
<<<<<<< HEAD
    $posts_request = $fb->get('/me?fields=posts.limit(50){id}',$accessToken);
=======
    $posts_request = $fb->get('/me?fields=posts.limit(55){id}',$accessToken);
>>>>>>> f6bcfb1a36cd2763a57a5400efc9158fb9ecc90a
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
$insertManyResult = $usercol->insertOne(json_decode($graphNode));

$cursor = $usercol->distinct("posts.id");
foreach ($cursor as $doc) {
  try {
    $reactions_request = $fb->get("/$doc?fields=status_type,is_instagram_eligible,type,created_time,message,reactions.type(LIKE).limit(0).summary(1).as(like),reactions.type(LOVE).limit(0).summary(1).as(love),reactions.type(HAHA).limit(0).summary(1).as(haha),reactions.type(WOW).limit(0).summary(1).as(wow),reactions.type(SAD).limit(0).summary(1).as(sad),reactions.type(ANGRY).limit(0).summary(1).as(angry),comments.limit(0).summary(1),shares.summary(1)",$accessToken);
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
  $insertManyResult = $postcol->insertOne($ReactionNode);
}
     
$rows = $connection->executeQuery('fb.post_detail', $query);
  
foreach ($rows as $row) {
  if(!isset($row->message)){
  // $msg = $row->message;  
   $curr_id = $row->id;
   $postcol->updateOne(
    [ 'id' => "$curr_id" ],
    [ '$set' => [ 'message' => " " ]]);
  }
  if(!isset($row->shares)){
    $curr_id = $row->id;
    $postcol->updateOne(
      ['id' => "$curr_id"],
      ['$set' => ['shares' => ['count' => 0 ]]]
    );
  }
}

//Get tagged place
try {
  $location_request = $fb->get('/me?fields=tagged_places.limit(30){id}',$accessToken);
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
$insertManyResult = $locationcol->insertOne(json_decode($graphNode));


$tagged = $locationcol->distinct("tagged_places.id");
foreach ($tagged as $doc) {
  try {
    $place_request = $fb->get("/$doc?fields=place,created_time,id",$accessToken);
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
  $insertManyResult = $placecol->insertOne($PlaceNode);
}


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
$userDetail = $userDetailNode->getDecodedBody();
$userdetailcol->insertOne($userDetail);


$graphNode = $pictureNode->getGraphNode();
$url= $graphNode->getField("url");
$user_detail = $connection->executeQuery('fb.userdetail', $query);
foreach ($user_detail as $row) {
  if(!isset($row->url)){
  // $msg = $row->message;  
   $curr_id = $row->id;
   $userdetailcol->updateOne(
    [ 'id' => "$curr_id" ],
    [ '$set' => [ 'url' => $url ]]);
  }
}

Header("Location: http://localhost/VizHub/user.php");
?>