<?php
class CoreBoxx {
  // (A) PROPERTIES & CONSTRUCTOR
  public $error = ""; // error message, if any
  function __construct () {
    $GLOBALS["_SESS"] = []; // initialize global session variable
  }

  // (B) MODULES
  // (B1) LOAD MODULE
  //  $module : module to load
  function load ($module) { if (!$this->loaded($module)) {
    $file = PATH_LIB . "LIB-$module.php";
    if (file_exists($file)) {
      require $file;
      $this->$module = new $module($this);
    } else { throw new Exception("$module module not found!"); }
  }}

  // (B2) IS MODULE LOADED?
  //  $module : module to check
  function loaded ($module) {
    return isset($this->$module) && is_object($this->$module);
  }

  // (C) FUNCTION MAPPING
  // (C1) AUTO MAP $_POST OR $_GET TO MODULE FUNCTION
  //  $module : module to load
  //  $function : function to run
  //  $mode : POST or GET
  function autoCall ($module, $function, $mode="POST") {
    // (C1-1) LOAD MODULE
    $this->load($module);

    // (C1-2) MAP POST OR GET?
    if ($mode=="POST") { $target =& $_POST; }
    else { $target =& $_GET; }

    // (C1-3) GET FUNCTION PARAMETERS
    $reflect = new ReflectionMethod($module, $function);
    $params = $reflect->getParameters();

    // (C1-4) EVIL MAPPING
    $evil = "\$results = \$this->$module->$function(";
    if (count($params)==0) { $evil .= ");"; }
    else {
      foreach ($params as $p) {
        // POST OR GET HAS EXACT PARAMETER MATCH
        if (isset($target[$p->name])) {
          $evil .= "\$_". $mode ."[\"". $p->name ."\"],";
        }

        // USE DEFAULT VALUE
        else if ($p->isDefaultValueAvailable()) {
          $val = $p->getDefaultValue();
          $evil .= is_string($val) ? "\"$val\"," : (
            $val===null ? "null," : "$val,"
          );
        }

        // NULL IF ALL ELSE FAILS
        else { $evil .= "null,"; }
      }
      $evil = substr($evil, 0, -1) . ");";
    }

    // (C1-5) EVIL RESULTS
    eval($evil);
    return $results;
  }

  // (C2) AUTO MAP $_POST OR $_GET TO MODULE FUNCTION & API RESPOND
  //  $module : module to load
  //  $function : function to run
  //  $mode : POST or GET
  function autoAPI ($module, $function, $mode="POST") {
    $result = $this->autoCall($module, $function, $mode);
    $this->respond($result, null, null,
      $this->DB->lastID!==null ? $this->DB->lastID : null
    );
  }

  // (C3) SAME AS ABOVE, BUT FOR "GET ENTRIES" API FUNCTIONS
  //  $module : module to load
  //  $function : function to run
  //  $mode : POST or GET
  function autoGETAPI ($module, $function, $mode="POST") {
    $results = $this->autoCall($module, $function, $mode);
    $this->respond(
      $results!==false, null,
      isset($results["data"]) ? $results["data"] : $results,
      isset($results["page"]) ? $results["page"] : null
    );
  }

  // (D) SYSTEM
  // (D1) STANDARD JSON RESPONSE
  //  $status : 1 or 0, true or false
  //  $msg : system message
  //  $data : optional, data append
  //  $more : optional, supplementary data
  //  $http : optional, HTTP response code (401, 403, 500, etc...)
  //  $exit : stop process, default true
  function respond ($status, $msg=null, $data=null, $more=null, $http=null, $exit=true) {
    if ($http!==null) { http_response_code($http); }
    if ($msg === null) {
      if ($status==1) { $msg = "OK"; }
      else { $msg = $this->error; }
    }
    echo json_encode([
      "status" => $status,
      "message" => $msg,
      "data" => $data,
      "more" => $more
    ]);
    if ($exit) { exit(); }
  }

  // (D2) STANDARD ERROR HANDLER
  function ouch ($ex) {
    // (D2-1) OUTPUT JSON ENCODED MESSAGE IN API MODE
    if (defined("API_MODE")) {
      $this->respond(0,
      ERR_SHOW ? $ex->getMessage() : "OPPS! An error has occured.",
      ERR_SHOW ? ["code" => $ex->getCode(), "file" => $ex->getFile(), "line" => $ex->getLine() ] : null);
    }

    // (D2-2) SHOW HTML ERROR MESSAGE IN WEB MODE
    else { ?>
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
  }

  // (E) OTHER CONVENIENCE
  // (E1) GENERATE RANDOM STRING
  // $length : number of bytes
  function random ($length=8) {
    return bin2hex(random_bytes($length));
  }

  // (E2) PAGINATION CALCULATOR
  //  $entries : total number of entries
  //  $now : current page
  function paginator ($entries, $now=1) {
    // (E2-1) TOTAL NUMBER OF PAGES
    $page = [
      "entries" => (int) $entries,
      "total" => ceil($entries / PAGE_PER)
    ];

    // (E2-2) CURRENT PAGE
    $page["now"] = $now > $page["total"] ? $page["total"] : $now ;
    if ($page["now"]<=0) { $page["now"] = 1; }
    $page["now"] = (int) $page["now"];

    // (E2-3) LIMIT X,Y
    $page["x"] = ($page["now"] - 1) * PAGE_PER;
    $page["y"] = PAGE_PER;

    // (E2-4) DONE
    return $page;
  }

  // (F) REDIRECT
  function redirect ($page="", $url=HOST_BASE) {
    header("Location: $url$page");
    exit();
  }
}

// (G) ALL LIBRARIES SHOULD EXTEND THIS CORE CLASS
class Core {
  function __construct ($core) {
    $this->core =& $core; // Link to core
    $this->error =& $core->error; // Error message
    if ($core->loaded("DB")) { $this->DB =& $core->DB; } // Link to database module
  }
}
