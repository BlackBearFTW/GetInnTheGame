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
  header('location: ./profile.php?iUserID=' . $_SESSION['user_id']);
}

// NOTE CONNECT TO DATABASE
require_once $_SERVER['DOCUMENT_ROOT'] . "/include/masterInclude.php";