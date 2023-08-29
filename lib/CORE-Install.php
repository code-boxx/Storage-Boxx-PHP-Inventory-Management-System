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
$_CORE->load("Install");
$_PHASE = isset($_POST["phase"]) ? $_POST["phase"] : "B";
while ($_PHASE != null) { $_PHASE = $_CORE->Install->$_PHASE(); }