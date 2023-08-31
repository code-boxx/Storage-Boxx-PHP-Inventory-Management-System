<?php
// (A) ADMIN ONLY
$_CORE->ucheck("A");

// (B) API ENDPOINTS
$_CORE->autoAPI([
  "sup" => ["Autocomplete", "sup"],
  "supitem" => ["Autocomplete", "supitem"],
  "user" => ["Autocomplete", "user"],
  "item" => ["Autocomplete", "item"],
  "sku" => ["Autocomplete", "sku"],
  "batch" => ["Autocomplete", "batch"]
]);

// (C) INVALID REQUEST
$_CORE->respond(0, "Invalid request", null, null, 400);