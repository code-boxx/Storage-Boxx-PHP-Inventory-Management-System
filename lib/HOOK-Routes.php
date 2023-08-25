<?php
// CALLED BY $_CORE->ROUTES->RESOLVE()
// USE THIS TO OVERRIDE URL PAGE ROUTES

// (A) EXACT PATH ROUTING
$routes = [
  // EXAMPLES
  // "/" => "myhome.php", // http://site.com/ > pages/myhome.php
  // "foo/" => "bar.php", // http://site.com/foo/ > pages/bar.php
];

// (B) WILDCARD PATH ROUTING
$wild = [
  "report/" => "REPORT-loader.php"
  // EXAMPLE
  // "category/" => "category.php", // http://site.com/category/* > pages/category.php
];

// (C) REDIRECT TO LOGIN PAGE IF NOT SIGNED IN
$override = function ($path) {
  global $_CORE;
  if (!isset($_SESSION["user"]) && $path!="login/" && $path!="forgot/") {
    if (isset($_POST["ajax"])) { exit("E"); }
    else { $_CORE->redirect("login/"); }
  }
  return $path;
};