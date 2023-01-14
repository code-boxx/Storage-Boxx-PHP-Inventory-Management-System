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

  // (C) SAVE SUPPLIER
  case "save":
    $_CORE->autoAPI("Suppliers", "save");
    break;

  // (D) DELETE SUPPLIER
  case "del":
    $_CORE->autoAPI("Suppliers", "del");
    break;

  // (E) IMPORT SUPPLIER
  case "import":
    $_CORE->autoAPI("Suppliers", "import");
    break;

  // (F) SAVE SUPPLIER ITEM
  case "saveItem":
    $_CORE->autoAPI("Suppliers", "saveItem");
    break;

  // (G) DELETE SUPPLIER ITEM
  case "delItem":
    $_CORE->autoAPI("Suppliers", "delItem");
    break;

  // (H) IMPORT SUPPLIER ITEM
  case "importItem":
    $_CORE->autoAPI("Suppliers", "importItem");
    break;
}