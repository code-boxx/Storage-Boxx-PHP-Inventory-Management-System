<?php
// (A) ADMIN ONLY
$_CORE->ucheck("A");

// (B) API ENDPOINTS
$_CORE->autoAPI([
  "save" => ["Inventory", "save"],
  "del" => ["Inventory", "del"],
  "get" => ["Inventory", "get"],
  "move" => ["Inventory", "move"],
  "import" => ["Inventory", "import"]
]);

// (C) INVALID REQUEST
$_CORE->respond(0, "Invalid request", null, null, 400);