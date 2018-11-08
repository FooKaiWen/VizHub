<!DOCTYPE html>
<html lang="en">
<head>
  <title>VizHub</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="design.css">
  <link rel="stylesheet" href="https://bootswatch.com/4/superhero/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</head>
<body>
  
<div class="lds-hourglass">Loading...</div>

<?php

require_once  'Facebook/autoload.php';

$fb = new Facebook\Facebook([
  'app_id' => '', // Replace {app-id} with your app id
  'app_secret' => '',
  'default_graph_version' => 'v3.1',


  ]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email']; // Optional permissions
$loginUrl = $helper->getLoginUrl('https://localhost/VizHub/fb-callback.php', $permissions);

  if (isset($_POST['logInBtn']))
  {
    echo "<script language='javascript' type='text/javascript'>";
    echo "window.location='$loginUrl';";
    echo "</script>";
  }

?>

</body>
</html>
