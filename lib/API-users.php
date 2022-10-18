<?php
// (A) REGISTERED USERS ONLY
if (!isset($_SESS["user"])) {
  $_CORE->respond(0, "Please sign in first", null, null, 403);
}

switch ($_REQ) {
  // (B) INVALID REQUEST
  default:
    $_CORE->respond(0, "Invalid request", null, null, 400);
    break;

  // (C) SAVE USER
  case "save":
    $_CORE->autoAPI("Users", "save");
    break;

  // (D) DELETE USER
  case "del":
    $_CORE->autoAPI("Users", "del");
    break;

  // (E) CREATE LOGIN TOKEN
  case "token":
    $result = $_CORE->autoCall("Users", "token");
    $_CORE->respond($result!==false, null, $result!==false?$result:null);
    break;

  // (F) NULLIFY LOGIN TOKEN
  case "notoken":
    $_CORE->autoAPI("Users", "notoken");
    break;
}