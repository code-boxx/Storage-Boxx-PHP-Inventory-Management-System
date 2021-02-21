<?php
// (A) LOAD CORE ENGINE
require __DIR__ . DIRECTORY_SEPARATOR . "core" . DIRECTORY_SEPARATOR . "Core.php";

// (B) STRIP PATH DOWN TO AN ARRAY
// E.G. HTTP://SITE.COM/HELLO/WORLD/ > $_PATH = ["HELLO", "WORLD"]
$_PATH = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (substr($_PATH, 0, strlen(URL_PATH_BASE)) == URL_PATH_BASE) {
  $_PATH = substr($_PATH, strlen(URL_PATH_BASE));
}
$_PATH = rtrim($_PATH, '/');
$_PATH = explode("/", $_PATH);

// (C) API CALL
// E.G. HTTP://SITE.COM/API/MODULE/ > $_PATH = ["API", "MODULE"]
if ($_PATH[0]==URL_PATH_API) {
  // (C1) INVALID URL PATH
  if (count($_PATH) != 2) { $_CORE->respond(0, "Invalid API call"); }

  // (C2) INVALID API REQUEST OR INSUFFICIENT PERMISSION
  if (!isset($_POST['req']) && !isset($_POST['reqA'])) { 
    $_CORE->respond(0, "Invalid API Request"); 
  }
  if (isset($_POST['reqA']) && !isset($_SESSION['user'])) { 
    $_CORE->respond(0, "No access permission");
  }

   // (C3) LOAD API MODULE
  $_APIFILE = PATH_CORE . "API-" . $_PATH[1] . ".php";
  if (file_exists($_APIFILE)) { require $_APIFILE; }
  else { exit($_CORE->respond(0, "Invalid Module")); }
}

// (D) LOAD HTML PAGE
else {
  // (D1) NOT SIGNED IN
  if (!isset($_SESSION['user']) && $_PATH[0]!="login") {
    if (isset($_POST['ajax'])) { exit("BADSESS"); }
    header('Location: '. URL_BASE .'login');
    exit();
  }

  // (D2) PAGE TO LOAD
  // IF $_PATH == [], WILL LOAD PAGES/PAGE-HOME.HTML
  // IF $_PATH == ["SINGLE"], WILL LOAD PAGES/PAGE-SINGLE.HTML
  // If $_PATH == ["MANY", "SECTIONS"], WILL LOAD PAGES/PAGE-MANY-SECTIONS.HTML
  $_HFILE = PATH_PAGES . "PAGE-";
  if (count($_PATH)==1) { $_HFILE .= $_PATH[0]=="" ? "home" : $_PATH[0] ; }
  else { $_HFILE .= implode("-", $_PATH) ; }
  $_HFILE .= ".php";

  // (D3) THROW 404 IF FILE NOT FOUND
  if (file_exists($_HFILE)) { require $_HFILE; }
  else { require PATH_PAGES . "PAGE-404.php"; }
}