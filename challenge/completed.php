<?php // Initialize the session

session_start();

require_once "../db_config.php";
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../registration/login.php");
    exit;
}

// NOTE IF DEVICE IS A DESKTOP, DENY ACCES
if ($_SESSION['device'] == 'desktop') {
    header("location: ../profile.php");
  }

$part_points = $_SESSION['part_points'];
$challenge_id = $_SESSION['challenge_id'];
$master_id = NULL;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="1200;url=../registration/logout.php" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Gehaald!</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/stylesheet-main.css">
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
<div class="page-header">
        <h1 class="text-center"><b>Je hebt de challenge gehaald!<b></h1>
        <h2 class="text-center">Voor deze challenge heb je <?php echo $part_points;?> punten gekregen!</h2></div>
        <?php 


if (isset($_SESSION['QRRACE'])) {
    $result = $_SESSION['QRRACE'];
    echo "<h3 class='text-center'>$result</h3>";
}

// NOTE IF CHALLENGE HAS MASTER CHALLENGE REDIRECT TO SUBPAGE 
$challenge_data = mysqli_query($link, "SELECT master_id FROM challenge_data WHERE challenge_id = '$challenge_id'"); 

while ($row = mysqli_fetch_assoc($challenge_data)) {
    $master_id = $row['master_id'];
}
echo " <p class='text-center'>";

if ($master_id == NULL) { // NOTE IF CHALLENGE IS NOT A SUB CHALLENGE
    echo "<a href='../index.php' class='btn btn-success'>Terug naar overzicht</a>";
} else {
    echo "<a href='./sub_challenge.php?challenge_id=$master_id' class='btn btn-success'>Terug naar overzicht</a>";
}
?>
    
    </p>

</body>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<script>history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1);</script>
</html>