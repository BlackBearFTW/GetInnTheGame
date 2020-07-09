<?php

session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: ./registration/login.php");
  exit;
}

// NOTE GET USER ID FROM URL BAR
if (isset($_GET["iUserID"]) && is_numeric($_GET["iUserID"])) {
  $iUserID = trim($_GET["iUserID"]);
} else {
  header('location: ./userProfile.php?iUserID=' . $_SESSION['user_id']);
}

// NOTE CONNECT TO MASTER INCLUDE FILE
require_once $_SERVER['DOCUMENT_ROOT'] . "/include/masterInclude.php";


// Check if user has a profile picture, otherwise load default profile picture
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/images/avatars/' . $iUserID . '.jpg')) {
  $sProfileImgPath =  '/images/avatars/' . $iUserID . '.jpg';
} else {
  $sProfileImgPath = '/images/avatars/default.jpg';
}

if ($stmt = mysqli_prepare($link, "SELECT firstname, lastname, company_name FROM users INNER JOIN company ON company.company_id = users.company_id WHERE users.user_id = ?")) {

  /* bind parameters for markers */
  mysqli_stmt_bind_param($stmt, "i", $iUserID);

  /* execute query */
  mysqli_stmt_execute($stmt);

  $result = mysqli_stmt_get_result($stmt);

  while ($row = mysqli_fetch_assoc($result)) {
    $sFullName = $row['firstname'] . ' ' . $row['lastname'];
    $sCompanyName = $row['company_name'];
  }

  /* close statement */
  mysqli_stmt_close($stmt);
}


?>

<body>

  <div class="container-fluid ">

    <!-- LOGOUT / PROFILE IMAGE / SETTINGS -->
    <div class="row mt-3">
      <div class="col-3"></div>
      <div class="col">
        <img src="<?php echo $sProfileImgPath; ?>" alt="" class="img-fluid mx-auto mt-3 prof-img">
      </div>
      <div class="col-3"></div>
    </div>

    <!-- NAME / COMPANY / POINTS DATA -->
    <div class="row">
      <div class="col-2"></div>
      <div class="col text-center">
        <h3 class="mt-2 mb-0 font-bold"><?php echo $sFullName; ?></h3>
        <h4 class="font-regular mb-0"><?php echo $sCompanyName; ?><br>150 Points</h4>
      </div>
      <div class="col-2"></div>
    </div>

    <!-- BADGES -->
    <div class="row" id="badges"></div>


  </div> <!-- END OF CONTAINER -->

</body>
<script>
  var badges = document.getElementById('badges');
  var iUserID = <?php echo $iUserID; ?>

  function profileUpdate() {
    let fData = new FormData()
    fData.append('iUserID', iUserID);

    fetch('/include/fetch-api/profileUpdate.php', {
        method: 'POST',
        body: fData,
      }).then(response => response.text())
      .then(val => list.innerHTML = val);
  }

  document.addEventListener('load', profileUpdate());
  setInterval(profileUpdate(), 3000);
</script>