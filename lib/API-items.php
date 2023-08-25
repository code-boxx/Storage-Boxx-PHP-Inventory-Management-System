<?php
// (A) ADMIN ONLY
$_CORE->ucheck("A");

// (B) API ENDPOINTS
$_CORE->autoAPI([
  "save" => ["Items", "save"],
  "del" => ["Items", "del"],
  "import" => ["Items", "import"],
  "check" => ["Items", "check"]
]);

// (C) INVALID REQUEST
$_CORE->respond(0, "Invalid request", null, null, 400);