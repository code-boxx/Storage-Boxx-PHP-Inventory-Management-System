<?php
// (A) API ENDPOINTS
$_CORE->autoAPI([
  "save" => ["Push", "save"],
  "del" => ["Push", "del", "A"],
  "send" => ["Push", "send", "A"]
]);

// (B) INVALID REQUEST
$_CORE->respond(0, "Invalid request", null, null, 400);