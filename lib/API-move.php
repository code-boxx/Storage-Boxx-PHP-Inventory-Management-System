<?php
// (A) ADMIN ONLY
$_CORE->ucheck("A");

// (B) API ENDPOINTS
$_CORE->autoAPI([
  "saveB" => ["Move", "saveB"],
  "delB" => ["Move", "delB"],
  "saveM" => ["Move", "saveMC"]
]);

// (C) INVALID REQUEST
$_CORE->respond(0, "Invalid request", null, null, 400);