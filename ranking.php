<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ./registration/login.php");
    exit;
}

// NOTE CONNECT TO DATABASE
require_once "db_config.php";

// NOTE GET RANKING
$user_result = mysqli_query($link, "SELECT user_id, firstname, lastname, points FROM users WHERE NOT points='10' ORDER BY points DESC ");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="refresh" content="1200;url=./registration/logout.php" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/stylesheet-main.css">
    <link rel="stylesheet" href="/css/ranking.css">
    <title>Ranking</title>
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        table {margin: 0 auto;}
        td {padding: 15px;}
    </style>
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
    <a class="dropdown-item kleur-text-knop" href= './profile.php'>Profile</a>
    <a class="dropdown-item kleur-text-knop" href= '# '>Ranking</a>
    <?php if ($_SESSION['device'] == "mobile") {
      echo "<a class='dropdown-item kleur-text-knop'  href='./qr_page.php'>QR</a>";
     echo "<a class='dropdown-item kleur-text-knop' href='./index.php'>Challenges</a>";
    }
    ?>
    <a class="dropdown-item kleur-text-knop" href='./registration/logout.php'>Log out</a>
  </div>
</div>
        </div>
<!--end of dropdown menu  -->
            <div class="col-8"><h2 class="center-dit color-wit"><strong>Ranking</strong></h2></div>
            
            <div class="col-2"></div>


<!-- Tthis is the brown ish top -->

            
        </div>
        <div class="row color-grijs">
            <div class="col-12 mh-100" style="padding: 10px;"></div>
        </div><br><br><br>


</body>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>


<?php

    $rank = 1;
    echo "<h3><table  border='1'>";
    echo "<tr class='sterk'><td>Rank</td><td>Naam</td><td>Punten</td></tr>";

    // NOTE DISPLAY TABLE WITH RANKING
    while($row = mysqli_fetch_assoc($user_result)) {
        echo "<tr data-href='profile.php?user_id=". $row['user_id'] ."' class='rank-row'><td>$rank</td><td>" . $row['firstname'] . ' ' . $row['lastname'] ."</td><td>" . $row['points'] . "</td></tr>";
        $rank = $rank + 1;
    }
    echo "</table></h3>"; //Close the table in HTML
    echo "<br><br><br>";


?>

<script>


 // NOTE MAKES WHOLE COLUMN CLICKABLE
 document.querySelectorAll(".rank-row").forEach(function(element){
        element.addEventListener('click', function(event) {
        window.document.location = event.target.parentNode.getAttribute('data-href');
        });
          });


</script>
</html>
<!-- 
Game Idea by
Judith van der Velden ʕノ•ᴥ•ʔノ ︵ ┻━┻

site structure and functions by
Robin Mager (╯°□°)╯︵ ┻━┻

Web design by
Bas Vleugels ┬─┬ノʕ•ᴥ•ノʔ 

-->
