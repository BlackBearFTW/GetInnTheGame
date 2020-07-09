<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: ./registration/login.php");
  exit;
}

// NOTE UNSET ALL SESSION VARIABLES THAT ARE NOT NEEDED
$requiredSessionVar = array('loggedin', 'firstname', 'user_id', 'email', 'user_role', 'company_id', 'device');
foreach ($_SESSION as $key => $value) {
  if (!in_array($key, $requiredSessionVar)) {
    unset($_SESSION[$key]);
  }
}

// NOTE CONNECT TO MASTER INCLUDE FILE
require_once $_SERVER['DOCUMENT_ROOT'] . "/include/masterInclude.php";
$user_id = $_SESSION['user_id'];

// NOTE SET ALL POINTS TO 10 FOR ALL USERS THAT HAVE LESS THAN 10 POINTS
mysqli_query($link, "UPDATE users SET points = 10 WHERE points < 10");


// NOTE IF DEVICE IS A DESKTOP, DENY ACCES TO PAGE
if ($_SESSION['device'] == 'desktop') {
  header("location: ./profile.php");
}

$all_challenges = [];

// NOTE GET ALL CHALLENGES THAT THE USER HASNT DONE YET
$challenge_result = mysqli_query($link, "SELECT * FROM `challenge_data` WHERE challenge_id NOT IN (SELECT challenge_id FROM challenge_status WHERE user_id = $user_id);");

while ($row = mysqli_fetch_assoc($challenge_result)) {
  $row['is_completed'] = false;
  $all_challenges[] = $row;
}

// NOTE GET ALL CHALLENGES THAT THE USER HAS COMPLETED
$challenge_sql = "SELECT * FROM `challenge_data` WHERE challenge_id IN (SELECT challenge_id FROM challenge_status WHERE user_id = $user_id)";
$challenge_result = mysqli_query($link, $challenge_sql);

while ($row = mysqli_fetch_assoc($challenge_result)) {
  $row['is_completed'] = true;
  $all_challenges[] = $row;
}

// NOTE SORT ALL RECORDS IN ORDER OF NAME
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
  <link rel='./website-icon.ico' href='favicon.ico' type='image/x-icon'>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <link rel="stylesheet" href="/css/stylesheet-main.css">
  <link rel="stylesheet" href="./css/stylesheet.css" type="text/css">
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="/js/hide-show.js"></script>
  <style>
    td {
      padding: 2%;
    }
  </style>
  <meta http-equiv="refresh" content="1200;url=./registration/logout.php" />
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
            <button class="dropdown-item kleur-text-knop" type="button" onclick="window.location.href = './profile.php'">Profile</button>
            <button class="dropdown-item kleur-text-knop" type="button" onclick="window.location.href = './ranking.php'">Ranking</button>
            <button class="dropdown-item kleur-text-knop" type="button" onclick="window.location.href = './qr_page.php'">QR</button>
            <button class="dropdown-item kleur-text-knop" type="button" onclick="window.location.href = '#'">Challenges</button>
            <button class="dropdown-item kleur-text-knop" type="button" onclick="window.location.href ='./registration/logout.php'">Log out</button>
          </div>
        </div>
      </div>
      <!--end of dropdown menu  -->
      <div class="col-8">
        <h2 class="center-dit color-wit"><strong>CHALLENGES</strong></h2>
      </div>

      <div class="col-2"></div>


      <!-- Tthis is the brown ish top -->


    </div>
    <div class="row color-grijs">
      <div class="col-12 mh-100" style="padding: 10px;"></div>
    </div>
    <br><br><br>

    <div class="row">
      <div class="col-md-3 col-xs-1"></div>
      <div class="col">
        <div>
          <h3 id="TitelAll">ALL</h3>
          <h3 id="TitelConnecting">Connecting</h3>
          <h3 id="TitelSharing">Sharing</h3>
          <h3 id="TitelFailfast">Fail fast</h3>
          <h3 id="TitelBeYou">Be you</h3>
          <h3 id="TitelFun">Fun</h3>
          <h3 id="TitelBattle">Battle</h3>
          <h3 id="TitelExploring">Exploring</h3>



        </div>

        <p class="text-center">
        </p><br>

        <!-- these are the buttons for categories -->
        <div id="myBtnContainer">
          <button id="ALL" class="filter-btn active" onclick="filterSelection('all')"><img src="/images/categorie/ALL.png" alt="ALL" width="55px" ;></button>
          <button id="Connecting" class="filter-btn" onclick="filterSelection('connecting')"><img src="/images/categorie/Connecting.png" alt="Connecting" width="55px" ;></button>
          <button id="Sharing" class="filter-btn" onclick="filterSelection('sharing')"><img src="/images/categorie/Sharing.png" alt="Sharing" width="55px" ;></button>
          <button id="Failfast" class="filter-btn" onclick="filterSelection('fail-fast')"><img src="/images/categorie/Fail-fast.png" alt="Fail fast" width="55px" ;></button><br><br>
          <button id="BeYou" class="filter-btn" onclick="filterSelection('be-you')"><img src="/images/categorie/Be-you.png" alt="Be you" width="55px" ;></button>
          <button id="Fun" class="filter-btn" onclick="filterSelection('fun')"><img src="/images/categorie/Fun.png" alt="Fun" width="55px" ;></button>
          <button id="Battle" class="filter-btn" onclick="filterSelection('battle')"><img src="/images/categorie/Battle.png" alt="Battle" width="55px" ;></button>
          <button id="Exploring" class="filter-btn" onclick="filterSelection('exploring')"><img src="/images/categorie/Exploring.png" alt="Exploring" width="55px" ;></button>
        </div>
      </div>
      <div class="col-md-3 col-xs-1"></div>
    </div>
    <br>

    <!-- this is where you can toggle what is shown -->
    <div>
      <ul class="nav nav-tabs" id="myli">
        <li id="first" class="button-list settingshead li "><a href="#first" data-toggle="tab" class="a-kleur">ALL</a></li>
        <li class="button-list settingshead li "><a href="#second" data-toggle="tab" class="a-kleur">Playable</a></li>
        <li class="button-list settingshead li "><a href="#third" data-toggle="tab" class="a-kleur">New</a></li>
        <li class="button-list settingshead li "><a href="#fourth" data-toggle="tab" class="a-kleur">Completed</a></li>

      </ul>

    </div>


    <!-- script for color selected -->
    <script>
      var selector = '.nav li';

      $(selector).on('click', function() {
        $(selector).removeClass('active');
        $(this).addClass('active');

        // this should give the first item active color
        // $('#first').addClass('active');
      });
    </script>



    <!-- <script>
// Get the container element
var liContainer = document.getElementById("myli");

// Get all buttons with class="btn" inside the container
var li = liContainer.getElementsByClassName("li");

// Loop through the buttons and add the active class to the current/clicked button
for (var i = 0; i < li.length; i++) {
  li[i].addEventListener("click", function() {
    var current = document.getElementsByClassName("active");

    // If there's no active class
    if (current.length > 0) {
      current[0].className = current[0].className.replace(" active", "");
    }

    // Add the active class to the current/clicked button
    this.className += " active";
  });
}
</script> -->

    <div class="colortje">
      <br>

      <div class='row text-center '>
        <div class='col-5 col-xs-1'></div>
        <div class="tab-content">
          <div class="tab-pane active" id="first">
            <div class="namedesig">

              <?php


              echo "<div class='col'>";
              echo "<h3><table cellspacing='0'>";

              // NOTE ECHO ALL CHALLENGES SORTED BY IF THEIR COMPLETED OR NOT
              foreach ($all_challenges as $challenge) {

                $category = $challenge['category'];
                $replay = $challenge['replay'];
                $hidden = $challenge['hidden'];

                if ($hidden == 0) {
                  if ($challenge['is_completed'] === true and $replay == 0) {
                    echo "<tr class='filterDiv completed $category'><td>" . $challenge['name'] . "</td><td>" . $challenge['part_points'] . "</td></tr>";
                  } elseif ($challenge['is_completed'] === true and $replay == 1) {
                    echo "<tr data-href='./challenge/challenge.php?challenge_id=" . $challenge['challenge_id'] . "' class='filterDiv challenge-row notcompleted $category'><td>" . $challenge['name'] . "</td><td>" . $challenge['part_points'] . "</td></tr>";
                  } else {
                    echo "<tr data-href='./challenge/challenge.php?challenge_id=" . $challenge['challenge_id'] . "' class='new filterDiv challenge-row notcompleted $category'><td>" . $challenge['name'] . "</td><td>" . $challenge['part_points'] . "</td></tr>";
                  }
                } else {
                } // IF HIDDEN IS 1
              }
              echo "</table></h3>"; //Close the table in HTML
              echo "</div>";
              ?>

            </div>
          </div>
          <div class="tab-pane" id="second">
            <div class="namedesig">
              <?php

              echo "<div class='col'>";
              echo "<h3><table cellspacing='0'>";

              // NOTE DISPLAY ALL CHALLENGES THAT ARE PLAYABLE
              foreach ($all_challenges as $challenge) {

                $category = $challenge['category'];
                $replay = $challenge['replay'];
                $hidden = $challenge['hidden'];

                if ($hidden == 0) {
                  if ($challenge['is_completed'] === true and $replay == 1) {
                    echo "<tr data-href='./challenge/challenge.php?challenge_id=" . $challenge['challenge_id'] . "' class='filterDiv challenge-row notcompleted $category'><td>" . $challenge['name'] . "</td><td>" . $challenge['part_points'] . "</td></tr>";
                  } elseif ($challenge['is_completed'] === false) {
                    echo "<tr data-href='./challenge/challenge.php?challenge_id=" . $challenge['challenge_id'] . "' class='new filterDiv challenge-row notcompleted $category'><td>" . $challenge['name'] . "</td><td>" . $challenge['part_points'] . "</td></tr>";
                  }
                } else {
                } // IF HIDDEN IS 1
              }
              echo "</table></h3>"; //Close the table in HTML
              echo "</div>";

              ?>
            </div>
          </div>

          <div class="tab-pane" id="third">
            <!-- new -->
            <div class="namedesig">
              <?php

              echo "<div class='col'>";
              echo "<h3><table cellspacing='0'>";

              // NOTE DISPLAY ALL CHALLENGES THAT ARE NEW
              foreach ($all_challenges as $challenge) {

                $category = $challenge['category'];
                $replay = $challenge['replay'];
                $hidden = $challenge['hidden'];

                if ($hidden == 0) {
                  if ($challenge['is_completed'] === false) {
                    echo "<tr data-href='./challenge/challenge.php?challenge_id=" . $challenge['challenge_id'] . "' class='new filterDiv challenge-row notcompleted $category'><td>" . $challenge['name'] . "</td><td>" . $challenge['part_points'] . "</td></tr>";
                  }
                } else {
                } // IF HIDDEN IS 1
              }
              echo "</table></h3>"; //Close the table in HTML
              echo "</div>";
              ?>

            </div>
          </div>


          <div class="tab-pane" id="fourth">
            <!-- completed -->
            <div class="namedesig">
              <?php

              echo "<div class='col'>";
              echo "<h3><table cellspacing='0'>";

              // NOTE DISPLAY ALL CHALLENGES THAT ARE COMPLETED
              foreach ($all_challenges as $challenge) {

                $category = $challenge['category'];
                $replay = $challenge['replay'];
                $hidden = $challenge['hidden'];

                if ($hidden == 0) {
                  if ($challenge['is_completed'] === true and $replay == 0) {
                    echo "<tr class='filterDiv completed $category'><td>" . $challenge['name'] . "</td><td>" . $challenge['part_points'] . "</td></tr>";
                  }
                } else {
                } // IF HIDDEN IS 1
              }
              echo "</table></h3>"; //Close the table in HTML
              echo "</div>"
              ?>

            </div>
          </div>
          <div class="col-5 col-xs-1"></div>
        </div>
      </div> <!-- END OF CONTAINER -->
      <div>
      </div>
</body>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>



<script>
  // NOTE FILTER FOR CATEGORY'S 

  filterSelection("all")

  function filterSelection(c) {
    var x, i;
    x = document.getElementsByClassName("filterDiv");
    if (c == "all") c = "";
    for (i = 0; i < x.length; i++) {
      RemoveClass(x[i], "filter-show");
      if (x[i].className.indexOf(c) > -1) AddClass(x[i], "filter-show");
    }
  }

  function AddClass(element, name) {
    var i, arr1, arr2;
    arr1 = element.className.split(" ");
    arr2 = name.split(" ");
    for (i = 0; i < arr2.length; i++) {
      if (arr1.indexOf(arr2[i]) == -1) {
        element.className += " " + arr2[i];
      }
    }
  }

  function RemoveClass(element, name) {
    var i, arr1, arr2;
    arr1 = element.className.split(" ");
    arr2 = name.split(" ");
    for (i = 0; i < arr2.length; i++) {
      while (arr1.indexOf(arr2[i]) > -1) {
        arr1.splice(arr1.indexOf(arr2[i]), 1);
      }
    }
    element.className = arr1.join(" ");
  }

  // Add active class to the current button (highlight it)
  var btnContainer = document.getElementById("myBtnContainer");
  var btns = btnContainer.getElementsByClassName("filter-btn");
  for (var i = 0; i < btns.length; i++) {
    btns[i].addEventListener("click", function() {
      var current = document.getElementsByClassName("active");
      current[0].className = current[0].className.replace(" active", "");
      this.className += " active";
    });
  }

  // NOTE MAKES WHOLE ROW CLICKABLE
  document.querySelectorAll(".challenge-row").forEach(function(element) {
    element.addEventListener('click', function(event) {
      window.document.location = event.target.parentNode.getAttribute('data-href');
    });
  })
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