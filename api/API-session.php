<?php
switch ($_REQ) {
  // (A) INVALID REQUEST
  default:
    $_CORE->respond(0, "Invalid request", null, null, 400);
    break;

  // (B) LOGIN
  case "logon": case "login":
    $_CORE->autoAPI("Users", "inJWT");
    break;

  // (C) LOGOFF
  case "logoff": case "logout":
    setcookie("jwt", null, -1, "/", HOST_NAME, API_HTTPS);
    $_CORE->respond(1, "OK");
    break;
}
