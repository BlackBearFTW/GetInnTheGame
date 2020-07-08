<?php




// <----- SECTION CODE FUNTION: SET UP PAGE SETTINGS ----->


// NOTE START THE SESSION
session_start();

// NOTE CHECK IF THE USER IS LOGGED IN, IF NOT THEN REDIRECT HIM TO LOGIN PAGE
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../registration/login.php");
    exit;  
} 

 // NOTE CHECK IF THE URL CONTAINS A VALID ASSIGNMENT ID, IF NOT REDIRECT TO ASSIGNMENT OVERFIEW PAGE
 if (!isset($_GET['challenge_id']) OR !is_numeric($_GET['challenge_id'])) {
   header("location: ../index.php");
 }

 // NOTE IF DEVICE IS A DESKTOP
if ($_SESSION['device'] == 'desktop') {
  header("location: ../profile.php");
}

// NOTE CONNECT TO DATABASE FILE
require_once '../db_config.php';


// NOTE SET SOME BASE VARIABLES
$challenge_id = preg_replace("/([^0-9]+)/","", mysqli_real_escape_string($link, $_GET['challenge_id']));
$user_id = $_SESSION['user_id'];
$user_company = $_SESSION['company_id'];
$completed = FALSE;
$wrong = FALSE;
$already_made = FALSE;
$badge_id = 0;
$Android = stripos($_SERVER['HTTP_USER_AGENT'],"Android");
$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");

   


// NOTE LOADS DATA FROM DATABASE: ASSIGNMENT STATUS DATA
$challenge_status = mysqli_query($link, "SELECT * FROM challenge_status WHERE challenge_id = $challenge_id AND user_id = $user_id");
if (mysqli_num_rows($challenge_status) > 0) {
$already_made = TRUE;
}

// NOTE LOADS DATA FROM DATABASE: ASSIGNMENT DATA
$result = mysqli_query($link, "SELECT * FROM challenge_data WHERE challenge_id = $challenge_id");

// NOTE GET RESULTS FROM DATABASE AND TURN IT INTO A VARIABLE 
if (mysqli_num_rows($result) > 0) {
  while($row = mysqli_fetch_assoc($result)) {
      $name = $row['name'];
      $db_description = $row['description'];
      $question = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $row['question']);
      $part_points = $row['part_points'];
      $answer = strtolower($row['answer']);
      $type = $row['type'];
      $steal = $row['steal'];
      $hidden = $row['hidden']; 
      $replay = $row['replay'];
      $master_id = $row['master_id'];


// NOTE TURN SERIALIZED ANSWER INTO ANSWER ARRAY
$data = @unserialize($answer);
if($data !== false || $answer === 'b:0;') { $answer = $data; } 
  }
}

// NOTE IF THE USER IS PART OF THE COMPANY FROM THE CURRENT CHALLENGE
if ($type == "QREXPERT" AND $user_company == $answer)  {
  header("location:./sub_challenge.php?challenge_id=$master_id");
}

// NOTE GET PARTICIPATION BADGE
$badge_data = mysqli_query($link, "SELECT * FROM badge WHERE challenge_id = $challenge_id AND rank = 0");
if (mysqli_num_rows($badge_data) > 0) {
  while($row = mysqli_fetch_assoc($badge_data)) {
    $badge_id = $row['badge_id']; 
  } 
} else {
    $badge_id = 0;
  }
 
if ($type == "OQA" AND $already_made == TRUE) {
  $part_points = 0;
  header("location: ../completed.php");
} 

// NOTE IF THE ASSIGNMENT IS HIDDEN (AKA YOU SHOULDNT PLAY THIS) REDIRECT TO INDEX
if ($hidden == 1 AND $type !== "QREXPERT") { 
  header("location: ../index.php");
}

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
      
// <----- SECTION CODE FUNCTION: IF USER HAS ANSWERED QUESTION DO CHECKS ----->
if ($type == "MASTER") {
  echo "<script>window.location.href='./sub_challenge.php?challenge_id=$challenge_id'</script>";
}

// NOTE IF USER HAS FILLED IN ANSWERFIELD
if(isset($_POST['answerfield'])) { 
  $useranswer = rtrim(strtolower($_POST['answerfield']));

 if ($type == "OQA" OR $type == "QRCODE") { // NOTE IF TYPE IS OPEN QUESTION
   if ($useranswer == $answer) { // CHECK ANSWER
      $completed = TRUE;
    }
  } // END OQA AND QRCODE

  elseif ($type == "QRRACE") { // NOTE IF TYPE IS OPEN QUESTION
    if ($useranswer == $answer) { // CHECK ANSWER
      $timestart = date_create("now"); //date_format(date_create("now"), 'H:i:s.u');
      $_SESSION['timestart'] = $timestart;
      echo "<script>window.location.href='./timer.php?challenge_id=$challenge_id'</script>";
     }
   } // END QRRACE

 elseif ($type == "WOL") { // NOTE IF TYPE IS WIN/LOSE
   $part_points = 0;
 
   if (in_array($useranswer, $answer)) {
     $part_points = array_search($useranswer, $answer);
     $completed = TRUE;
    }
  } // END WOL

 elseif ($type == "COUNT") {
  if(isset($_POST['useranswer_a']) AND !empty($_POST['useranswer_a'])) {
    $useranswer_a = unserialize($_POST['useranswer_a']);
  } else {
  $useranswer_a = [];
  }

  if (array_search($useranswer, $answer) !== false AND !in_array($useranswer, $useranswer_a)) {
  array_push($useranswer_a, $useranswer);
  $useranswer_a_encrypted = serialize($useranswer_a);
  } else {
    $wrong = TRUE;
  }

  if (empty(array_diff($answer, $useranswer_a))) {
    $completed = TRUE;
 }
  
  } // END COUNT

  elseif ($type == "QREXPERT") {

    if ($answer == 'all') {
    $expert_data = mysqli_query($link, "SELECT qr_token FROM users WHERE user_id != $user_id");
    } else {
    $expert_data = mysqli_query($link, "SELECT qr_token FROM users WHERE company_id = $answer AND user_id != $user_id");
    }

    while($row = mysqli_fetch_assoc($expert_data)){
     $qr_token[] = strtolower($row['qr_token']);
   }
   
   if (in_array($useranswer, $qr_token)) {
    $completed = 1;
  } else {
    $wrong = 1;
  }

  } elseif ($type == "ADMINCHECK" AND isset($_POST['completed'])) {
    mysqli_query($link, "INSERT INTO challenge_status (user_id, challenge_id, badge_id, status) VALUES ($user_id, $challenge_id, $badge_id, 'PENDING')");
    header("location: ./completed.php");
  } 

 } // NOTE END OF FILLED IN ANSWER 





// <----- SECTION CODE FUNCTION: IF ANSWER IS CORRECT GIVE USER POINTS AND REDIRECT THEM TO THE COMPLETED PAGE. ----->

// NOTE IF ANSWER IS CORRECT
if ($completed == TRUE) { 

  // NOTE CHECK IF ONLY ONE USER CAN OWN THE BADGE AT THE TIME 
   if ($steal == 1) { 
      $owned_result = mysqli_query($link,"SELECT user_id as old_user FROM challenge_status WHERE challenge_id = $challenge_id"); // NOTE GET RECORDS FROM "challenge_status" DATABASE TABLE
      while($row = mysqli_fetch_assoc($owned_result)) { // NOTE TURNS QUERY RESULTS INTO VARIABLE
        $old_user = $row['old_user'];
      }

      if ($user_id != $old_user) {
        PointRemoving(); // REMOVES POINTS FROM OLD USER
        mysqli_query($link,"UPDATE challenge_status SET user_id = '$user_id' WHERE challenge_id = '$challenge_id'"); // NOTE UPDATE USER IN "challenge_status" DATABASE TABLE
         PointAdding(); // GIVE CURRENT USER POINTS
      }
       else {$part_points = 0;}
      }

   else  { // NOTE IF BADGE CAN BE OWNED BY MULTIPLE PEOPLE AT THE SAME TIME
      PointAdding(); // GIVE CURRENT USER POINTS
      if ($already_made == FALSE) {
        mysqli_query($link,"INSERT INTO challenge_status (challenge_id, user_id, badge_id) VALUES ($challenge_id, $user_id, $badge_id)"); // NOTE INSERT USER AND ASSIGNMENT INTO "challenge_status" DATABASE TABLE
        } elseif ($already_made == TRUE) { mysqli_query($link,"UPDATE challenge_status SET played_count= `played_count` +1 WHERE user_id = $user_id AND challenge_id = $challenge_id"); 
        }
         } 
       
            // NOTE SET SESSION VARIABLE FOR COMPLETED PAGE
            $_SESSION['challenge_id'] = $challenge_id;
            $_SESSION['part_points'] = $part_points;

     // NOTE REDIRECT TO COMPLETED.PHP
       header("location: ./completed.php");
      }
  
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="refresh" content="1200;url=../registration/logout.php" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Challenge: <?= $name; ?></title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/stylesheet-main.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
<!-- this is the orange  -->
<div class="container-fluid">
        <div class="row color-oranje" style=" box-shadow: 0 0 40px black;">
            <div class="col-2"></div>
            <div class="col-8"><h1 class="text-center color-wit"><strong>WELCOME!</strong></h1></div>
            <div class="col-2"></div>
        </div>
        <div class="row color-grijs">
            <div class="col-12 mh-100" style="padding: 10px;"></div>
        </div><br>
        
  <div class="page-header"><h1 class="text-center"><?php echo $name;?></h1></div><?php if ($db_description != "") {echo "<h4> Uitleg: </h4> $db_description<br><br>";} ?>
  <p><?php echo "<h4>Vraag:</h4> $question";?></p><br>
  <h4> Puntscore voor deze challenge: </h4><?php echo $part_points;?><br><br>
  
  <div class='row'>
  <div class='col-2'></div>
  <div class='col-8'>
  <form action="" method="POST" id="answerform" autocomplete="off">
  </body>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
 <script>
window.onpopstate = function() {
  document.location = '../index.php'; 
    }; history.pushState({}, '')
</script>
  </html>

  <?php


// <----- SECTION CODE FUNTION: LOAD INPUTS FOR ANSWERS AND OTHER LOGIC ----->

// NOTE CHECKS ASSIGNMENT TYPE
if ($type == "OQA") {  // IF TYPE IS OPEN QUESTION
      echo "<h4>Vul hier je antwoord in:</h4><input type='text' name='answerfield'  value=''> <input type='submit' name='submit'></form>";

} elseif ($type == "QRCODE" OR $type == "WOL" OR $type == "QREXPERT" OR $type == "QRRACE") {  // IF TYPE IS QR CODE OR WIN/LOSE
  echo "<video id='preview'></video><br><input type='hidden' id='inputfield' name='answerfield'  value=''></form><style>#preview {pointer-events: none; }</style>";
  QRcode("inputfield");
  
} elseif ($type == "ADMINCHECK") {
  echo "<input type='hidden' id='inputfield' name='answerfield'  value='ADMINCHECK'><input type='submit' name='completed' value='Ik heb deze opdracht voltooid'></form>"; 

} elseif ($type == "COUNT") { // IF TYPE IS MULTI ANSWER QR CODE
  echo "<video id='preview'></video><br><input type='hidden' id='inputfield' name='answerfield'  value=''>";

  if (isset($useranswer_a_encrypted, $useranswer)) {
  echo "<input type='hidden' id='useranswer_a'  name='useranswer_a' value='$useranswer_a_encrypted'>"; 
  }

  echo "</form><style>#preview {pointer-events: none; }</style>";
  QRcode("inputfield");
  }

  else { // NOTE IF TYPE IS UNKNOWN
      echo "Fout! Kan challenge niet laden";
  }

  ?>
      </div> <!-- NOTE CLOSE COLUMN -->
    <div class='col-2'></div>
</div> <!-- NOTE CLOSE ROW -->

</div>
  <?php

  // <----- SECTION CODE FUNCTION: IF ANSWER ISNT CORRECT ----->
   // NOTE IF ANSWER IS INCORRECT
   if ($completed == FALSE AND isset($_POST['answerfield']) AND $type != "COUNT") {
    echo "<br>";
    echo "Fout! probeer het antwoord goed in te vullen";
  } elseif ($wrong == TRUE) {
    echo "<br>";
    echo "Fout! probeer het antwoord goed in te vullen";
  }
  ?>

