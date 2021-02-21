<?php
// (A) ADMIN REQUESTS
$_CORE->load("User");
if (isset($_POST['reqA'])) { switch ($_POST['reqA']) {
  // (A0) INVALID
  default:
    $_CORE->respond(0, "Invalid request");
    break;

  // (A1) SAVE USER
  case "save":
    $_CORE->autoAPI("User", "save");
    break;

  // (A2) DELETE USER
  case "del":
    $_CORE->autoAPI("User", "del");
    break;
}}

// (B) OPEN REQUESTS
if (isset($_POST['req'])) { switch ($_POST['req']) {
  // (B0) INVALID
  default:
    $_CORE->respond(0, "Invalid request");
    break;

  // (B1) LOGIN
  case "login":
    if (isset($_SESSION['user'])) { $_CORE->respond(1, "Already signed in"); }
    $_POST['session'] = true;
    $_CORE->autoAPI("User", "verify");
    break;

  // (B2) LOGOFF
  case "logoff":
    unset($_SESSION['user']);
    $_CORE->respond(1);
    break;
}}