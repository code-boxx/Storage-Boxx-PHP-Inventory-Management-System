<?php
// SEARCH FOR @CHANGE AND UPDATE THE SETTINGS TO YOUR OWN!

// (A) HOST
define("HOST_BASE", "http://localhost/"); // CHANGED BY INSTALLER
define("HOST_NAME", parse_url(HOST_BASE, PHP_URL_HOST));
define("HOST_BASE_PATH", parse_url(HOST_BASE, PHP_URL_PATH));
define("HOST_API", HOST_BASE_PATH . "api/");
define("HOST_API_BASE", HOST_BASE . "api/");
define("HOST_ASSETS", HOST_BASE . "assets/");

// (B) DATABASE - @CHANGE
define("DB_HOST", "localhost"); // CHANGED BY INSTALLER
define("DB_NAME", "storageboxx"); // CHANGED BY INSTALLER
define("DB_CHARSET", "utf8");
define("DB_USER", "root"); // CHANGED BY INSTALLER
define("DB_PASSWORD", ""); // CHANGED BY INSTALLER

// (C) ERROR HANDLING
/* @CHANGE - DON'T DISPLAY ERRORS BUT KEEP IN ERROR LOG ON LIVE SYSTEMS
// (C1) RECOMMENDED FOR LIVE SERVER
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 0);
ini_set("log_errors", 1);
ini_set("error_log", "PATH/error.log");
define("ERR_SHOW", false); */

// (C2) RECOMMENDED FOR DEVELOPMENT SERVER
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
ini_set("log_errors", 0);
define("ERR_SHOW", true);

// (D) JSON WEB TOKEN
define("JWT_SECRET", "M7lWydOxnQIeQeK_YbLbjMO2LT2s2zAghML-lekJkVrBhyMw"); // CHANGED BY INSTALLER
define("JWT_ISSUER", "localhost"); // CHANGED BY INSTALLER
define("JWT_ALGO", "HS256");
define("JWT_EXPIRE", 0); // in seconds, 0 for none

// (E) API ENDPOINT
define("API_HTTPS", false); // CHANGED BY INSTALLER
define("API_CORS", false); // CHANGED BY INSTALLER
// define("API_CORS", false); // no cors, accept host_name only
// define("API_CORS", true); // any domain + mobile apps
// define("API_CORS", "site-a.com"); // this domain only
// define("API_CORS", ["site-a.com", "site-b.com"]); // multiple domains

// (F) AUTOMATIC SYSTEM PATH
define("PATH_LIB", __DIR__ . DIRECTORY_SEPARATOR);
define("PATH_BASE", dirname(PATH_LIB) . DIRECTORY_SEPARATOR);
define("PATH_API", PATH_BASE . "api" . DIRECTORY_SEPARATOR);
define("PATH_ASSETS", PATH_BASE . "assets" . DIRECTORY_SEPARATOR);
define("PATH_PAGES", PATH_BASE . "pages" . DIRECTORY_SEPARATOR);
