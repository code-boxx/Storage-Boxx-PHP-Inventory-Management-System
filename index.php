<?php
// (A) LOAD CORE ENGINE
require __DIR__ . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "GO.php";

// (B) GENERATE HTACCESS FILE
$htaccess = PATH_BASE . ".htaccess";
if (!file_exists($htaccess)) {
  file_put_contents($htaccess, implode("\r\n", [
    "RewriteEngine On",
    "RewriteBase " . HOST_BASE_PATH,
    "RewriteRule ^index\.php$ - [L]",
    "RewriteCond %{REQUEST_FILENAME} !-f",
    "RewriteCond %{REQUEST_FILENAME} !-d",
    "RewriteRule . " . HOST_BASE_PATH . "index.php [L]"
  ]));
  header("Location: " . $_SERVER["REQUEST_URI"]);
  exit();
}

// (C) STRIP PATH DOWN TO AN ARRAY
// E.G. HTTP://SITE.COM/HELLO/WORLD/ > $_PATH = ["HELLO", "WORLD"]
$_PATH = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
if (substr($_PATH, 0, strlen(HOST_BASE_PATH)) == HOST_BASE_PATH) {
  $_PATH = substr($_PATH, strlen(HOST_BASE_PATH));
}
$_PATH = rtrim($_PATH, "/");
$_PATH = explode("/", $_PATH);

// (D) AJAX MODE
$pgajax = $_PATH[0]=="a";

// (D) LOGIN CHECK
if (!isset($_SESSION["user"])) {
  if ($pgajax) { exit("SE"); }
  if (count($_PATH)>1 || $_PATH[0]!="login") {
    header("Location: ". HOST_BASE ."login/");
    exit();
  }
}
if (isset($_SESSION["user"]) && $_PATH[0]=="login") {
  header("Location: ". HOST_BASE);
  exit();
}

// (E) LOAD PAGE
// HTTP://SITE.COM/ >>> LOAD PAGE-HOME.PHP
// HTTP://SITE.COM/FOO/ >>> LOAD PAGE-FOO.PHP
// HTTP://SITE.COM/FOO/BAR/ >>> LOAD PAGE-FOO-BAR.PHP
// NOT FOUND >>> LOAD PAGE-404.PHP
$pgfile = PATH_PAGES . "PAGE-";
$pgfile .= $_PATH[0]=="" ? "home.php" : implode("-", $_PATH) . ".php";
$pgexist = file_exists($pgfile);
if (!$pgexist) {
  http_response_code(404);
  if ($pgajax) { exit("PAGE NOT FOUND"); }
}
if (!$pgajax) { require PATH_PAGES . "TEMPLATE-top.php"; }
require $pgexist ? $pgfile : PATH_PAGES . "PAGE-404.php";
if (!$pgajax) { require PATH_PAGES . "TEMPLATE-bottom.php"; }
