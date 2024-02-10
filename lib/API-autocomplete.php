<?php
// (A) ADMIN ONLY
$_CORE->ucheck("A");

// (B) API ENDPOINTS
$_CORE->autoAPI([
  "sup" => ["Autocomplete", "sup"],
  "supitem" => ["Autocomplete", "supitem"],
  "cus" => ["Autocomplete", "cus"],
  "user" => ["Autocomplete", "user"],
  "item" => ["Autocomplete", "item"],
  "sku" => ["Autocomplete", "sku"],
  "deliver" => ["Autocomplete", "deliver"],
  "purchase" => ["Autocomplete", "purchase"]
]);

// (C) INVALID REQUEST
$_CORE->respond(0, "Invalid request", null, null, 400);