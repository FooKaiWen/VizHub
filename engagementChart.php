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
      <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>    
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
         $numType = array();

         $i = 50;  
         $linkCount = 0;  
         $photoCount = 0;  
         $statusCount = 0;  
         $videoCount = 0;  
         $eventCount = 0;
         
        $loveCount = 0;
        $wowCount = 0;
        $hahaCount = 0;
        $angryCount = 0;
        $sadCount = 0;

         foreach ($postDetail as $data) {     
             $likeArray [] = $data->like->summary->total_count;  
             $timeArray [] = substr($data->created_time,0,10);  

             if($i >0){  
                 $numType [] = $data->type;  
             }  
             $i--;  
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
         $timeCount = array_count_values($timeArray);

        foreach($timeCount as $time => $value){
            $accuLikeCount[$time] = 0;
            $accuCommentCount[$time] = 0;
            $accuShareCount[$time] = 0;
            $accuLoveCount[$time] = 0;
            $accuAngryCount[$time] = 0;
            $accuWowCount[$time] = 0;
            $accuHahaCount[$time] = 0;
            $accuSadCount[$time] = 0;
        }

        $timeDetail = $connection->executeQuery('fb.postDetail', $query);  
        
        foreach($timeDetail as $data){
            $accuLikeCount[substr($data->created_time,0,10)] += $data->like->summary->total_count;
            $accuCommentCount[substr($data->created_time,0,10)] += $data->comments->summary->total_count;
            $accuShareCount[substr($data->created_time,0,10)] += $data->shares->count; 

            $accuLoveCount[substr($data->created_time,0,10)] += $data->love->summary->total_count;  
            $accuAngryCount[substr($data->created_time,0,10)] += $data->angry->summary->total_count;  
            $accuWowCount[substr($data->created_time,0,10)] += $data->wow->summary->total_count;  
            $accuHahaCount[substr($data->created_time,0,10)] += $data->haha->summary->total_count;  
            $accuSadCount[substr($data->created_time,0,10)] += $data->sad->summary->total_count;  
        }

         foreach($postTypeCount as $type => $count){  
             $postType [] = $type;  
             $postCount [] = $count;  
         }  
        
         foreach($accuLikeCount as $date => $like){
             $distinctDate [] = $date;
             $accuLike [] = $like;
         }

         foreach($accuCommentCount as $date => $comment){
            $accuComment [] = $comment;
         }

         foreach($accuShareCount as $date => $share){
             $accuShare [] = $share;
         }

         foreach($accuLoveCount as $date => $love){
            $accuLove [] = $love;
            $loveCount += $love;
        }

        foreach($accuAngryCount as $date => $angry){
            $accuAngry [] = $angry;
            $angryCount += $angry;
        }

        foreach($accuWowCount as $date => $wow){
            $accuWow [] = $wow;
            $wowCount += $wow;
        }

        foreach($accuHahaCount as $date => $haha){
            $accuHaha [] = $haha;
            $hahaCount += $haha;
        }

        foreach($accuSadCount as $date => $sad){
            $accuSad [] = $sad;
            $sadCount += $sad;
        }

         $userDetails = $connection->executeQuery('fb.userDetail', $query);  
           
         foreach($userDetails as $data){  
             $numFriends = $data->friends->summary->total_count;      
         }  
           
         session_start();
         $logoutUrl = $_SESSION['logoutUrl'];

         ?>  

<script type="text/javascript">
    $(window).on('load',function(){
        $('#myModal').modal('show');
    });
    
</script>

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
         <li class="nav-item active dropdown">
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
         <li>
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
                    <input type="checkbox" id="togAllBtn" onclick='friendNumber(<?php echo json_encode($numFriends) ?>);plotReachChart();'>  
                    <div class="slider round">  
                        <span class="on"> Reach Graph</span><span class="off">Reach Graph</span>  
                    </div>
                </label>
                <label class="switch">
                    <input type="checkbox" id="togTotBtn" onclick='sortHighestCount(<?php echo json_encode($loveCount) ?>, <?php echo json_encode($hahaCount) ?>, <?php echo json_encode($wowCount) ?>, <?php echo json_encode($sadCount) ?>, <?php echo json_encode($angryCount) ?>,"reaction");plotReactChart()'>   
                    <div class="slider round">  
                        <span class="on"> Reaction Graph</span><span class="off">Reaction Graph</span>  
                    </div>
                </label>
                <label class="switch">
                    <input type="checkbox" id="togTypBtn" onclick='sortHighestCount(<?php echo json_encode($linkCount) ?>,<?php echo json_encode($videoCount) ?>,<?php echo json_encode($photoCount) ?>,<?php echo json_encode($eventCount) ?>,<?php echo json_encode($statusCount) ?>,"postType");plotPostTypeChart("chart",<?php echo json_encode($postCount) ?>,<?php echo json_encode($postType)?>)'>  
                    <div class="slider round">  
                        <span class="on">Post Type</span><span class="off">Post Type</span>  
                    </div>
                </label>
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

                <!-- Modal -->    
                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">    
                <div class="modal-dialog modal-dialog-centered" role="document">    
                    <div class="modal-content">    
                    <div class="modal-header">    
                        <h5 class="modal-title" id="exampleModalLongTitle">Guides</h5>    
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">    
                        <span aria-hidden="true">×</span>    
                        </button>    
                    </div>    
                    <div class="modal-body" align="center">    
                        <p><img src="img/toggleButton.PNG" width="80" height="30"> Toggle <b>buttons</b> for graphs.</p>
                        <p><img src="img/likeParameter.PNG"> Click the <b>parameters</b> for different data display.</p>
                        <p>Check on the <b><i>info box</i></b> (the <span style="color:blue;">blue</span> box) at the top for basic information.</p>
                        <p>Check on the <b><i>insight box</i></b> (the <span style="color:red;">red</span> box) at the bottom for customized insight.</p>
                    </div>    
                    <div class="modal-footer">    
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>    
                    </div>    
                    </div>    
                </div>    
                </div>    
                    <label for="triggerMessage" class="title"><i>Info</i></label>  
                    <div class="triggerMessage" id="triggerMessage" > 
                    <p id="chartInfo">Please toggle the parameters beside for graph!</p>
                    </div>
                  <p class="title"><i>Chart</i></p> 
                  <div class ="plot">
                     <canvas id="chart" float="right" width="300" height="150" ></canvas>
                  </div>
                  
                  <label for="informMessage" class="title"><i>Insight</i></label>
                  <div class ="informMessage"  >
                     <div id ="topInfo">Please toggle the parameters beside for insight!
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
      <script>assignValues(<?php echo json_encode($likeArray) ?>, <?php echo json_encode($accuLike) ?>, <?php echo json_encode($accuComment) ?>, <?php echo json_encode($accuShare) ?>, <?php echo json_encode($distinctDate) ?>,<?php echo json_encode($accuLove)?>, <?php echo json_encode($accuHaha)?>, <?php echo json_encode($accuWow)?>, <?php echo json_encode($accuSad)?>, <?php echo json_encode($accuAngry)?>);</script>  
      <!-- Bootstrap core JavaScript-->  
      <script src="vendor/jquery/jquery.min.js"></script>  
      <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>  
      <!-- Core plugin JavaScript-->  
      <script src="vendor/jquery-easing/jquery.easing.min.js"></script>  
      <!-- Custom scripts for all pages-->  
      <script src="js/sb-admin.min.js"></script>  
   </body>
</html>