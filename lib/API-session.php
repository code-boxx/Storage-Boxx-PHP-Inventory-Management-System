<?php
switch ($_REQ) {
  // (A) INVALID REQUEST
  default:
    $_CORE->respond(0, "Invalid request", null, null, 400);
    break;

  // (B) LOGIN
  case "login":
    $_CORE->autoAPI("Users", "login");
    break;

  // (C) NFC TOKEN LOGIN
  case "intoken":
    $_CORE->autoAPI("Users", "intoken");
    break;

  // (D) LOGOUT
  case "logout":
    $_CORE->autoAPI("Users", "logout");
    break;

  // (E) REQUEST PASSWORD RESET
  case "forgotA":
    $_CORE->autoAPI("Forgot", "request");
    break;

  // (F) PROCESS PASSWORD RESET
  case "forgotB":
    $_CORE->autoAPI("Forgot", "reset");
    break;
}