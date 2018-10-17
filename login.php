<!DOCTYPE HTML>
<html>
  <head>
    <title>VizHub</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    
  </head>
  <body>
    <div class="title">
      <h1>VizHub &trade; : Data Insight Provider</h1>
    </div> 
      <hr/>
    <div class="introduction">

      <h4>What is VizHub about?</h4>
      <p>It is a university project which our team is working on. <br/> This website is about collecting your profile and post 
      data through Facebook Graph API (with your permission, of course 👌).</p>
    
      <h4>What do we do with the data collected?🤔</h4>
      <p>We analyse them and present stunning insights back to you visually. We focus on two types of visualizations, location based 
      and engagement based. <br/> Location visualization shows the locations of you've been to in the map, whereas engagement visualization shows
      the number of likes of a post, mentions of you and other amazing insights!</p>
      <p>Examples of those visualizations:</p>
      <p><img src="https://www.maptive.com/wp-content/uploads/2015/11/Screen-Shot-2015-11-29-at-6.48.56-PM-1.png" alt="description" 
      width="350" height="250" />
         <img src="http://opendatabits.com/wp-content/uploads/2014/06/Scatter3.png" alt="description" 
      width="350" height="250" /></p>
      <p>*The images are for illustration purposes only.</p>

      <h4>Who are we?</h4>
      <p>We are from Universiti Sains Malaysia, Malaysia. Currently third year Computer Science undergraduates, 
      working on this project.</p>
      <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15889.642903317785!2d100.28805829776803!3d5.354124675719885!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x304ac1a836ae7e53%3A0x835ac54fe8f4d95a!2sUniversity+of+Science%2C+Malaysia!5e0!3m2!1sen!2smy!4v1538313473518" 
      width="400" height="350" frameborder="0" style="border:0" allowfullscreen></iframe>

      <?php

      require_once  'Facebook/autoload.php';

      $fb = new Facebook\Facebook([
        'app_id' => '267157010556839', // Replace {app-id} with your app id
        'app_secret' => 'cb8559fb855dcb5a73a624df4fdf58f5',
        'default_graph_version' => 'v3.1',
        ]);

      $helper = $fb->getRedirectLoginHelper();

      $permissions = ['email']; // Optional permissions
      $loginUrl = $helper->getLoginUrl('https://localhost/project/fb-callback.php', $permissions);

      echo '<p><a href="' . htmlspecialchars($loginUrl) . '">Click here</a> to login!</p>';

      shell_exec("python plot.py");
      ?>
    </div>

    <p><a href="http://localhost/project/output.html">Plot</a></p>
  </body>
</html>