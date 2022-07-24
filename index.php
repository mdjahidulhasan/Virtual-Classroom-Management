<?php 
  include("header.php");
  $ooo = "";
  $username = $user['username'];
  $userCode = $user2['courseCode'];
  $post = new Post($con, $username, $userCode);
  $checkTeaching = $post->checkTeachingClass();
  $checkEnrolled = $post->checkEnrolledClass();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
  <div class="bg">
    <div class="wrapper">
      <?php if ($checkTeaching == true) { ?>
        <div class='teaching'>
          <h3>
            <span class='header'>Teaching:</span>
          </h3>
          <?php $post->loadTeachingClasses(); ?>
        </div>
      <?php } ?>

      <?php if ($checkEnrolled == true) { ?>
        <div class='enrolled'>
          <h3>
            <span class='header'>Enrolled:</span>
          </h3>
          <?php $post->loadEnrolledClasses(); ?>
        </div>
      <?php } ?>

      <?php if (($checkEnrolled == false) && ($checkTeaching == false)) { ?>
        <div id='nullTeachingEnrolled'>
          <?php 
            if(strcmp($user_role,"Teacher") == 0){ ?>
              <p>It seems you have not created any class yet!</p>
              <p>Click the button below or <i class='fas fa-plus' style='padding:0.4rem; color:inherit'></i> above to start with your class</p>
              <a href='create_class.php'>
                <button class='null-button'>Create Class</button>
              </a>
            <?php
            }
            else{ ?>
              <p>It seems you have not enrolled in any class yet!</p>
              <p>Click the button below or <i class='fas fa-plus' style='padding:0.4rem; color:inherit'></i> above to start with your class</p>
              <a href='join_class.php'>
                <button class='null-button'>Join Class</button>
              </a>
          <?php } ?>
        </div>
      <?php } ?>
    </div>
  </div>
</body>
</html> 