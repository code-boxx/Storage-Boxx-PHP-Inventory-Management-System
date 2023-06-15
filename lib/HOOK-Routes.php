<?php
// CALLED BY $_CORE->ROUTES->RESOLVE()
// USE THIS TO OVERRIDE URL PAGE ROUTES

// (A) REDIRECT TO LOGIN PAGE IF NOT SIGNED IN
$override = function ($path) {
  global $_CORE;
  if (!isset($_SESSION["user"]) && $path!="login/" && $path!="forgot/") {
    if (isset($_POST["ajax"])) { exit("E"); }
    else { $_CORE->redirect("login/"); }
  }
  return $path;
};

// (B) WILD CARD
$wild = ["report/" => "REPORT-loader.php"];