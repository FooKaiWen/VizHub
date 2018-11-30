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

$dbHost ='localhost';
$dbPort ='27017';

$connection = new MongoDB\Driver\Manager("mongodb://$dbHost:$dbPort"); 
$query = new MongoDB\Driver\Query([]); 


$client = new MongoDB\Client;

$tempDb = $client->selectDatabase('fb');
$tempDb->drop();

$newDb = $client->selectDatabase('fb');

$userCol = $newDb->selectCollection('postId');
$postCol = $newDb->selectCollection('postDetail');
$locationCol = $newDb->selectCollection('locationId');
$placeCol = $newDb->selectCollection('locationDetail');
$userDetailCol = $newDb->selectCollection('userDetail');

$fb = new Facebook\Facebook([
  'app_id' => '267157010556839', // Replace {app-id} with your app id
  'app_secret' => 'cb8559fb855dcb5a73a624df4fdf58f5',
  'default_graph_version' => 'v3.2',
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
    $postsRequest = $fb->get('/me?fields=posts.limit(70){id}',$accessToken);
} catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}
$postIdNode = $postsRequest->getGraphNode();
$insertManyResult = $userCol->insertOne(json_decode($postIdNode));

$cursor = $userCol->distinct("posts.id");
foreach ($cursor as $doc) {
  try {
    $reactionsRequest = $fb->get("/$doc?fields=status_type,is_instagram_eligible,type,created_time,message,reactions.type(LIKE).limit(0).summary(1).as(like),reactions.type(LOVE).limit(0).summary(1).as(love),reactions.type(HAHA).limit(0).summary(1).as(haha),reactions.type(WOW).limit(0).summary(1).as(wow),reactions.type(SAD).limit(0).summary(1).as(sad),reactions.type(ANGRY).limit(0).summary(1).as(angry),comments.limit(0).summary(1),shares.summary(1)",$accessToken);
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
  } catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}
  $postDetailNode = $reactionsRequest->getDecodedBody();
  $insertManyResult = $postCol->insertOne($postDetailNode);
}
     
$rows = $connection->executeQuery('fb.postDetail', $query);
  
foreach ($rows as $row) {
  if(!isset($row->message)){
  // $msg = $row->message;  
   $currId = $row->id;
   $postCol->updateOne(
    [ 'id' => "$currId" ],
    [ '$set' => [ 'message' => " " ]]);
  }
  if(!isset($row->shares)){
    $currId = $row->id;
    $postCol->updateOne(
      ['id' => "$currId"],
      ['$set' => ['shares' => ['count' => 0 ]]]
    );
  }
}

//Get tagged place
try {
  $locationRequest = $fb->get('/me?fields=tagged_places.limit(30){id}',$accessToken);
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}
$locationIdNode = $locationRequest->getGraphNode();
$insertManyResult = $locationCol->insertOne(json_decode($locationIdNode));


$tagged = $locationCol->distinct("tagged_places.id");
foreach ($tagged as $doc) {
  try {
    $placeRequest = $fb->get("/$doc?fields=place,created_time,id",$accessToken);
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
  } catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}
  $placeNode = $placeRequest->getDecodedBody();
  $insertManyResult = $placeCol->insertOne($placeNode);
}


try {
  // Returns a `FacebookFacebookResponse` object
  $pictureNode = $fb->get("/me/picture?type=large&redirect=false",$accessToken);
  $userDetailNode = $fb->get("/me?fields=id,name,friends",$accessToken);
} catch(FacebookExceptionsFacebookResponseException $e) {
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(FacebookExceptionsFacebookSDKException $e) {
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}  
$userDetail = $userDetailNode->getDecodedBody();
$userDetailCol->insertOne($userDetail);


$graphNode = $pictureNode->getGraphNode();
$url= $graphNode->getField("url");
$userDetail = $connection->executeQuery('fb.userDetail', $query);
foreach ($userDetail as $row) {
  if(!isset($row->url)){
  // $msg = $row->message;  
   $currId = $row->id;
   $userDetailCol->updateOne(
    [ 'id' => "$currId" ],
    [ '$set' => [ 'url' => $url ]]);
  }
}

Header("Location: http://localhost/VizHub/user.php");
?>