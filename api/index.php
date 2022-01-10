<?php
// (A) API MODE FLAG
// use this to tweak your system behaviors
// e.g. if (defined("api_mode")) { json_encode(data) } else { display html }
define("API_MODE", true);

// (B) LOAD CORE
require dirname(__DIR__) . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "CORE-go.php";

// (C) ENFORCE HTTPS (RECOMMENDED)
if (API_HTTPS && empty($_SERVER["HTTPS"])) {
  $_CORE->respond(0, "Please use HTTPS", null, null, 426);
}

// (D) GET CLIENT ORIGIN
$_OGN = $_SERVER["HTTP_ORIGIN"] ??
        $_SERVER["HTTP_REFERER"] ??
        $_SERVER["REMOTE_ADDR"] ??
        "" ;

// (E) CORS SUPPORT - ONLY IF NOT LOCALHOST
if (!in_array($_OGN, ["::1", "127.0.0.1", "localhost"])) {
  // (E1) PARSE ORIGIN HOST NAME
  $_OGN_HOST = parse_url($_OGN, PHP_URL_HOST);

  // (E2) FALSE - ONLY CALLS FROM HOST_NAME ALLOWED
  if (API_CORS===false && $_OGN_HOST!=HOST_NAME) { $access = false; }

  // (E3) STRING - ALLOW CALLS FROM API_CORS ONLY
  else if (is_string(API_CORS) && $_OGN_HOST!=API_CORS) { $access = false; }

  // (E4) ARRAY - SPECIFIED DOMAINS IN API_CORS ONLY
  else if (is_array(API_CORS) && !in_array($_OGN_HOST, API_CORS)) { $access = false; }

  // (E5) TRUE - ANYTHING GOES
  else { $access = true; $_OGN = "*"; }

  // (E6) ACCESS DENIED
  if ($access === false) {
    $_CORE->respond(0, "Calls from $_OGN not allowed", null, null, 403);
  }

  // (E7) OUTPUT CORS HEADERS IF REQUIRED
  if ($_OGN_HOST != HOST_NAME) {
    header("Access-Control-Allow-Origin: $_OGN");
    header("Access-Control-Allow-Credentials: true");
  }
}

// (F) PARSE URL PATH INTO AN ARRAY
// (F1) EXTRACT PATH FROM FULL URL
// E.G. HTTP://SITE.COM/API/FOO/BAR/ > $_PATH="FOO/BAR"
$_PATH = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$_PATH = substr($_PATH, strlen(HOST_API));
$_PATH = rtrim($_PATH, "/");

// (F2) EXPLODE INTO AN ARRAY
// E.G. $_PATH="FOO/BAR/" > $_PATH=["FOO", "BAR"]
$_PATH = explode("/", $_PATH);

// (G) MANAGE REQUEST
// (G1) VALID API REQUEST?
$valid = count($_PATH)==2;
if ($valid) {
  $_MOD = $_PATH[0];
  $_REQ = $_PATH[1];
  $valid = file_exists(PATH_API . "API-$_MOD.php");
}

// (G2) LOAD API HANDLER
if ($valid) {
  // CLEAN UP
  unset($access); unset($_PATH); unset($valid);

  // FLAGS THAT ARE USEFUL IN YOUR API
  // $_MOD : requested module. e.g. user
  // $_REQ : requested action. e.g. save
  // $_OGN : client origin. e.g. https://site.com/
  // $_OGN_HOST : host name. e.g. site.com
  require PATH_API . "API-$_MOD.php";
} else { $_CORE->respond(0, "Invalid request", null, null, 400); }
