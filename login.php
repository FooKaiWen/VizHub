<?php

require_once  'Facebook/autoload.php';

$fb = new Facebook\Facebook([
  'app_id' => '267157010556839', // Replace {app-id} with your app id
  'app_secret' => 'cb8559fb855dcb5a73a624df4fdf58f5',
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