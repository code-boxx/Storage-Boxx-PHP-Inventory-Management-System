<?php
class CoreBoxx {
  // (A) PROPERTIES
  public $error = ""; // Error message, if any

  // (B) LOAD MODULE
  //  $module : module to load
  function load ($module) {
    if ($this->loaded($module)) { return true; }
    $file = PATH_LIB . "LIB-$module.php";
    if (file_exists($file)) {
      require $file;
      $this->$module = new $module($this);
      return true;
    } else {
      $this->error = "$file not found!";
      return false;
    }
  }

  // (C) IS MODULE LOADED?
  //  $module : module to check
  function loaded ($module) {
    return isset($this->$module) && is_object($this->$module);
  }

  // (D) AUTO MAP $_POST OR $_GET TO MODULE FUNCTION
  //  $module : module to load
  //  $function : function to run
  //  $mode : POST or GET
  function autoCall ($module, $function, $mode="POST") {
    // (D1) LOAD MODULE
    if (!$this->load($module)) { return false; }

    // (D2) GET FUNCTION PARAMETERS
    $reflect = new ReflectionMethod($module, $function);
    $params = $reflect->getParameters();

    // (D3) EVIL AUTO MAP-AND-RUN
    if ($mode=="POST") { $target =& $_POST; }
    else { $target =& $_GET; }
    $evil = "\$results = \$this->$module->$function(";
    if (count($params)==0) { $evil .= ");"; }
    else {
      foreach ($params as $p) {
        if (!isset($target[$p->name])) { $target[$p->name] = null; }
        $evil .= "\$_" . $mode . "['$p->name'],";
      }
      $evil = substr($evil, 0, -1) . ");";
    }
    eval($evil);
    return $results;
  }

  // (E) AUTO MAP $_POST OR $_GET TO MODULE FUNCTION & API RESPOND
  //  $module : module to load
  //  $function : function to run
  //  $mode : POST or GET
  function autoAPI ($module, $function, $mode="POST") {
    $this->respond($this->autoCall($module, $function, $mode));
  }

  // (F) SAME AS ABOVE, BUT FOR "GET ENTRIES" API FUNCTIONS
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

  // (G) GENERATE RANDOM STRING
  // CREDITS : https://stackoverflow.com/questions/4356289/php-random-string-generator
  // $length : number of characters to generate
  function random ($length=16) {
    $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $cLength = strlen($characters);
    $random = "";
    for ($i = 0; $i < $length; $i++) {
      $random .= $characters[rand(0, $cLength - 1)];
    }
    return $random;
  }

  // (H) STANDARD JSON RESPONSE
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

  // (I) PAGINATION CALCULATOR
  //  $entries : total number of entries
  //  $now : current page
  function paginator ($entries, $now=1) {
    // (I1) TOTAL NUMBER OF PAGES
    $page = [
      "entries" => $entries,
      "total" => ceil($entries / PAGE_PER)
    ];

    // (I2) CURRENT PAGE
    $page["now"] = $now > $page["total"] ? $page["total"] : $now ;
    if ($page["now"]<=0) { $page["now"] = 1; }

    // (I3) LIMIT X,Y
    $page["x"] = ($page["now"] - 1) * PAGE_PER;
    $page["y"] = PAGE_PER;

    // (I4) DONE
    return $page;
  }
}

// (J) ALL LIBRARIES SHOULD EXTEND THIS CORE CLASS
class Core {
  function __construct ($core) {
    $this->core =& $core; // Link to core
    $this->error =& $core->error; // Error message
    if ($core->loaded("DB")) { $this->DB =& $core->DB;  } // Link to database module
  }
}
