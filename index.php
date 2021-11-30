<?php
// (A) LOAD CORE ENGINE
require __DIR__ . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "GO.php";

// (B) GENERATE HTACCESS FILE
$htaccess = PATH_BASE . ".htaccess";
if (!file_exists($htaccess)) {
  if (file_put_contents($htaccess, implode("\r\n", [
    "RewriteEngine On",
    "RewriteBase " . HOST_BASE_PATH,
    "RewriteRule ^index\.php$ - [L]",
    "RewriteCond %{REQUEST_FILENAME} !-f",
    "RewriteCond %{REQUEST_FILENAME} !-d",
    "RewriteRule . " . HOST_BASE_PATH . "index.php [L]"
  ])) === false) { exit("Failed to create $htaccess"); }
  header("Location: " . $_SERVER["REQUEST_URI"]);
  exit();
}
unset($htaccess);

// (C) STRIP PATH DOWN TO AN ARRAY
// E.G. HTTP://SITE.COM/HELLO/WORLD/ > $_PATH = ["HELLO", "WORLD"]
$_PATH = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
if (substr($_PATH, 0, strlen(HOST_BASE_PATH)) == HOST_BASE_PATH) {
  $_PATH = substr($_PATH, strlen(HOST_BASE_PATH));
}
$_PATH = rtrim($_PATH, "/");
$_PATH = explode("/", $_PATH);

// (D) AJAX MODE
$_AJAX = $_PATH[0]=="a";

// (E) LOGIN CHECK
if ($_USER===false) {
  if ($_AJAX) { exit("SE"); }
  if (count($_PATH)>1 || $_PATH[0]!="login") {
    header("Location: ". HOST_BASE ."login/");
    exit();
  }
}
if ($_USER!==false && count($_PATH)==1 && $_PATH[0]=="login") {
  header("Location: ". HOST_BASE);
  exit();
}

// (F) LOAD PAGE
// (F1) PHYSICAL PAGE TO LOAD
// HTTP://SITE.COM/ >>> LOAD PAGE-HOME.PHP
// HTTP://SITE.COM/FOO/ >>> LOAD PAGE-FOO.PHP
// HTTP://SITE.COM/FOO/BAR/ >>> LOAD PAGE-FOO-BAR.PHP
$_PAGE = PATH_PAGES . "PAGE-";
$_PAGE .= $_PATH[0]=="" ? "home.php" : implode("-", $_PATH) . ".php";

// (F2) NOT FOUND
if (!file_exists($_PAGE)) {
  http_response_code(404);
  if ($_AJAX) { echo "PAGE NOT FOUND"; }
  else {
    require PATH_PAGES . "TEMPLATE-top.php";
    require PATH_PAGES . "PAGE-404.php";
    require PATH_PAGES . "TEMPLATE-bottom.php";
  }
  exit();
}

// (F3) LOAD PAGE
// FLAGS THAT MAY BE USEFUL IN PAGES
// $_AJAX : AJAX MODE
// $_PAGE : CURRENT PHYSICAL PAGE
if (!$_AJAX) { require PATH_PAGES . "TEMPLATE-top.php"; }
require $_PAGE;
if (!$_AJAX) { require PATH_PAGES . "TEMPLATE-bottom.php"; }
