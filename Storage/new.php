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
         
         
         $userdatas = $connection->executeQuery('fb.userDetail', $query);
         
         foreach($userdatas as $userdata){
         $name = $userdata->name;
         $id = $userdata->id;
         $url =$userdata->url;
         }
         
         $newdb = $client->selectDatabase('fb');
         $temp = $newdb->selectCollection('predictMessage');
         $temp->drop();
         $messagecol = $newdb->selectCollection('predictMessage');
         
         session_start();
         $logoutUrl = $_SESSION['logoutUrl'];
         
         
         ?>
   </head>
   <body>
      <nav class="navbar navbar-expand navbar-dark bg-dark static-top">  
         <a class="navbar-brand mr-1" href="index.html">VizHub</a>  
         <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">  
         <i class="fas fa-bars"></i>  
         </button>  
         <a class="navbar-brand" href="#">Home</a>  
         <a class="navbar-brand" href="http://localhost/VizHub/aboutUs.html">About Us</a>  
         <button style = "float:right;"class ="Btn Btn--facebook" onclick="logout()">Log Out</button>
      </nav>

       <div id="wrapper">
      <!-- Sidebar -->
      <ul class="sidebar navbar-nav">
         <li class="nav-item active">
            <a class="nav-link" href="index.html">
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
               <a class="dropdown-item" href="engagementChart.html">Engagement</a>
               <a class="dropdown-item" href="mapChart.html">Location</a>
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


            <?php
               if(isset($_REQUEST['submit_btn'])){
               
                 $message = $_POST["predictM"];
                 if($message != ""){
                   $messagecol->insertOne(
                     [
                     '_id'=>'message',
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
                   <div id="summary" style="font-size:20px;">The machine predicted that the number of likes is somewhat around <strong style="background-color:White; color:Black;">';
                 
                   if($upperboundary == 250){
                     echo htmlspecialchars($upperboundary).' and above</strong>';
                   } else {
                     echo htmlspecialchars($lowerboundary).' to '.htmlspecialchars($upperboundary).'</strong>';
                   }
                   
                   echo ' for the message: '.htmlspecialchars($message).'</div>';   
                 } else {
                   echo '
                   <label for="summary" style="margin-top:15px; margin-left:30px;"><i>Error</i></label>
                   <div id="summary" style="font-size:20px;">Invalid input! Please re-type the message.</div>';
                 }
               }
               ?>
            </div>

                        <div class="card mb-3">
               <div class="card-header">
                  <i class="fas fa-chart-area"></i>
                  Prediction
               </div>
               <div class="card-body">
                  <form method="POST" action="">
                     <div class="container">
                        <div class="form-group" >
                           <label for="Message" style="margin-top :15px;"><i>Message for Like Prediction:</i></label>
                           <textarea class="form-control" style ="border: 3px solid rgb(47, 52, 78); " name="predictM" rows="3" id="message" 
                              placeholder="Type Your Message Here For Like Prediction . . . . . ."><?php if(isset($_REQUEST['submit_btn'])){echo htmlspecialchars($message);}?></textarea>
                           <div style ="text-align:center;">  
                              <button class ="copyText" onclick="copyMessage();return false;">Copy<br> Message</button>
                              <button class ="predict" type="submit" name="submit_btn">Predict <br> likes</button>
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
 

      <!-- Bootstrap core JavaScript-->
      <script src="vendor/jquery/jquery.min.js"></script>
      <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
      <!-- Core plugin JavaScript-->
      <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
      <!-- Custom scripts for all pages-->
      <script src="js/sb-admin.min.js"></script>
   </body>
</html>