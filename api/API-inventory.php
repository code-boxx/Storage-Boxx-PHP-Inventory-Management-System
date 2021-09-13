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
