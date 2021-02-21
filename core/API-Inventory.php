<?php
// (A) ADMIN REQUESTS
$_CORE->load("Inventory");
if (isset($_POST['reqA'])) { switch ($_POST['reqA']) {
  // (A0) INVALID
  default:
    $_CORE->respond(0, "Invalid request");
    break;

  // (A1) SAVE ITEM
  case "save":
    $_CORE->autoAPI("Inventory", "save");
    break;
  
  // (A2) DELETE ITEM
  case "del":
    $_CORE->autoAPI("Inventory", "del");
    break;

  // (A3) ADD STOCK MOVEMENT
  case "move":
    $result = $_CORE->Inventory->move(
      $_POST['sku'], $_POST['direction'], $_POST['qty'], $_POST['notes']
    );
    $_CORE->respond($result!==false, null, $result!==false?$result:null);
    break;
  
  // (G) SEARCH FOR SKU
  case "findSKU":
    $_CORE->respond(1, "OK", $_CORE->Inventory->findSKU($_POST['search']));
    break;
  
  // (H) GET ITEM BY SKU
  case "get":
    $_CORE->respond(1, "OK", $_CORE->Inventory->get($_POST['sku']));
    break;
}}

// (B) OPEN REQUESTS
if (isset($_POST['req'])) { switch ($_POST['req']) {
  // (B0) INVALID
  default:
    $_CORE->respond(0, "Invalid request");
    break;
}}