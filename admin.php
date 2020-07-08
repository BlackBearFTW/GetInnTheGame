<?php 

session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ./registration/login.php");
    exit;
}


if(!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin"){
    header("location: ./index.php");
    exit;
}

// NOTE IF DEVICE IS A MOBILE
if ($_SESSION['device'] == 'mobile') {
    header("location: ../profile.php");
  }

require_once "db_config.php";

// NOTE THIS FUNCTION GENERATERS A RANDOM TEXT STRING (FOR THE COMPANY_TOKEN)
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// <----- SECTION CODE FUNCTION: ADD NEW COMPANY ----->

// NOTE CHECK IF A NEW COMPANY WAS SUBMITTED AND INSERTS IT INTO THE DATABASE
if (isset($_POST['add_company_name'])) {
    $add_company_name = $_POST['add_company_name'];
    $add_company_token = str_shuffle(str_replace(".", "", uniqid(generateRandomString(10),true)));
    mysqli_query($link, "INSERT INTO company(company_name, company_token) VALUES ('$add_company_name', '$add_company_token')");
    unset($_POST);
    $add_company_name = $add_company_token = '';
}
    
// NOTE INPUT FOR NEW COMPANY
echo "<form method='POST'><input type='text' name='add_company_name' placeholder='Nieuw Bedrijf Toevoegen...'><input type='submit'></form>";

// NOTE GET ALL COMPANY's
$all_company = mysqli_query($link, "SELECT company_name, company_token FROM company");

// NOTE DISPLAY EVERY COMPANY AND THEIR QR CODE
echo "<table border='1'><th>Bedrijf</th><th>Invite</th>";
while($row = mysqli_fetch_assoc($all_company)) {
    echo "<tr><td>" . $row['company_name'] . "</td><td><a href='https://api.qrserver.com/v1/create-qr-code/?data=www.getinnthegame.com/registration/register.php?company_token=". $row['company_token'] ."&download=1'>Download QR</a></td></tr>";
}
echo "</table>";

// <----- SECTION CODE FUNCTION: ADMIN CHECK -----> 

// NOTE IF ADMIN HAS ACCEPTED OR DENIED CHALLENGE GET DATA OF USER
if (isset($_POST['row_id']) AND !empty($_POST['row_id']) AND isset($_POST['challenge_status']) AND !empty($_POST['challenge_status'])) {
    $c_row_id = $_POST['row_id'];
    $c_status = $_POST['challenge_status'];
    $c_part_points = $_POST['part_points'];
    $c_user_id = $_POST['user_id'];
    
  if ($c_status == 'ACCEPT') { // NOTE IF CHALLENGE WAS ACCEPTED... CHANGE STATUS TO COMPLETED AND GIVE USER POINTS
       mysqli_query($link, "UPDATE challenge_status SET status = 'COMPLETED' WHERE row_id = '$c_row_id'");
       mysqli_query($link, "UPDATE users SET points = points + $c_part_points WHERE user_id = $c_user_id");

   } elseif ($c_status == 'DENY') { // NOTE IF CHALLENGE WAS DENIED... DELETE RECORD SO USER CAN TRY AGAIN
     mysqli_query($link, "DELETE FROM challenge_status WHERE row_id = '$c_row_id'");
  }
    unset($_POST); // NOTE DELETE ALL DATA SO ADMIN CANT ACCEPT / DENY CHALLENGE AGAIN
    $c_row_id = $c_status = $c_part_points = $c_part_points = $c_user_id = '';
   }

   // NOTE GET ALL ADMINCHECK CHALLENGES
$admincheck_results = mysqli_query($link, "SELECT cs.row_id, users.user_id, users.firstname, users.lastname, cd.name, cd.question, cd.part_points, cs.played_at FROM challenge_status cs, challenge_data cd, users WHERE cs.challenge_id = cd.challenge_id AND cs.user_id = users.user_id AND cs.status = 'PENDING'");

echo "<div class='admincheck'><table border='1' style='white-space:pre; height: 100%; width: 100%;'><th>Kick-Off Koffie</th>";

// NOTE DISPLAY CHALLENGE AND USER SO ADMIN CAN ACCEPT OR DENY
while($row = mysqli_fetch_assoc($admincheck_results)) {
    echo "<tr style='width:100%;'><td>Naam: <strong>". $row['firstname'] ." ". $row['lastname'] ."</strong>\nChallenge: ". $row['name'] ."\nDoel: ". $row['question'] ."\nPuntenwaarde: ". $row['part_points'] ."\nSpeeldatum: ". $row['played_at'] ."\n<form method='POST'><input type='hidden' value='". $row['part_points'] ."' name='part_points'><input type='hidden' value='". $row['user_id'] ."' name='user_id'><input type='hidden' value='". $row['row_id'] ."' name='row_id'>Status: <input type='submit' value='ACCEPT' name='challenge_status' onclick=\"return confirm('Are you sure you want to ACCEPT?')\">&nbsp<input type='submit' value='DENY' name='challenge_status' onclick=\"return confirm('Are you sure you want to DENY?')\"></form></tr>";
    }
 
echo "<tr style='height:100%; width: 100%;'></tr>";
echo "</table></div>";

?>
<meta http-equiv="refresh" content="1200;url=./registration/logout.php" />
<style>

.admincheck {
    height:100%;
    overflow-x: visible;
overflow-y: auto;
  position: absolute;
   right: 0px;
    top: 0px;
      width: 70vw;
}
</style>

<script>
    if ( window.history.replaceState ) {
        
        window.history.replaceState( null, null, window.location.href );
    }

window.onpopstate = function() {
  document.location = './index.php'; 
    }; history.pushState({}, '')
</script>