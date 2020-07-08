<?php

// <----- SECTION CODE FUNTION: SET UP PAGE SETTINGS ----->
 //echo "<style> body { background-color: orange; z-index: 1;} </style>";

// NOTE START THE SESSION
session_start();

// NOTE CHECK IF THE USER IS LOGGED IN, IF NOT THEN REDIRECT HIM TO LOGIN PAGE
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../registration/login.php");
    exit;  
} 

// NOTE IF DEVICE IS A DESKTOP
if ($_SESSION['device'] == 'desktop') {
  header("location: ../profile.php");
}

// NOTE CHECK IF THE URL CONTAINS A VALID ASSIGNMENT ID, IF NOT REDIRECT TO ASSIGNMENT OVERFIEW PAGE
if (!isset($_GET['challenge_id']) OR !is_numeric($_GET['challenge_id'])) {
  header("location: ../index.php");
}


if (!isset($_SESSION['timestart'])) { // NOTE IF THE SESSION DOESNT HAVE A STARTTIME REDIRECT TO challenge.php
  echo "<script>window.location.href='../challenge.php?challenge_id=$challenge_id'</script>";
} 
else {
    $timestart = $_SESSION['timestart']; // NOTE OTHERWISE GET IT 
}

// NOTE CONNECT TO DATABASE FILE
require_once '../db_config.php';


// NOTE SET SOME BASE VARIABLES
$challenge_id =  preg_replace("/([^0-9]+)/","", mysqli_real_escape_string($link, $_GET['challenge_id']));
$user_id = $_SESSION['user_id'];
$completed = FALSE;
//$already_made = FALSE;
$db_badge = 0;
$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
date_default_timezone_set("CET");

// NOTE LOADS DATA FROM DATABASE: ASSIGNMENT STATUS DATA
/*$challenge_status = mysqli_query($link, "SELECT * FROM challenge_status WHERE challenge_id = $challenge_id AND user_id = $user_id");
if (mysqli_num_rows($challenge_status) > 0) {
$already_made = TRUE;
}*/

// NOTE LOADS DATA FROM DATABASE: ASSIGNMENT DATA
$sql = "SELECT * FROM challenge_data WHERE challenge_id = $challenge_id";
$result = mysqli_query($link, $sql);

// NOTE GET RESULTS FROM DATABASE AND TURN IT INTO A VARIABLE 
if (mysqli_num_rows($result) > 0) {
  while($row = mysqli_fetch_assoc($result)) {
      $question = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $row['question']);
      $part_points = $row['part_points'];
      $answer = strrev(strtolower($row['answer']));
      $steal = $row['steal'];
      $hidden = $row['hidden']; 
  }
} 

$badge_data = mysqli_query($link, "SELECT * FROM badge WHERE challenge_id = $challenge_id AND rank > 0");
if (mysqli_num_rows($badge_data) > 0) {
  while($row = mysqli_fetch_assoc($badge_data)) {
    $badge_id = $row['badge_id']; 
  }
}



// NOTE IF THE ASSIGNMENT IS HIDDEN (AKA YOU SHOULDNT PLAY THIS) REDIRECT TO INDEX
if ($hidden == 1) { 
  header("location: ../index.php");
}

// NOTE GET USERPOINTS FROM USERS TABLE 
 $user_result = mysqli_query($link, "SELECT points FROM users WHERE user_id =  $user_id");


// <----- SECTION CODE FUNCTION: DEFINE FUNCTIONS ----->

  // NOTE ADDS POINTS TO THE USERS
  function PointAdding() { 
    global $link, $user_id, $part_points;
    mysqli_query($link, "UPDATE users SET points= `points` +'$part_points' WHERE user_id= '$user_id'");
  }

  // NOTE REMOVES POINTS FROM THE USERS
  function PointRemoving() { 
    global $link, $old_user, $part_points;
    mysqli_query($link, "UPDATE users SET points= `points` -'$part_points' WHERE user_id= '$old_user'");
  }
  
  // NOTE FUNCTION THAT ACTIVATES QR CODE SCANNER
  function QRcode($answer_input) { 
    global $Android, $iPhone;
      if ($Android) { echo "<script src='https://rawgit.com/schmich/instascan-builds/master/instascan.min.js'></script>";}
        elseif ($iPhone) { echo "<script src='../js/instascan.min.js'></script>";}
      echo"<script type='text/javascript'>
  
      let scanner = new Instascan.Scanner({ video: document.getElementById('preview'), mirror: false });
   
   //camera
   scanner.addListener('scan', function(content) {
     document.getElementById('inputfield').value = content;
   
     var inputfield = document.getElementById('" . $answer_input ."');
     if (inputfield.value != '') {
       document.getElementById('answerform').submit();
     }
   });
   
   //camera
   Instascan.Camera
     .getCameras()
     .then(function(cameras) {
       //camera
       if (cameras.length > 0) {
         //camera
         scanner.start(cameras[1]);
       } else {
         //als er geen camera is komt er een error
         console.error('No cameras found.');
       }
     })
     .catch(function(e) {
       console.error(e);
     });
   
      </script>";
   
    }

    // <----- SECTION CODE FUNTION: TIMER ----->

  if (isset($_POST['answerfield'])) { // NOTE IF THE USER HAS NOT SCANNED A QR CODE
       $answerfield = strtolower($_POST['answerfield']); // NOTE IF THE USER HAS ANSWERED
         if ($answerfield == $answer) { // NOTE IF THE ANSWER IS CORRECT 

             if (!isset($timestop)) { // NOTE GET A STOP TIME IF IT HASNT BEEN SET
                $timestop = date_create("now"); }

                $interval = date_diff($timestop, $timestart);
                $result = date_interval_format($interval, "%H:%I:%S.%F"); // NOTE FORMAT IT
                $completed = TRUE; 
             }   else {
               echo "Dit is niet de goede QR code!"; 
               echo "<form id='answerform' method='POST'></video><br><input type='hidden' id='inputfield' name='answerfield'  value=''></form><video id='preview'><style>#preview {pointer-events: none; position: absolute !important; z-index:5 !important; }</style>";
               QRcode("inputfield");
              } // NOTE IF SCANNED QR CODE IS WRONG
            } else {
              echo "<form id='answerform' method='POST'></video><br><input type='hidden' id='inputfield' name='answerfield'  value=''></form><video id='preview'><style>#preview {pointer-events: none; position: absolute !important; z-index:5 !important; }</style>";
              QRcode("inputfield");
            }



      // <----- SECTION CODE FUNCTION: IF ANSWER IS CORRECT GIVE USER POINTS AND REDIRECT THEM TO THE COMPLETED PAGE. ----->

// NOTE IF ANSWER IS CORRECT
if ($completed == TRUE) { 

  $query = mysqli_query($link,"SELECT * FROM challenge_status WHERE user_id = $user_id AND challenge_id = $challenge_id"); // NOTE CHECK IF USER HAS ALREADY PLAYED THIS ASSIGNMENT

if (mysqli_num_rows($query) > 0) { // NOTE GET THE RESULT AND INSERT THEM INTO A VARIABLE
  while($row = mysqli_fetch_assoc($query)) {
    $db_score = $row['user_input'];
  }


  if ($result > $db_score) { // NOTE CHECK IF DATABASE TIME IS FASTER THAN RESULT TIME
      $_SESSION['QRRACE'] = "<strong>$result</strong><br>Maar helaas is je highscore: <strong>$db_score</strong>"; 
  } 
  else {  // NOTE IF DATABASE TIME IS SLOWER THEN RESULT TIME (AKA YOU GOT FASTER)   
    mysqli_query($link, "UPDATE `challenge_status` SET `user_input` = '$result', played_count= `played_count` +1, played_at = NOW() WHERE  user_id = $user_id AND challenge_id = $challenge_id "); // NOTE IF USER HAS PLAYED: UPDATE THEIR SCORE
    $_SESSION['QRRACE'] = "<strong>$result</strong>";
  } 
}

else { // NOTE IF THERE IS NO QUERY RESULT
     mysqli_query($link, "INSERT INTO challenge_status (challenge_id, user_id, badge_id, user_input) VALUES ($challenge_id, $user_id, (SELECT badge_id FROM badge WHERE rank = 0 AND challenge_id = $challenge_id), '$result')"); // NOTE IF USER HAS NOT PLAYED: INSERT THEIR SCORE
     $_SESSION['QRRACE'] = "<strong>$result</strong>";
           }      
           PointAdding(); 

          // <----- SECTION CODE FUNCTION: BADGE RANKING ----->

          // NOTE REMOVE POINTS FROM PREVIOUS BEST PLAYERS
          $rank_result = mysqli_query($link, "SELECT cs.user_id, badge.badge_value FROM challenge_status cs, badge WHERE cs.badge_id = badge.badge_id  AND badge.rank != 0 AND cs.challenge_id = $challenge_id");

      while ($row = mysqli_fetch_assoc($rank_result)) {
      $point_remove[] = $row;
      }
      
      // FIXME OPTIMIZATION 
      foreach ($point_remove as $p_r) {
          $pr_userid = $p_r['user_id'];
          $pr_badgevalue = $p_r['badge_value'];
      
          mysqli_query($link, "UPDATE users SET points = points - $pr_badgevalue WHERE user_id = $pr_userid");
      }
      
           // NOTE SET EVERYONE'S BADGE TO PARTICIPATION BADGE
           mysqli_query($link, "UPDATE challenge_status AS t1 JOIN badge t2 ON t1.challenge_id = t2.challenge_id SET t1.badge_id = t2.badge_id  WHERE t2.challenge_id = $challenge_id AND t2.rank = 0 AND t1.challenge_id = $challenge_id"); // NOTE SET ALL BADGES OF ALL USERS FROM THIS ASSIGNMENT TO PARTICIPATION BADGE
           
           // NOTE GET ALL BADGES FOR THE CURRENT ASSIGNMENT
           // FIXME CAN BE OPTIMISED
           $q_badge = mysqli_query($link, "SELECT * FROM badge WHERE challenge_id = $challenge_id AND rank != 0 ORDER BY badge_id ASC");
           $num_rows = mysqli_num_rows($q_badge); // COUNT HOW MANY BADGES THERE ARE FOR THIS ASSIGNMENT
           $q_rank = mysqli_query($link, "SELECT users.user_id, ranking.user_input FROM challenge_status AS ranking INNER JOIN users ON ranking.user_id = users.user_id WHERE ranking.challenge_id = $challenge_id ORDER BY ranking.user_input ASC LIMIT $num_rows");
           
           while($row = mysqli_fetch_assoc($q_badge)) {
             $badge[] = $row;
           }
           
           while($row = mysqli_fetch_assoc($q_rank)) {
             $rank[] = $row;
           }
           
           for ($i = 0; $i <= sizeof($badge); $i++){
               $rank[$i] = array_merge($rank[$i], $badge[$i] );
               
               $db_badge = $rank[$i]['badge_id'];
               $db_rank_user = $rank[$i]['user_id'];
               // These bits are just for demonstrating the data is merged correctly
               mysqli_query($link, "UPDATE challenge_status SET badge_id = $db_badge WHERE user_id = $db_rank_user AND challenge_id = $challenge_id");
           }
           
           
           $rank_result_after = mysqli_query($link, "SELECT cs.user_id, badge.badge_value FROM challenge_status cs, badge WHERE cs.badge_id = badge.badge_id  AND badge.rank != 0 AND cs.challenge_id = $challenge_id");

           while ($row = mysqli_fetch_assoc($rank_result_after)) {
           $point_remove_after[] = $row;
           }
           
           foreach ($point_remove_after as $p_r_a) {
               $pr_userid = $p_r_a['user_id'];
               $pr_badgevalue = $p_r_a['badge_value'];
           
               mysqli_query($link, "UPDATE users SET points = points + $pr_badgevalue WHERE user_id = $pr_userid");

               if ($pr_userid == $user_id) {
                 $part_points = $part_points + $pr_badgevalue;
               }
           }
          
          

          // <----- SECTION CODE FUNCTION: COMPLETE PROCESS ----->
       // NOTE SET SESSION VARIABLE FOR COMPLETED PAGE 
       $_SESSION['part_points'] = $part_points;
       $_SESSION['score'] = $result;
       
       // NOTE REDIRECT TO COMPLETED.PHP
        header("location: ./completed.php");


    }
   
  



    
?>