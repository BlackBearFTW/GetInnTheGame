<?php

// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ./registration/login.php");
    exit;
}

// NOTE IF DEVICE IS A DESKTOP, DENY ACCES
if ($_SESSION['device'] == 'desktop') {
    header("location: ../profile.php");
  }
  
require_once "db_config.php";

$user_id = $_SESSION['user_id'];

// NOTE GET QR CODE FROM DB
$qr_result = mysqli_query($link, "SELECT qr_token FROM `users` WHERE user_id = $user_id");

while($row = mysqli_fetch_assoc($qr_result)) {
   $db_qr = $row['qr_token'];
    }


?>

<html>
    <head>
        <title>QR-CODE</title>
        <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/stylesheet-main.css">
    
<link rel="stylesheet" href="./css/stylesheet.css" type="text/css">
<meta http-equiv="refresh" content="1200;url=./registration/logout.php" />
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
  <div class="dropdown-menu kleur-knop" aria-labelledby="dropdownMenu2">
    <!-- for more links copy past one from down bellow and change href and the naming  -->
    <button class="dropdown-item kleur-text-knop" type="button" onclick="window.location.href = './profile.php'">Profile</button>
    <button class="dropdown-item kleur-text-knop" type="button" onclick="window.location.href = './ranking.php'">Ranking</button>
    <button class="dropdown-item kleur-text-knop" type="button" onclick="window.location.href = '#'">QR</button>
    <button class="dropdown-item kleur-text-knop" type="button" onclick="window.location.href = './index.php'">Challenges</button>
    <button class="dropdown-item kleur-text-knop" type="button" onclick="window.location.href ='./registration/logout.php'">Log out</button>
  </div>
</div>
        </div>
<!--end of dropdown menu  -->
            
            <div class="col-8"><h2 class="center-dit color-wit"><strong>QR</strong></h2></div>
            <div class="col-2"></div>



<!-- Tthis is the brown ish top -->

            
        </div>
        <div class="row color-grijs">
            <div class="col-12 mh-100" style="padding: 10px;"></div>
        </div><br>

  <div class="row">
            <div class="col-1"></div>
            <div class="col-10 text-center">
                <p>This is your own QR-code. If someone has carried out your challenge properly or, for example, if someone has helped you, he or she will scan this code to get the points.</p>
        <!--loads a the QR picture that is auto generated-->
      <img id='barcode' 
            src="https://api.qrserver.com/v1/create-qr-code/?data=<?php if(isset($db_qr)) { echo $db_qr;} ?>&amp;size=110x110" 
            alt="there is no QR ? try and ask someone" 
            title="made by Bas & Robin" 
            width="280" 
            height="280"
            class="center-block" />
            <div class="col-1"></div>
        

            
    </body>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</html>
<!-- 
Game Idea by
Judith van der Velden ʕノ•ᴥ•ʔノ ︵ ┻━┻

site structure and functions by
Robin Mager (╯°□°)╯︵ ┻━┻

Web design by
Bas Vleugels ┬─┬ノʕ•ᴥ•ノʔ 

-->
