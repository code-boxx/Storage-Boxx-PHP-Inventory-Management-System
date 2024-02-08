<?php
class Route extends Core {
  public $path;    // current url path
  public $pathlen; // current url path length
  public $mod;     // current requested api module
  public $act;     // current requested api action
  public $origin;  // client origin, e.g. http://site.com
  public $orihost; // client origin host, e.g. site.com

  // (A) RUN URL ROUTING ENGINE
  function run () : void {
    // (A1) GET URL PATH SEGMENT
    $this->path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

    // (A2) SPECIAL CASE
    // e.g. http://site.com//, http://site.com//XYZ
    if ($this->path == "") {
      $this->load("PAGE-404.php", 404);
      exit();
    }

    // (A3) CLEAN CURRENT URL PATH
    // http://site.com/ > $this->path = "/"
    // http://site.com/hello/world/ > $this->path = "hello/world/"
    $this->path = preg_replace("~/{2,}~", "/", $this->path);
    if (substr($this->path, 0, strlen(HOST_BASE_PATH)) == HOST_BASE_PATH) {
      $this->path = substr($this->path, strlen(HOST_BASE_PATH));
    }
    $this->path = rtrim($this->path, "/\\") . "/";
    $this->pathlen = strlen($this->path);

    // (A2) MISSING ASSET FILE
    if (substr($this->path, 0, 6) == "assets") {
      $this->load("PAGE-404.php", 404);
    }

    // (A3) THIS IS AN API REQUEST
    else if (
      strlen($this->path) >= strlen(HOST_API) &&
      substr($this->path, 0, strlen(HOST_API)) == HOST_API
    ) {
      $this->Core->mode = "A";
      $this->api();
    }

    // (A4) A "NORMAL" HTTP REQUEST
    else {
      $this->Core->mode = "W";
      $this->resolve();
    }
  }

  // (B) RESOLVE CURRENT URL ROUTE
  function resolve () : void {
    // (B1) LOAD ROUTE OVERRIDE
    require PATH_LIB . "HOOK-Routes.php";

    // (B2) MANUAL ROUTE OVERRIDE
    if (isset($override)) {
      $this->path = $override($this->path);
      $this->pathlen = strlen($this->path);
    }

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
      : "PAGE-" . str_replace("/", "-", rtrim($this->path, "/\\")) . ".php"
    );
  }

  // (C) LOAD HTML PAGE
  //  $file : exact file name to load
  //  $http : optional http response code
  function load ($file, $http=null) : void {
    // (C1) ALL PAGES CAN ACCESS CORE
    global $_CORE;

    // (C2) LOAD SPECIFIED PAGE
    if (file_exists(PATH_PAGES . $file)) {
      if ($http) { http_response_code($http); }
      require PATH_PAGES . $file;
    } else {
      http_response_code(404);
      if (!isset($_POST["ajax"])) { require PATH_PAGES . "PAGE-404.php"; }
    }
  }

  // (D) RESOLVE API REQUEST
  function api () : void {
    // (D1) ENFORCE HTTPS (RECOMMENDED)
    if (API_HTTPS && empty($_SERVER["HTTPS"])) {
      $this->Core->respond(0, "Please use HTTPS", null, null, 426);
    }

    // (D2) PARSE URL PATH INTO AN ARRAY - CHECK VALID API REQUEST
    // http://site.com/api/module/action
    $request = explode("/", rtrim(substr($this->path, strlen(HOST_API)), "/\\"));
    $valid = count($request)==2;
    if ($valid) {
      $this->mod = $request[0];
      $this->act = $request[1];
      $valid = file_exists(PATH_LIB . "API-{$this->mod}.php");
      unset($request);
    }
    if (!$valid) { $this->Core->respond(0, "Invalid request", null, null, 400); }

    // (D3) CORS SUPPORT - ONLY IF NOT LOCALHOST
    $this->origin = $_SERVER["HTTP_ORIGIN"] ?? $_SERVER["HTTP_REFERER"] ?? $_SERVER["REMOTE_ADDR"] ?? "" ;
    $this->orihost = parse_url($this->origin, PHP_URL_HOST);
    if ($this->orihost=="") { $this->orihost = $this->origin; }
    if (!in_array($this->orihost, ["::1", "127.0.0.1", "localhost"])) {
      // (D3-1) USE CORE-CONFIG.PHP CORS RULE
      // false - only calls from host_name allowed
      if (API_CORS===false && $this->orihost!=HOST_NAME) { $access = false; }
      // string - allow calls from api_cors only
      else if (is_string(API_CORS) && $this->orihost!=API_CORS) { $access = false; }
      // array - specified domains in api_cors only
      else if (is_array(API_CORS) && !in_array($this->orihost, API_CORS)) { $access = false; }
      // true - anything goes
      else { $access = true; }

      // (D3-2) MANUAL OVERRIDE
      require PATH_LIB . "HOOK-API-CORS.php";

      // (D3-3) ACCESS DENIED
      if (!isset($access)) { $access = false; }
      if ($access === false) {
        $this->Core->respond(0, "Calls from $this->origin not allowed", null, null, 403);
      }

      // (D3-4) OUTPUT CORS HEADERS IF REQUIRED
      if ($this->orihost != HOST_NAME) {
        header("Access-Control-Allow-Origin: $this->origin");
        header("Access-Control-Allow-Credentials: true");
      }
    }

    // (D4) LOAD API HANDLER
    global $_CORE;
    require PATH_LIB . "API-{$this->mod}.php";
  }

  // (E) REGENERATE HTACCESS + MANIFEST FILES
  function init ($hbase=HOST_BASE_PATH) : void {
    // (E1) HTACCESS
    $file = PATH_BASE . ".htaccess";
    if (file_put_contents($file, implode("\r\n", [
      "RewriteEngine On",
      "RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]",
      "RewriteBase " . $hbase,
      "RewriteRule ^index\.php$ - [L]",
      "RewriteCond %{REQUEST_FILENAME} !-f",
      "RewriteCond %{REQUEST_FILENAME} !-d",
      "RewriteRule . " . $hbase . "index.php [L]"
    ])) === false) { throw new Exception("Failed to create $file"); }

    // (E2) WEB MANIFEST
    $file = PATH_BASE . "CB-manifest.json";
    $replace = ["start_url", "scope"];
    $cfg = file($file) or exit("Cannot read $file");
    foreach ($cfg as $j=>$line) { foreach ($replace as $r) { if (strpos($line, "\"$r\"") !== false) {
      $cfg[$j] = "  \"$r\": \"".$hbase."\",\r\n";
    }}}
    $replace = ["short_name", "name"];
    foreach ($cfg as $j=>$line) { foreach ($replace as $r) { if (strpos($line, "\"$r\"") !== false) {
      $cfg[$j] = "  \"$r\": \"".SITE_NAME."\",\r\n";
    }}}
    if (file_put_contents($file, implode("", $cfg)) === false) {
      throw new Exception("Failed to write $file");
    }
  }
}