<?php 
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ../index.php");
    exit;
}

// NOTE IF USER DOES NOT HAVE A TOKEN
if (!isset($_GET['token'])) {
    header("location: ./login.php");
  } elseif (empty($_GET['token'])) {
    header("location: ./login.php"); 
  }

  // Include config file
require_once "../db_config.php";

// NOTE GET TOKEN FROM URL
$activate_token = $_GET['token'];

$query = mysqli_query($link, "SELECT `activate_token` FROM `users` WHERE activate_token= '$activate_token'");


    if (!$query)
    {
        die('Error: ' . mysqli_error($link));
    }

if(mysqli_num_rows($query) > 0){

    // NOTE IF CODE IS CORRECT ACTIVATE ACCOUNT
    mysqli_query($link, "UPDATE `users` SET `activate_token`= NULL WHERE `activate_token` = '$activate_token'");
    echo "Je account is geactiveerd!";
    echo "<br><a href='./login.php'>&#10229 Ga naar het login scherm</a>";

}else{ // NOTE IF CODE IS NOT CORRECT
    echo "Sorry, deze token bestaat niet meer :(";
    echo "<br><a href='./login.php'>&#10229 Ga naar het login scherm</a>";
}


 
?>

