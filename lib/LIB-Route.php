<?php
class Route extends Core {
  // (A) RUN URL ENGINE
  private $path; // current url path
  private $pathlen; // current url path length
  function run () {
    // (A1) CURRENT URL PATH
    $this->path = rtrim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/") . "/";

    // (A2) THIS IS AN API REQUEST
    if (
      strlen($this->path) >= strlen(HOST_API) &&
      substr($this->path, 0, strlen(HOST_API)) == HOST_API
    ) {
      define("API_MODE", 1);
      $this->api();
    }

    // (A3) A "NORMAL" HTTP REQUEST
    else {
      define("WEB_MODE", 1);
      $this->resolve();
    }
  }

  // (B) RESOLVE CURRENT URL ROUTE
  function resolve () {
    // (B1) GET CURRENT URL PATH
    // http://site.com/ > $this->path = "/"
    // http://site.com/hello/world/ > $this->path = "hello/world/"
    if (substr($this->path, 0, strlen(HOST_BASE_PATH)) == HOST_BASE_PATH) {
      $this->path = substr($this->path, strlen(HOST_BASE_PATH));
    }
    $this->path = rtrim($this->path, "/") . "/";
    $this->pathlen = strlen($this->path);

    // (B2) PRE-RESOLVE HOOK
    require PATH_LIB . "CORE-Routes.php";
    if (isset($override)) { $this->path = $override($this->path); }

    // (B3) EXACT ROUTES HAVE PRECEDENCE
    if (isset($routes[$this->path])) {
      $this->load($routes[$this->path]);
      return;
    }

    // (B4) WILDCARD ROUTES
    if (isset($wild)) { foreach ($wild as $p=>$f) {
      $wildlen = strlen($p);
      if ($wildlen > $this->pathlen) { continue; }
      if (substr($this->path, 0, $wildlen) == $p) {
        $this->load($f);
        return;
      }
    }}

    // (B5) AUTO RESOLVE OTHERWISE
    $this->load($this->path=="/"
      ? "PAGE-home.php"
      : "PAGE-" . str_replace("/", "-", rtrim($this->path, "/")) . ".php"
    );
  }

  // (C) LOAD HTML PAGE
  //  $file : exact file name to load
  //  $http : optional http response code
  function load ($file, $http=null) {
    // (C1) ALL PAGES CAN ACCESS CORE & SESSION VARS
    global $_CORE;
    global $_SESS;
    $_PATH = $this->path;

    // (C2) LOAD SPECIFIED PAGE
    if (file_exists(PATH_PAGES . $file)) {
      if ($http) { http_response_code($http); }
      require PATH_PAGES . $file;
    } else {
      http_response_code(404);
      require PATH_PAGES . "PAGE-404.php";
    }
  }

  // (D) RESOLVE API REQUEST
  function api () {
    // (D1) ENFORCE HTTPS (RECOMMENDED)
    if (API_HTTPS && empty($_SERVER["HTTPS"])) {
      $this->core->respond(0, "Please use HTTPS", null, null, 426);
    }

    // (D2) GET CLIENT ORIGIN
    $_OGN = $_SERVER["HTTP_ORIGIN"] ?? $_SERVER["HTTP_REFERER"] ?? $_SERVER["REMOTE_ADDR"] ?? "" ;
    $_OGN_HOST = parse_url($_OGN, PHP_URL_HOST);

    // (D3) CORS SUPPORT - ONLY IF NOT LOCALHOST
    if (!in_array($_OGN, ["::1", "127.0.0.1", "localhost"])) {
      // (D3-1) FALSE - ONLY CALLS FROM HOST_NAME ALLOWED
      if (API_CORS===false && $_OGN_HOST!=HOST_NAME) { $access = false; }

      // (D3-2) STRING - ALLOW CALLS FROM API_CORS ONLY
      else if (is_string(API_CORS) && $_OGN_HOST!=API_CORS) { $access = false; }

      // (D3-3) ARRAY - SPECIFIED DOMAINS IN API_CORS ONLY
      else if (is_array(API_CORS) && !in_array($_OGN_HOST, API_CORS)) { $access = false; }

      // (D3-4) TRUE - ANYTHING GOES
      else { $access = true; $_OGN = "*"; }

      // (D3-5) ACCESS DENIED
      if ($access === false) {
        $this->core->respond(0, "Calls from $_OGN not allowed", null, null, 403);
      }

      // (D3-6) OUTPUT CORS HEADERS IF REQUIRED
      if ($_OGN_HOST != HOST_NAME) {
        header("Access-Control-Allow-Origin: $_OGN");
        header("Access-Control-Allow-Credentials: true");
      }
    }

    // (D4) PARSE URL PATH INTO AN ARRAY
    // http://site.com/api/module/request > $this->path = ["module", "request"]
    $this->path = explode("/", rtrim(substr($this->path, strlen(HOST_API)), "/"));

    // (D5) VALID API REQUEST?
    $valid = count($this->path)==2;
    if ($valid) {
      $_MOD = $this->path[0];
      $_REQ = $this->path[1];
      $valid = file_exists(PATH_LIB . "API-$_MOD.php");
    }

    // (D6) LOAD API HANDLER
    if ($valid) {
      // (D6-1) CLEAN UP
      unset($access); unset($this->path); unset($valid);

      // (D6-2) VARS THAT ARE USEFUL IN YOUR API
      // $_MOD : requested module. e.g. user
      // $_REQ : requested action. e.g. save
      // $_OGN : client origin. e.g. https://site.com/
      // $_OGN_HOST : host name. e.g. site.com
      // $_CORE : core boxx engine
      // $_SESS : session vars
      global $_CORE;
      global $_SESS;
      require PATH_LIB . "API-$_MOD.php";
    } else { $this->core->respond(0, "Invalid request", null, null, 400); }
  }

  // (E) REGENERATE HTACCESS FILE
  function htaccess () {
    $htaccess = PATH_BASE . ".htaccess";
    if (file_put_contents($htaccess, implode("\r\n", [
      "RewriteEngine On",
      "RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]",
      "RewriteBase " . HOST_BASE_PATH,
      "RewriteRule ^index\.php$ - [L]",
      "RewriteCond %{REQUEST_FILENAME} !-f",
      "RewriteCond %{REQUEST_FILENAME} !-d",
      "RewriteRule . " . HOST_BASE_PATH . "index.php [L]"
    ])) === false) { throw new Exception("Failed to create $htaccess"); }
  }
}