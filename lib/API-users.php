<?php
// (A) ADMIN ONLY
$_CORE->ucheck("A");

// (B) API ENDPOINTS
$_CORE->autoAPI([
  "get" => ["Users", "get"],
  "getAll" => ["Users", "getAll"],
  "save" => ["Users", "save"],
  "del" => ["Users", "del"],
  "import" => ["Users", "import"]
]);

// (C) INVALID REQUEST
$_CORE->respond(0, "Invalid request", null, null, 400);