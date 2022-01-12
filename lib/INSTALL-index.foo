<?php
// (A) LOAD CORE ENGINE
require __DIR__ . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "CORE-go.php";
$_CORE->load("Route");

// (B) AUTO RESOLVE ROUTE
$_CORE->Route->run(function ($_PATH) {
  // (B1) REDIRECT TO LOGIN PAGE IF NOT SIGNED IN
  global $_SESS;
  if (!isset($_SESS["user"]) && $_PATH!="login/") {
    if (isset($_POST["ajax"])) { exit("E"); }
    else {
      header("Location: ".HOST_BASE."login");
      exit();
    }
  }

  // (B2) RETURN $_PATH
  return $_PATH;
});
