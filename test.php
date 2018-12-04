<?php
require_once  "vendor/autoload.php";

$dbHost ='localhost';  
$dbPort ='27017';  

$client = new MongoDB\Client;  
$connection = new MongoDB\Driver\Manager("mongodb://$dbHost:$dbPort");  

$query = new MongoDB\Driver\Query([]);  
$postDetail = $connection->executeQuery('fb.postDetail', $query);  

foreach($postDetail as $row){
$temp = $row->created_time;
$date[] = substr($temp,0,10);
}

print_r($date);
print_r("=========");
$hi = array_unique($date);
print_r(array_unique($date));

$lol= $connection->executeQuery('fb.postDetail', $query);  
$temp = array();
$likeCount = 0;
foreach ($hi as $i){

    foreach($lol as $post){
      $temp = $post->created_time;
      $date = substr($temp,0,10);
      if ($date == $i)
      {
          $likeCount = $post->like->summary->total_count + $likeCount; 
          print_r($likeCount);
      }
    }
    

}

print_r($temp);


?>


    