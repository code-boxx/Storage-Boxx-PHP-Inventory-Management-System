<?php
// (A) PROPERTIES
// (B-D, M) MODULES HANDLING
// (E-F) FUNCTION MAPPING
// (G-H, N) SYSTEM
// (I-L) CONVENIENCE
class CoreBoxx {
  // (A) PROPERTIES
  public $modules = []; // loaded modules
  public $error = "";   // error message, if any
  public $page = null;  // pagination data, if any
  public $mode = "C";   // operation mode - "a"pi "w"eb "c"li

  // (B) IS MODULE LOADED?
  //  $module : module to check
  function loaded ($module) { return isset($this->modules[$module]); }

  // (C) LOAD MODULE
  //  $module : module to load
  function load ($module) : void {
    if ($this->loaded($module)) { return; }
    $file = PATH_LIB . "LIB-$module.php";
    if (file_exists($file)) {
      require $file;
      $this->modules[$module] = new $module($this);
    } else { throw new Exception("$module module not found!"); }
  }

  // (D) "MAGIC LINK" TO MODULE
  function __get ($name) {
    if (isset($this->modules[$name])) { return $this->modules[$name]; }
  }

  // (E) AUTO MAP $_POST OR $_GET TO MODULE FUNCTION
  //  $module : module to load
  //  $function : function to run
  //  $mode : POST or GET
  function autoCall ($module, $function, $mode="POST") {
    // (E1) LOAD MODULE
    $this->load($module);

    // (E2) MAP POST OR GET?
    if ($mode=="POST") { $target =& $_POST; } else { $target =& $_GET; }

    // (E3) GET FUNCTION PARAMETERS
    $reflect = new ReflectionMethod($module, $function);
    $params = $reflect->getParameters();

    // (E4) EVIL MAPPING
    $evil = "\$results = \$this->$module->$function(";
    if (count($params)==0) { $evil .= ");"; }
    else {
      foreach ($params as $p) {
        // (E4-1) POST OR GET HAS EXACT PARAMETER MATCH
        if (isset($target[$p->name])) { $evil .= "\$_". $mode ."[\"". $p->name ."\"],"; }

        // (E4-2) USE DEFAULT VALUE
        else if ($p->isDefaultValueAvailable()) {
          $val = $p->getDefaultValue();
          if (is_string($val)) { $evil .= "\"$val\","; }
          else if (is_bool($val)) { $evil .= $val ? "true," : "false," ; }
          else { $evil .= ($val===null ? "null," : "$val,"); }
        }

        // (E4-3) NULL IF ALL ELSE FAILS
        else { $evil .= "null,"; }
      }
      $evil = substr($evil, 0, -1) . ");";
    }

    // (E5) EVIL RESULTS
    eval($evil);
    return $results;
  }

  // (F) AUTO RESOLVE API REQUEST
  //  $actions : ["action" => ["module", "function", "level"]]
  //  $mode : POST or GET
  function autoAPI ($actions, $mode="POST") : void {
    if (isset($actions[$this->Route->act])) {
      // (F1) ACCESS CHECK
      if (isset($actions[$this->Route->act][2])) {
        $this->ucheck($actions[$this->Route->act][2]);
      }

      // (F2) RUN FUNCTION
      $result = $this->autoCall($actions[$this->Route->act][0], $actions[$this->Route->act][1], $mode);
      $this->respond(
        is_bool($result) ? $result : true, null,
        is_bool($result) ? null : $result,
        $this->DB->lastID!==null ? $this->DB->lastID : null
      );
    }
  }

  // (G) STANDARD JSON RESPONSE
  //  $status : 1 or 0, true or false
  //  $msg : system message
  //  $data : optional, data append
  //  $more : optional, supplementary data
  //  $http : optional, HTTP response code (401, 403, 500, etc...)
  //  $exit : stop process, default true
  function respond ($status, $msg=null, $data=null, $more=null, $http=null, $exit=true) : void {
    if ($http!==null) { http_response_code($http); }
    if ($msg === null) {
      if ($status==1) { $msg = "OK"; }
      else { $msg = $this->error; }
    }
    echo json_encode([
      "status" => $status, "message" => $msg,
      "data" => $data, "more" => $more, "page" => $this->page
    ]);
    if ($exit) { exit(); }
  }

  // (H) STANDARD ERROR HANDLER
  function ouch ($ex) : void {
    // (H1) SHOW HTML ERROR MESSAGE IN WEB MODE
    if ($this->mode=="W") { ?>
    <div style="box-sizing:border-box;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:9999;background:#fff;color:#000;padding:30px;font-family:arial">
      <h1 style="font-size:50px;padding:0;margin:0">(╯°□°)╯︵ ┻━┻</h1>
      <p style="font-size:30px;color:#ff4545">AN ERROR HAS OCCURED.</p>
      <?php if (ERR_SHOW) { ?>
      <table>
        <tr><td style="font-weight:700">Message</td><td><?=$ex->getMessage()?></td></tr>
        <tr><td style="font-weight:700">Code</td><td><?=$ex->getCode()?></td></tr>
        <tr><td style="font-weight:700">File</td><td><?=$ex->getFile()?></td></tr>
        <tr><td style="font-weight:700">Line</td><td><?=$ex->getLine()?></td></tr>
        <tr><td style="font-weight:700">Trace</td><td><?=$ex->getTraceAsString()?></td></tr>
      </table>
      <?php } ?>
    </div>
    <?php }

    // (H2) OUTPUT JSON ENCODED MESSAGE OTHERWISE
    else {
      $this->respond(0,
      ERR_SHOW ? $ex->getMessage() : "OPPS! An error has occured.",
      ERR_SHOW ? [
        "code" => $ex->getCode(), "file" => $ex->getFile(),
        "line" => $ex->getLine(), "trace" => $ex->getTraceAsString()
      ] : null, null, 500);
    }
  }

  // (I) GENERATE RANDOM STRING
  // $length : number of characters to generate
  function random ($length=8) {
    return substr(preg_replace("/[^A-Za-z0-9]/", "", base64_encode(random_bytes($length * 2))), 0, $length);
  }

  // (J) PAGINATION CALCULATOR
  //  $entries : total number of entries
  //  $now : current page
  function paginator ($entries, $now=1) : void {
    // (J1) TOTAL NUMBER OF PAGES
    $this->page = [
      "entries" => (int) $entries,
      "total" => ceil($entries / PAGE_PER)
    ];

    // (J2) CURRENT PAGE
    $this->page["now"] = $now > $this->page["total"] ? $this->page["total"] : $now ;
    if ($this->page["now"]<=0) { $this->page["now"] = 1; }
    $this->page["now"] = (int) $this->page["now"];

    // (J3) LIMIT X,Y
    $this->page["x"] = ($this->page["now"] - 1) * PAGE_PER;
    $this->page["y"] = PAGE_PER;
    $this->page["lim"] = " LIMIT {$this->page["x"]}, {$this->page["y"]}";
  }

  // (K) REDIRECT
  function redirect ($page="", $url=HOST_BASE) : void {
    header("Location: $url$page");
    exit();
  }

  // (L) USER ACCESS LEVEL CHECK
  // $level : required user level
  //  true : must be logged in
  //  array : must be any one of these levels
  //  string : must be this level
  // $redirect : redirect to this page, web mode only
  function ucheck ($lvl=true, $redirect="login/") : void {
    // (L1) ACCESS CHECK
    if ($lvl===true) { $access = isset($_SESSION["user"]); }
    else if (is_array($lvl)) { $access = isset($_SESSION["user"]) && in_array($_SESSION["user"]["user_level"], $lvl); }
    else {
      $access = (isset($_SESSION["user"]) && $_SESSION["user"]["user_level"]==$lvl) || $this->Route->path==$redirect;
    }

    // (L2) STOP IF NO GO
    if ($access==false) {
      if ($this->mode=="A") { $this->respond(0, "No access permission", null, null, 403); }
      else {
        if (isset($_POST["ajax"])) { exit("E"); }
        $this->redirect($redirect);
      }
    }
  }
}

// (M) ALL MODULES SHOULD EXTEND THIS CORE CLASS
class Core {
  // (M1) LINK MODULE TO CORE
  public $Core;
  public $error;
  function __construct ($core) {
    $this->Core =& $core;
    $this->error =& $core->error;
  }

  // (M2) MAKE MODULES LINKING EASIER
  function __get ($name) {
    if (isset($this->Core->modules[$name])) { return $this->Core->modules[$name]; }
  }
}

// (N) CORE OBJECT + GLOBAL ERROR HANDLING
$_CORE = new CoreBoxx();
function _CORERR ($ex) { global $_CORE; $_CORE->ouch($ex); }
set_exception_handler("_CORERR");