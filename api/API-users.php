<?php
// (A) MUST BE SIGNED IN
if (!isset($_SESSION["user"])) {
  $_CORE->respond("E", "Please sign in first");
}

switch ($_REQ) {
  // (B) INVALID REQUEST
  default:
    $_CORE->respond(0, "Invalid request");
    break;

  // (C) SAVE USER
  case "save":
    $_CORE->autoAPI("Users", "save");
    break;

  // (D) DELETE USER
  case "del":
    $_CORE->autoAPI("Users", "del");
    break;
}
