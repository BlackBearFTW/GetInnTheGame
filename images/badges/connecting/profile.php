<?php 

session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ./registration/login.php");
    exit;
}

// NOTE CONNECT TO DATABASE
require_once "db_config.php";

// NOTE GET USER ID FROM URL BAR
$user_id = $_SESSION['user_id'];
if (!isset($_GET["user_id"]))  {
 echo "<script>window.location.href='./profile.php?user_id=$user_id'</script>";
} else {
    // NOTE MAKE URL STRING SAFE FOR DATABASE
    $user_id = preg_replace("/([^a-zA-Z0-9]+)/","", mysqli_real_escape_string($link, $_GET['user_id']));

} 


?>

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
    <a class="dropdown-item kleur-text-knop" href= ' ./profile.php?user_id=<?php echo $_SESSION['user_id']; ?>'>Profile</a>
    <a class="dropdown-item kleur-text-knop" href= './ranking.php'>Ranking</a>

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
            <div class="col-8"><h2 class="center-dit color-wit"><strong>Profile</strong></h2></div>
            
            <div class="col-2"></div>


<!-- Tthis is the brown ish top -->

            
        </div>
        <div class="row color-grijs">
            <div class="col-12 mh-100" style="padding: 10px;"></div>
        </div>
        <br><br><br>


<?php 
// <----- SECTION CODE FUNCTION: GET DATA ABOUT USER ----->

// NOTE GET DATA FROM USER
$user_data = mysqli_query($link, "SELECT firstname, lastname, points, company.company_name FROM users, company WHERE company.company_id = users.company_id AND user_id = $user_id");

// NOTE PRINTS DATA FROM USER PROFILES 
while($row = mysqli_fetch_assoc($user_data)) {
    echo $row['firstname'] . " " . $row['lastname'] . "<br>";
    echo $row['company_name'] . "<br>";
    echo $row['points'] . " Punten<br>";

}

echo "<br>";

// <----- SECTION CODE FUNCTION: GET BADGES AND DISPLAY THEM ----->
    
    // NOTE IF USER DOESNT HAVE THIS BADGE
    $b_img_result = mysqli_query($link, "SELECT cd.name, cd.description, badge_id, img_path FROM badge, challenge_data AS cd WHERE cd.challenge_id = badge.challenge_id AND badge_id IN (SELECT badge_id FROM challenge_status WHERE user_id = $user_id AND status = 'COMPLETED') ORDER BY cd.name ASC");

      while($row = mysqli_fetch_assoc($b_img_result)) {
        $row['is_completed'] = true;
        $all_challenges[] = $row;
        }
        
        // NOTE IF THE USER HAS THIS BADGE
        $b_img_result = mysqli_query($link, "SELECT cd.name, cd.description, badge_id, img_path FROM badge, challenge_data AS cd WHERE cd.challenge_id = badge.challenge_id AND badge_id  NOT IN (SELECT badge_id FROM challenge_status WHERE user_id = $user_id) ORDER BY cd.name ASC");
        while($row = mysqli_fetch_assoc($b_img_result)) {
          $row['is_completed'] = false;
          $false_challenge[] = $row;
          }

          // NOTE IF USER DOES HAVE THE BADGE BUT THE STATUS IS PENDING
    $b_img_result = mysqli_query($link, "SELECT cd.name, cd.description, badge_id, img_path FROM badge, challenge_data AS cd WHERE cd.challenge_id = badge.challenge_id AND badge_id IN (SELECT badge_id FROM challenge_status WHERE user_id = $user_id AND status = 'PENDING') ORDER BY cd.name ASC");

    while($row = mysqli_fetch_assoc($b_img_result)) {
      $row['is_completed'] = false;
      $false_challenge[] = $row;
      }
          
          
      // NOTE SORT ALL RECORDS IN ORDER OF NAME
function cmp($a, $b)
{
return strcasecmp($a["name"], $b["name"]);
}

usort($false_challenge, "cmp");
$final_challenges = array_merge($all_challenges, $false_challenge);

        // NOTE ECHO ALL BADGES AND THEIR LIGHTBOX
          foreach ($final_challenges as $all_ch) {
        $badge_id = $all_ch['badge_id'];
        $img_path = $all_ch['img_path'];
        $challenge_name = $all_ch['name'];
        $challenge_description = $all_ch['description'];
        $is_completed = $all_ch['is_completed'];

        if ($is_completed == false) { // 
        // NOTE GENERATE A NOT COMPLETED BUTTON
        echo "<button type='button' class='btn  niet-behaald margin-iets schaduw-doos' data-toggle='modal' data-target='#badge_model_". $badge_id ."'><img src='./images/badges/". $img_path ."' width=80px height=auto alt='UNKNOWN'></button>";
        
        // NOTE GENERATE MODAL HEADER FOR NOT COMPLETED CHALLENGE
        echo "<div class='modal fade' id='badge_model_". $badge_id ."' tabindex='-1' role='dialog' aria-labelledby='exampleModalCenterTitle' aria-hidden='true'>
        <div class='modal-dialog modal-dialog-centered' role='document'>
          <div class='modal-content  niet-behaald' style='opacity: 1 !important;'>";

        } elseif ($is_completed == true) {
            // NOTE GENERATE A COMPLETED BUTTON
          echo "<button type='button' class='btn  behaald margin-iets schaduw-doos' data-toggle='modal' data-target='#badge_model_". $badge_id ."'><img src='./images/badges/". $img_path ."' width=80px height=auto alt='UNKNOWN'></button>";
        
          // NOTE GENERATE MODAL HEADER FOR COMPLETED CHALLENGE
        echo "<div class='modal fade' id='badge_model_". $badge_id ."' tabindex='-1' role='dialog' aria-labelledby='exampleModalCenterTitle' aria-hidden='true'>
        <div class='modal-dialog modal-dialog-centered' role='document'>
          <div class='modal-content  behaald'>";
        }
      
        // NOTE ECHO THE MODAL (LIGHTBOX)
        echo " 
          <div class='modal-header' style='border-bottom: 0 none !important;'>
          <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
            <span aria-hidden='true'>&times;</span>
          </button>
        </div>
        <div class='modal-body'>
        <div class='container-fluid'>

          <div class='row text-center'>
            <div class='col'></div>
            <div class='col-10'><img src='./images/badges/". $img_path ."' class='img-fluid' alt='UNKNOWN'></div>
            <div class='col'></div>
          </div>

          <br>

          <div class='row text-center'>
            <div class='col-1'></div>
            <div class='col'>
            <h5>$challenge_name</h5>
            $challenge_description
            </div>
            <div class='col-1'></div>
          </div>

                </div>
                 </div>
          </div>
        </div>
      </div>";


    }

?>
</div>
</body>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<link rel="stylesheet" href="./css/stylesheet.css" type="text/css">
<link rel="stylesheet" href="./css/stylesheet-main.css" type="text/css">
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

</html>