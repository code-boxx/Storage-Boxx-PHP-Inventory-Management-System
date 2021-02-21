<?php
class Core {
  // (A) PROPERTIES
  public $pdo = null;
  public $stmt = null;
  public $error = "";
  public $lastID = null;

  // (B) __CONSTRUCT () : SYSTEM INIT
  function __construct () {
    // (B1) CREATE .HTACCESS FILE IF NOT EXIST
    if (!file_exists(PATH_ROOT . ".htaccess")) {
      if (file_put_contents(PATH_ROOT . ".htaccess", implode("\r\n", [
        "RewriteEngine On",
        "RewriteBase " . URL_PATH_BASE,
        "RewriteCond %{REQUEST_FILENAME} !-f",
        "RewriteCond %{REQUEST_FILENAME} !-d",
        "RewriteRule . " . URL_PATH_BASE . "index.php [L]"
      ])) === false) { exit("Failed to create .htaccess"); }
      header("Location: " . $_SERVER['REQUEST_URI']);
      exit();
    }

    // (B2) CONNECT TO DATABASE
    try {
      $this->pdo = new PDO(
        "mysql:host=". DB_HOST .";charset=". DB_CHARSET .";dbname=". DB_NAME,
        DB_USER, DB_PASSWORD, [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
          PDO::ATTR_EMULATE_PREPARES => false
        ]
      );
    } catch (Exception $ex) { exit ($ex->getMessage()); }
  }

  // (C) __DESTRUCT () : CLOSE CONNECTION WHEN DONE
  function __destruct () {
    if ($this->stmt !== null) { $this->stmt = null; }
    if ($this->pdo !== null) { $this->pdo = null; }
  }

  // (D) LOAD () : LOAD SPECIFIED MODULE
  //  $module : module to load
  function load ($module) {
    // (D1) ALREADY LOADED
    if (isset($this->$module)) { return true; }

    // (D2) EXTEND THE BASE OBJECT
    $file = PATH_CORE . "LIB-$module.php";
    if (file_exists($file)) {
      require $file;
      $this->$module = new $module();
      // EVIL POINTER - ALLOW OBJECTS TO ACCESS EACH OTHER
      $this->$module->core =& $this;
      $this->$module->error =& $this->error;
      $this->$module->pdo =& $this->pdo;
      $this->$module->stmt =& $this->stmt;
      return true;
    } else {
      $this->error = "$file not found!";
      return false;
    }
  }

  // (E) RESPOND () : OUTPUT SYSTEM STANDARD JSON RESPONSE
  //  $status : 1 or 0, true or false
  //  $msg : system message
  //  $data : optional, data append
  //  $more : optional, supplementary data
  //  $exit : stop process, default true
  function respond ($status, $msg=null, $data=null, $more=null, $exit=true) {
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

  // (F) AUTOAPI () : AUTO MAP $_POST TO MODULE FUNCTION
  //  $module : module to load
  //  $function : function to run
  function autoAPI ($module, $function) {
    // (F1) LOAD MODULE
    if (!$this->load($module)) { $this->respond(0); }

    // (F2) EVIL AUTO MAP-AND-RUN
    $evil = "\$this->respond(\$this->$module->$function(";
    $reflect = new ReflectionMethod($module, $function);
    $params = $reflect->getParameters();
    if (count($params)==0) { $evil .= "));"; }
    else {
      foreach ($params as $p) {
        if (!isset($_POST[$p->name])) { $_POST[$p->name] = null; }
        $evil .= "\$_POST['$p->name'],";
      }
      $evil = substr($evil, 0, -1) . "));";
    }
    eval($evil);
  }

  // (G) RANDOM () : RANDOM STRING
  // CREDITS : https://stackoverflow.com/questions/4356289/php-random-string-generator
  // $length : number of characters to generate
  function random ($length=16) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $cLength = strlen($characters);
    $random = '';
    for ($i = 0; $i < $length; $i++) {
      $random .= $characters[rand(0, $cLength - 1)];
    }
    return $random;
  }

  // (H) START () : AUTO-COMMIT OFF
  function start () {
    $this->pdo->beginTransaction();
  }

  // (I) END () : COMMIT OR ROLL BACK?
  function end ($commit=1) {
    if ($commit) { $this->pdo->commit(); }
    else { $this->pdo->rollBack(); }
  }

  // (J) EXEC () : RUN SQL QUERY
  //  $sql : SQL query
  //  $data : array of parameters
  function exec ($sql, $data=null) {
    try {
      $this->stmt = $this->pdo->prepare($sql);
      $this->stmt->execute($data);
      $this->lastID = $this->pdo->lastInsertId();
      return true;
    } catch (Exception $ex) {
      $this->error = $ex->getMessage();
      return false;
    }
  }

  // (K) FETCH () : FETCH SINGLE ROW
  //  $sql : SQL query
  //  $data : array of parameters
  function fetch ($sql, $data=null) {
    if (!$this->exec($sql, $data)) { return false; }
    return $this->stmt->fetch();
  }
  
  // (L) FETCHALL () : FETCH MULTIPLE ROWS
  //  $sql : SQL query
  //  $data : array of parameters
  //  $arrange : (string) arrange by [$ARRANGE] => RESULTS
  //             (array) arrange by [$ARRANGE[0] => $ARRANGE[1]]
  //             (none) default - whatever is set in PDO
  function fetchAll ($sql, $data=null, $arrange=null) {
    // (L1) RUN SQL QUERY
    if (!$this->exec($sql, $data)) { return false; }

    // (L2) FETCH ALL AS-IT-IS
    if ($arrange===null) { return $this->stmt->fetchAll(); }

    // (L3) ARRANGE BY $DATA[$ARRANGE] => RESULTS
    else if (is_string($arrange)) {
      $data = [];
      while ($row = $this->stmt->fetch()) { $data[$row[$arrange]] = $row; }
      return $data;
    }

    // (L4) ARRANGE BY $DATA[$ARRANGE[0]] => $ARRANGE[1]
    else {
      $data = [];
      while ($row = $this->stmt->fetch()) { $data[$row[$arrange[0]]] = $row[$arrange[1]]; }
      return $data;
    }
  }
}