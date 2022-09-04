<?php
// (A) REDIRECT TO LOGIN PAGE IF NOT SIGNED IN
$override = function ($path) {
  global $_SESS;
  if (!isset($_SESS["user"]) && $path!="login/") {
    if (isset($_POST["ajax"])) { exit("E"); }
    else { header("Location: ".HOST_BASE."login"); exit(); }
  }
  return $path;
};

// (B) WILD CARD
$wild = ["report/" => "REPORT-loader.php"];