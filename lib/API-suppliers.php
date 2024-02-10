<?php
// (A) ADMIN ONLY
$_CORE->ucheck("A");

// (B) API ENDPOINTS
$_CORE->autoAPI([
  "save" => ["Suppliers", "save"],
  "del" => ["Suppliers", "del"],
  "import" => ["Suppliers", "import"],
  "getItem" => ["Suppliers", "getItem"],
  "saveItem" => ["Suppliers", "saveItem"],
  "delItem" => ["Suppliers", "delItem"],
  "importItem" => ["Suppliers", "importItem"]
]);

// (C) INVALID REQUEST
$_CORE->respond(0, "Invalid request", null, null, 400);