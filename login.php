<?php

require_once  'Facebook/autoload.php';

$fb = new Facebook\Facebook([
  'app_id' => '', // Replace {app-id} with your app id
  'app_secret' => '',
  'default_graph_version' => '',
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
