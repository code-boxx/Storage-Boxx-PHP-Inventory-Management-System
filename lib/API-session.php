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

  // (C) LOGOUT
  case "logout":
    $_CORE->autoAPI("Users", "logout");
    break;

  // (D) REQUEST PASSWORD RESET
  case "forgotA":
    $_CORE->autoAPI("Forgot", "request");
    break;

  // (E) PROCESS PASSWORD RESET
  case "forgotB":
    $_CORE->autoAPI("Forgot", "reset");
    break;
}