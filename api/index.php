<?php
// (A) LOAD CORE
// API MODE FLAG - USE THIS TO TWEAK YOUR SYSTEM BEHAVIORS
// E.G. IF (DEFINED("API_MODE")) { JSON_ENCODE(ARRAY) } ELSE { ARRAY TO HTML }
define("API_MODE", true);
require dirname(__DIR__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "GO.php";

// (B) ENFORCE HTTPS (RECOMMENDED)
if (API_HTTPS && empty($_SERVER["HTTPS"])) {
  $_CORE->respond(0, "Please use HTTPS");
}

// (C) AUTO REGENERATE HTACCESS IF NOT FOUND
$htaccess = PATH_API . ".htaccess";
if (!file_exists($htaccess)) {
  file_put_contents($htaccess, implode("\r\n", [
    "RewriteEngine On",
    "RewriteBase " . HOST_API,
    "RewriteRule ^index\.php$ - [L]",
    "RewriteCond %{REQUEST_FILENAME} !-f",
    "RewriteCond %{REQUEST_FILENAME} !-d",
    "RewriteRule . " . HOST_API . "index.php [L]"
  ]));
}

// (D) PARSE URL PATH INTO AN ARRAY
// (D1) EXTRACT PATH FROM FULL URL
// E.G. http://site.com/api/foo/bar/ >>> $path="/api/foo/bar/"
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// (D2) REMOVE "API" SEGMENT FROM PATH
// E.G. $path="/api/foo/bar/" >>> $path="foo/bar/"
if (substr($path, 0, strlen(HOST_API)) == HOST_API) {
  $path = substr($path, strlen(HOST_API));
}

// (D3) EXPLODE INTO AN ARRAY
// E.G. $path="foo/bar/" >>> $path=["foo", "bar"]
$path = explode("/", rtrim($path, "/"));

// (E) MANAGE REQUEST
// $path[0] IS THE "MODULE CODE"
// $path[1] IS THE "REQUEST"
// REST OF THE DATA IS UP TO YOU - USE $_POST OR $_GET
// E.G. GET USER -> http://site.com/api/user/get/?id=1
$valid = count($path)==2;
if ($valid) { $valid = $path[0]!="index"; }
if ($valid) {
  $_MOD = $path[0];
  $_REQ = $path[1];
  unset($path);
  $api = PATH_API . "API-$_MOD.php";
  $valid = file_exists($api);
}
if ($valid) { require $api; }
else { $_CORE->respond(0, "Invalid request"); } // Bad request
