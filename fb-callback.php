<?php
  require_once  'Facebook/autoload.php';
  require_once  "vendor/autoload.php";


  if(!session_id()) {
    session_start();
  }

$dbhost ='localhost';
$dbport ='27017';

$client = new MongoDB\Client;

$tempdb = $client->selectDatabase('fb');
$tempcol = $tempdb->selectCollection('post_id');
$tempcol->drop();
$tempcol = $tempdb->selectCollection('post_detail');
$tempcol->drop();
$tempcol = $tempdb->selectCollection('location_id');
$tempcol->drop();
$tempcol = $tempdb->selectCollection('location_detail');
$tempcol->drop();
$tempcol = $tempdb->selectCollection('userdetail');
$tempcol->drop();
$tempdb->drop();
$newdb = $client->selectDatabase('fb');

$usercol = $newdb->selectCollection('post_id');
$postcol = $newdb->selectCollection('post_detail');
$locationcol = $newdb->selectCollection('location_id');

$placecol = $newdb->selectCollection('location_detail');


$userdetailcol = $newdb->selectCollection('userdetail');

$connection = new MongoDB\Driver\Manager("mongodb://$dbhost:$dbport");
$query = new MongoDB\Driver\Query([]); 

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
    $posts_request = $fb->get('/me?fields=posts.limit(51){id}',$accessToken);
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
<<<<<<< HEAD
    $reactions_request = $fb->get("/$doc?fields=status_type,is_instagram_eligible,type,created_time,message,reactions.type(LIKE).limit(0).summary(1).as(like),reactions.type(LOVE).limit(0).summary(1).as(love),reactions.type(HAHA).limit(0).summary(1).as(haha),reactions.type(WOW).limit(0).summary(1).as(wow),reactions.type(SAD).limit(0).summary(1).as(sad),reactions.type(ANGRY).limit(0).summary(1).as(angry),comments.limit(0).summary(1),shares.summary(1)",$accessToken);
=======
    $reactions_request = $fb->get("/$doc?fields=type,created_time,message,reactions.type(LIKE).limit(0).summary(1).as(like),reactions.type(LOVE).limit(0).summary(1).as(love),reactions.type(HAHA).limit(0).summary(1).as(haha),reactions.type(WOW).limit(0).summary(1).as(wow),reactions.type(SAD).limit(0).summary(1).as(sad),reactions.type(ANGRY).limit(0).summary(1).as(angry),comments.limit(0).summary(1),shares.summary(1)",$accessToken);
>>>>>>> f3a5264909e528e47af1f21c71107482982e79cb
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
  $location_request = $fb->get('/me?fields=tagged_places.limit(30)',$accessToken);
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
    $place_request = $fb->get("/$doc?fields=place,created_time",$accessToken);
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


// $_SESSION["location"]=$big;
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

//   $likes= $usercol->find(
//     ['id' => "$doc"],
//     ['projection' => ['love.summary.total_count' => 1, '_id' => 0]]
//   );
  

//   foreach ($likes as $like) {

//     echo (json_encode($like));
    
//   }

// }


// $cursor = $usercol->distinct("like.summary.total_count");
// foreach ($cursor as $doc) {
//   echo "$doc\n";
// }

// $bson = MongoDB\BSON\fromPHP(array($graphNode));

// printf("Inserted %d document(s)\n", $insertManyResult->getInsertedCount());


// $insertManyResult = $usercol->insertOne(json_decode($graphNode));


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
    $userDetailNode = $fb->get("/me?fields=id,name,friends",$accessToken);
  } catch(FacebookExceptionsFacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
  } catch(FacebookExceptionsFacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
  }

  
  $graphNode = $pictureNode->getGraphNode();
  $url= $graphNode->getField("url");
  
  
  $userDetail = $userDetailNode->getDecodedBody();
  $userdetailcol->insertOne($userDetail);

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