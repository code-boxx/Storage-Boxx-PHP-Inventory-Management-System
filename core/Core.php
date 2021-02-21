<?php
// (A) SETTINGS
// (A1) ERROR REPORTING
error_reporting(E_ALL & ~E_NOTICE);

// (A2) DATABASE SETTINGS - CHANGE TO YOUR OWN!
define('DB_HOST', 'localhost');
define('DB_NAME', 'test');
define('DB_CHARSET', 'utf8');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// (A3) FILE PATH
define("PATH_ROOT", dirname(__DIR__) . DIRECTORY_SEPARATOR);
define("PATH_CORE", __DIR__ . DIRECTORY_SEPARATOR);
define("PATH_PAGES", PATH_ROOT . "pages" . DIRECTORY_SEPARATOR);
define("PATH_PUBLIC", PATH_ROOT . "public" . DIRECTORY_SEPARATOR);

// (A4) HOST
define("URL_BASE", "http://localhost/"); // CHANGE TO YOUR OWN!
define("URL_PATH_BASE", parse_url(URL_BASE, PHP_URL_PATH));
define("URL_PATH_API", "api");
define("URL_API", URL_BASE . URL_PATH_API . "/");
define("URL_PUBLIC", URL_BASE . "public/");

// (A5) EMAIL
define("EMAIL_FROM", "sys@site.com"); // CHANGE TO YOUR OWN!

// (A6) PAGINATION
define("PG_PER", "20"); // ENTRIES PER PAGE
define("PG_ADJ", "2"); // ADJACENT SQUARES FOR HTML PAGINATION

// (B) START ENGINE
session_start();
require PATH_CORE . "LIB-Core.php";
$_CORE = new Core();