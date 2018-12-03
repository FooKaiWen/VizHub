<!DOCTYPE html>  
<html lang="en">
   <head>
      <title>VizHub</title>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" type="text/css" href="chart.css">
      <meta name="description" content="">
      <meta name="author" content="">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>  
      <script src="http://cdnjs.cloudflare.com/ajax/libs/p5.js/0.5.6/p5.js"></script>  
      <script src ="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>   
      <!-- Bootstrap core CSS-->  
      <link href="vendor/bootstrap/css/bootstrap2.min.css" rel="stylesheet">
      <!-- Custom fonts for this template-->  
      <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
      <!-- Custom styles for this template-->  
      <link href="css/sb-admin.css" rel="stylesheet">
   </head>
   <body id="page-top">
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
           
         session_start();
         $logoutUrl = $_SESSION['logoutUrl'];

         ?>  
      <nav class="navbar navbar-expand navbar-dark bg-dark static-top">  
         <a class="navbar-brand mr-1" href="user.php">VizHub</a>  
         <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">  
         <i class="fas fa-bars"></i>  
         </button>  
         <a class="navbar-brand" href="user.php">Home</a>  
     
         <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
               <li>
                  <button class ="btn btn-secondary btn-sm" onclick="logout()">Log Out</button>
               </li>
            </ul>
         </div>
      </nav>
      <div id="wrapper">
      <!-- Sidebar -->  
      <ul class="sidebar navbar-nav">
         <li class="nav-item active">  
            <a class="nav-link" href="user.php">  
            <i class="fas fa-fw fa-tachometer-alt"></i>  
            <span>Profile</span>  
            </a>  
         </li>
         <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown"  
               aria-haspopup="true" aria-expanded="false">  
            <i class="fas fa-fw fa-chart-area"></i>  
            <span>Visualization</span>  
            </a>  
            <div class="dropdown-menu" aria-labelledby="pagesDropdown">
               <i class="fas fa-fw fa-chart-area"></i>  
               <h6 class="dropdown-header">Type of Visualization:</h6>
               <a class="dropdown-item" href="engagementChart.php">Engagement</a>  
               <a class="dropdown-item" href="mapChart.php">Location</a>  
               <div class="dropdown-divider"></div>
            </div>
         </li>  
      </ul>
      <div id="content-wrapper">
         <div class="container-fluid">
            <!-- Area Chart Example-->  
            <div class="card mb-3">
               <div class="card-header">  
                  <i class="fas fa-chart-area"></i>  
                  Engagement Visualization
               </div>
               <div class="card-body">
               <div class="row" style = "width: 980px; float:left;">
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
                </div>

                    <div class="row" style = "width: 980px; float:left; margin-bottom:10px;">
                    <div class="numselect">
                    <select id="selected">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="50" selected>50</option>
                    </select>
                    </div>
                    </div>


                  <label for="triggerMessage" class="title"><i>Info</i></label>  
                  <div class="triggerMessage" id="triggerMessage" >
                     **Try CLICK on the Parameter !!  
                     <p id="chartInfo"></p>
                  </div>
                  
                 

                  

                  
                  <label for="" class="title"><i>Chart</i></label>  
                  
                  
                  
                  
                  <div class ="plot">
                     <canvas id="chart" float="right" width="300" height="150" ></canvas>
                  </div>
                  
                 
                  
                  <label for="informMessage" class="title"><i>Insight</i></label>
                  <div class ="informMessage"  >
                     <div id ="topInfo" style = "display:none;">
                     </div>
                  </div>
               </div>
            </div>
            <!-- /.container-fluid -->  
         </div>
         <!-- /.content-wrapper -->  
      </div>
      <!-- /#wrapper -->  
      <!-- Scroll to Top Button-->  
      <a class="scroll-to-top rounded" href="#page-top">  
      <i class="fas fa-angle-up"></i>  
      </a>  
      <script>
      function logout(){
            var logoutUrl = <?php echo json_encode($logoutUrl);?>;
            window.location = logoutUrl;
         }
      </script>
      <script type="text/javascript" src="chart.js"></script>  
      <!-- Bootstrap core JavaScript-->  
      <script src="vendor/jquery/jquery.min.js"></script>  
      <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>  
      <!-- Core plugin JavaScript-->  
      <script src="vendor/jquery-easing/jquery.easing.min.js"></script>  
      <!-- Custom scripts for all pages-->  
      <script src="js/sb-admin.min.js"></script>  
   </body>
</html>