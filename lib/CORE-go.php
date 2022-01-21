<?php
// (A) LOAD CONFIG + CORE LIBRARY
require __DIR__ . DIRECTORY_SEPARATOR . "CORE-config.php";
require PATH_LIB . "LIB-Core.php";
$_CORE = new CoreBoxx();

// (B) GLOBAL ERROR HANDLING
function _CORERR ($ex) { global $_CORE; $_CORE->ouch($ex); }
set_exception_handler("_CORERR");

// (C) LOAD DEFAULT MODULES + STARTING SEQUENCE
$_CORE->load("DB");
$_CORE->load("Options");
$_CORE->load("Session");
