<?php
// (A) ADMIN ONLY
if (!isset($_SESS["user"])) {
  $_CORE->respond(0, "Please sign in first", null, null, 403);
}

switch ($_REQ) {
  // (B) INVALID REQUEST
  default:
    $_CORE->respond(0, "Invalid request", null, null, 400);
    break;

  // (C) SAVE ITEM
  case "save":
    $_CORE->autoAPI("Inventory", "save");
    break;

  // (D) DELETE ITEM
  case "del":
    $_CORE->autoAPI("Inventory", "del");
    break;

  // (E) ADD STOCK MOVEMENT
  case "move":
    $result = $_CORE->autoCall("Inventory", "move");
    $_CORE->respond($result!==false, null, $result!==false?$result:null);
    break;

  // (F) GET ITEM BY SKU
  case "get":
    $_CORE->autoGETAPI("Inventory", "get");
    break;
}