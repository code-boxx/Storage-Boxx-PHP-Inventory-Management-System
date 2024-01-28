<?php
// (A) API ENDPOINTS
$_CORE->autoAPI([
  // (A1) REGULAR LOGIN
  "login" => ["Users", "login"],
  "logout" => ["Users", "logout"],
  // (A2) FORGOT PASSWORD
  "forgotA" => ["Forgot", "request"],
  "forgotB" => ["Forgot", "reset"],
  // (A3) WEB AUTHN LOGIN
  "waregA" => ["WAIN", "regA", true],
  "waregB" => ["WAIN", "regB", true],
  "waunreg" => ["WAIN", "unreg", true],
  "waloginA" => ["WAIN", "loginA"],
  "waloginB" => ["WAIN", "loginB"],
  // (A4) NFC LOGIN
  "nfcadd" => ["NFCIN", "add", "A"],
  "nfcdel" => ["NFCIN", "del", "A"],
  "nfclogin" => ["NFCIN", "login"],
  // (A5) QR LOGIN
  "qradd" => ["QRIN", "add", "A"],
  "qrdel" => ["QRIN", "del", "A"],
  "qrlogin" => ["QRIN", "login"]
]);

// (B) INVALID REQUEST
$_CORE->respond(0, "Invalid request", null, null, 400);