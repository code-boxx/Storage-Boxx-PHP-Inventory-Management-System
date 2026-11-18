<?php
// (PHASE A) BASE SETTINGS
// (PHASE B) BASE REQUIREMENTS CHECK
// (PHASE C) UPDATE CHECK
// (PHASE D) INSTALLATION HTML PAGE
// (PHASE E) GENERATE & VERIFY HTACCESS FILE
// (PHASE F) ACTUAL INSTALLATION
// (PHASE G) CLEAN UP
require __DIR__ . DIRECTORY_SEPARATOR . "CORE-Config.php";
require __DIR__ . DIRECTORY_SEPARATOR . "LIB-Core.php";

// (PHASE A2) REFUSE TO RUN ON AN ALREADY INSTALLED SYSTEM
if (JWT_SECRET !== "YOUR-SECRET-KEY" || JWT_ISSUER !== "YOUR-NAME") {
  http_response_code(403);
  exit("Installation has already been completed.");
}

$_CORE->load("Install");
$_PHASE = isset($_POST["phase"]) ? $_POST["phase"] : "B";
while ($_PHASE != null) { $_PHASE = $_CORE->Install->$_PHASE(); }
