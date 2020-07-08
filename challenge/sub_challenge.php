<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../registration/login.php");
    exit;
}

// NOTE IF DEVICE IS A DESKTOP
if ($_SESSION['device'] == 'desktop') {
  header("location: ../profile.php");
}

// NOTE CONNECT TO DATABASE
require_once "../db_config.php";

$firstname = $_SESSION['firstname'];
$user_id = $_SESSION['user_id'];
$company_id = $_SESSION['company_id'];

       
// NOTE GET CHALLENGE ID
$all_challenges = [];
$challenge_id = preg_replace("/([^0-9]+)/","", mysqli_real_escape_string($link, $_GET['challenge_id']));


// NOTE GET ALL CHALLENGES THAT BELONG TO MASTER ID THAT HAVENT BEEN PLAYED YET
$challenge_result = mysqli_query($link, "SELECT * FROM `challenge_data` WHERE answer != '$company_id' AND master_id = $challenge_id AND challenge_id NOT IN (SELECT challenge_id FROM challenge_status WHERE user_id = $user_id);");

// NOTE IF USER HAS COMPLETED ALL SUB CHALLENGES
if (mysqli_num_rows($challenge_result) == 0) {
  mysqli_query($link, "INSERT INTO challenge_status (challenge_id, user_id, badge_id) VALUES ($challenge_id, $user_id, $badge_id)");
  header("location: ../index.php");
}

while($row = mysqli_fetch_assoc($challenge_result)) {
$row['is_completed'] = false;
$all_challenges[] = $row;
}

// NOTE GET ALL CHALLENGES THAT BELONG TO MASTER ID THAT HAVE BEEN PLAYED
$challenge_result = mysqli_query($link, "SELECT * FROM `challenge_data` WHERE answer != '$company_id' AND master_id = $challenge_id AND challenge_id IN (SELECT challenge_id FROM challenge_status WHERE user_id = $user_id)");

while($row = mysqli_fetch_assoc($challenge_result)) {
$row['is_completed'] = true;
$all_challenges[] = $row;
}

// NOTE FUNCTION THAT SORTS CHALLENGE RESULT IN GOOD ORDER
function cmp($a, $b)
{
return strcasecmp($a["name"], $b["name"]);
}

usort($all_challenges, "cmp");
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<link rel='../website-icon.ico' href='favicon.ico' type='image/x-icon'>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/stylesheet-main.css">
<link rel="stylesheet" href="../css/stylesheet.css" type="text/css">
<style>td {padding: 2%;} </style>
 <meta http-equiv="refresh" content="1200;url=../registration/logout.php" />
<meta charset="UTF-8">
    <title>Challenges</title>

</head>
<body>
  <!-- this is the orange top -->
<div class="container-fluid">
        <div class="row color-oranje " style=" box-shadow: 0 0 40px black;">

<!-- this is the dropdown menu with the links -->
        <div class="col-2">
<div class="dropdown">
  <button class="btn" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
  <h2 style="color: #564f42">&#9776;</h2>
  </button>
  <div class="dropdown-menu  kleur-knop" aria-labelledby="dropdownMenu2">
    <!-- for more links copy past one from down bellow and change href and the naming  -->
    <button class="dropdown-item kleur-text-knop" type="button" onclick="window.location.href = '../profile.php'">Profile</button>
    <button class="dropdown-item kleur-text-knop" type="button" onclick="window.location.href = '../ranking.php'">Ranking</button>
    <button class="dropdown-item kleur-text-knop" type="button" onclick="window.location.href = '../qr_page.php'">QR</button>
    <button class="dropdown-item kleur-text-knop" type="button" onclick="window.location.href = '../index.php'">Challenges</button>
    <button class="dropdown-item kleur-text-knop" type="button" onclick="window.location.href ='../registration/logout.php'">Log out</button>
  </div>
</div>
<!--end of dropdown menu  -->

        </div>
            <div class="col-8"><h2 class="center-dit color-wit"><strong>CHALLENGES</strong></h2></div>
<div class="col-2"></div>


<!-- Tthis is the brown ish top -->

            
        </div>
        <div class="row color-grijs">
            <div class="col-12 mh-100" style="padding: 10px;"></div>
        </div>
        <br><br><br>



<!-- this is where you can toggle what is shown -->


<!-- ROBIN DO NOT REMOVE DIS !!!!!!!!!! -->
<div class='row text-center'>
<div class='col-5 col-xs-1'></div>
  <div class="tab-content">
    <div class="tab-pane active" id="first">
      <div class="namedesig">
<!-- END OF ROBIN DO NOT REMOVE DIS !!!!!! -->
        <?php
echo "<div class='col'>";
echo "<h3><table cellspacing='0'>";

// NOTE DISPLAY ALL SUBCHALLENGE
    foreach($all_challenges as $challenge) {

   $category = $challenge['category'];
   $replay = $challenge['replay']; 
   $hidden = $challenge['hidden'];
  
   if ($challenge['is_completed'] === true AND $replay == 0) { echo"<tr class=' completed $category'><td>Completed</td><td>" . $challenge['name'] . "</td><td>" . $challenge['part_points'] . "</td></tr>";}

elseif ($challenge['is_completed'] === true AND $replay == 1) { echo"<tr data-href='./challenge.php?challenge_id=" . $challenge['challenge_id'] . "' class=' challenge-row notcompleted $category'><td>Played</td><td>" . $challenge['name'] . "</td><td>" . $challenge['part_points'] . "</td></tr>";}


else { echo"<tr data-href='./challenge.php?challenge_id=" . $challenge['challenge_id'] . "' class=' challenge-row notcompleted $category'><td>&starf;</td><td>" . $challenge['name'] . "</td><td>" . $challenge['part_points'] . "</td></tr>";}
    }

    echo "</table></h3>"; //Close the table in HTML
  echo "</div>";
    ?>

<div class="col-5 col-xs-1"></div>
</div>
</div> <!-- END OF CONTAINER -->


</body>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

<script>
document.querySelectorAll(".challenge-row").forEach(function(element){
        element.addEventListener('click', function(event) {
        window.document.location = event.target.parentNode.getAttribute('data-href');
        });
          })
</script>
</html>

<!-- 
Game Idea by
Judith van der velde ʕノ•ᴥ•ʔノ ︵ ┻━┻

site structure and functions by
Robin Mager (╯°□°)╯︵ ┻━┻

Web design by
Bas Vleugels ┬─┬ノʕ•ᴥ•ノʔ 

-->





