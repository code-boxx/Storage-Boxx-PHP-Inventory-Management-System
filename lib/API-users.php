<?php
// (A) ADMIN ONLY
$_CORE->ucheck("A");

// (B) API ENDPOINTS
$_CORE->autoAPI([
  "save" => ["Users", "save"],
  "del" => ["Users", "del"],
  "token" => ["Users", "token"],
  "notoken" => ["Users", "notoken"]
]);

// (C) INVALID REQUEST
$_CORE->respond(0, "Invalid request", null, null, 400);