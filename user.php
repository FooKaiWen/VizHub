<!DOCTYPE html>  
<html lang="en">
   <head>
      <title>VizHub</title>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" type="text/css" href="design.css">
      <!-- <link rel="stylesheet" type="text/css" href="home.css"> -->
      <meta name="description" content="">
      <meta name="author" content="">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>  
      <script src="http://cdnjs.cloudflare.com/ajax/libs/p5.js/0.5.6/p5.js"></script>  
      <script src ="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>  

      <!-- Bootstrap core CSS-->
      <link href="vendor/bootstrap/css2/bootstrap.min.css" rel="stylesheet">

      <!-- Custom fonts for this template-->
      <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

      <!-- Custom styles for this template-->
      <link href="css/sb-admin.css" rel="stylesheet">

      <?php
         require_once  'Facebook/autoload.php';
         require_once  "vendor/autoload.php";
         
         $dbhost ='localhost';
         $dbport ='27017';
         
         $client = new MongoDB\Client;
         $connection = new MongoDB\Driver\Manager("mongodb://$dbhost:$dbport");
         $query = new MongoDB\Driver\Query([]);
         
         
         $userDatas = $connection->executeQuery('fb.userDetail', $query);
         
         foreach($userDatas as $userData){
            $name = $userData->name;
            $id = $userData->id;
            $url =$userData->url;
         }
         
         $newdb = $client->selectDatabase('fb');
         $temp = $newdb->selectCollection('predictMessage');
         $temp->drop();
         $messageCol = $newdb->selectCollection('predictMessage');
         
         session_start();
         $logoutUrl = $_SESSION['logoutUrl'];
         ?>
   </head>
   <body>
      <nav class="navbar navbar-expand navbar-dark bg-dark static-top">  
         <a class="navbar-brand mr-1" href="user.php">VizHub</a>  
         <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">  
            <i class="fas fa-bars"></i>  
         </button>  
         <a class="navbar-brand" href="user.php">Home</a>  
         <a class="navbar-brand" href="http://localhost/VizHub/aboutUs.html">About Us</a>  
         <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
               <li>
                  <button style = "float:right;"class ="Btn Btn--facebook" onclick="logout()">Log Out</button>
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
              Profile</div>
            <div class="card-body">
            <div class="jumbotron jumbotron-fluid">
                <div class="container">
                  <img src="<?php echo $url; ?>" alt="Profile Picture">
                  <h2 id="detail-name">Name: <?php echo $name; ?><br/></h2>
                  <h2 id="detail-id">ID: <?php echo $id; ?></h2>
               </div> 
            </div>

            </div>

                        <div class="card mb-3">
               <div class="card-header">
                  <i class="fas fa-chart-area"></i>
                  Prediction
               </div>
               <div class="card-body">
               <?php
               if(isset($_REQUEST['submit_btn'])){
               
                 $message = $_POST["predictM"];
                 
                 if($message != ""){
                   $messageCol->insertOne(
                     [
                     '_id'=>'message',
                     'pmessage'=>"$message"]);
                   shell_exec("python readtext.py");
                   $upperBoundary = $messageCol->findOne()->likesRange;
                   if($upperBoundary == 50){
                     $lowerBoundary = 0;
                   } else {
                     $lowerBoundary = $upperBoundary - 49;
                   }
               
                   echo '
                   <label for="summary" style="margin-top:15px; margin-left:30px;"><i>Result:</i></label>
                   <div id="summary" style="font-size:20px;">The machine predicted that the number of likes is somewhat around <strong style=" color:Black;">';
                 
                   if($upperBoundary == 250){
                     echo htmlspecialchars($upperBoundary).' and above</strong>';
                   } else {
                     echo htmlspecialchars($lowerBoundary).' to '.htmlspecialchars($upperBoundary).'</strong>';
                   }
                   
                   echo ' for the message: '.htmlspecialchars($message).'</div>';   
                 } else {
                   echo '
                   <label for="summary" style="margin-top:15px; margin-left:30px;"><i>Error</i></label>
                   <div id="summary" style="font-size:20px; margin-top:10px;">Invalid input! Please re-type the message.</div>';
                 }
               }
               ?>
                  <form method="POST" action="">
                     <div class="container">
                        <div class="form-group" >
                           <label for="Message" style="margin-top :15px; margin-left:20px;"><i>Message for Like Prediction:</i></label>
                           <textarea class="form-control" style ="border: 3px solid rgb(47, 52, 78); width:985px; margin-left:20px;" name="predictM" rows="3" id="message" 
                              placeholder="Type Your Message Here For Like Prediction . . . . . ."><?php if(isset($_REQUEST['submit_btn'])){echo htmlspecialchars($message);}?></textarea>
                           <div style ="text-align:center; margin-right:10px;">  
                              <button class ="copyText" onclick="copyMessage();return false;">Copy<br> Message</button>
                              <button class ="predict" type="submit" name="submitBtn">Predict <br> likes</button>
                           </div>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
         </div>
         <!-- /.container-fluid -->
      </div>
   </div>

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
      <!-- Bootstrap core JavaScript-->
      <script src="vendor/jquery/jquery.min.js"></script>
      <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
      <!-- Core plugin JavaScript-->
      <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
      <!-- Custom scripts for all pages-->
      <script src="js/sb-admin.min.js"></script>
   </body>
</html>