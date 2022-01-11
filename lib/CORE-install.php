<?php
// (A) SETTINGS
// (A1) TURN ON ERROR REPORTING
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
ini_set("log_errors", 0);

// (A2) SYSTEM REQUIREMENTS
define("I_MIN_PHP", "7.4.0");
define("I_APACHE", apache_get_version() !== false);

// (A3) IMPORTANT FILES & FOLDERS
define("I_BASE", dirname(__DIR__) . DIRECTORY_SEPARATOR);
define("I_API", I_BASE . "api" . DIRECTORY_SEPARATOR);
define("I_ASSETS", I_BASE . "assets" . DIRECTORY_SEPARATOR);
define("I_LIB", I_BASE . "lib" . DIRECTORY_SEPARATOR);
define("I_ALL", [
  I_BASE, I_API, I_ASSETS, I_LIB, I_LIB . "CORE-config.php", I_LIB . "INSTALL-index.foo",
  I_LIB . "SQL-storage-boxx.sql"
]);

// (A4) DATABASE DEFAULTS
define("I_DB_HOST", "localhost");
define("I_DB_NAME", "coreboxx");
define("I_DB_USER", "root");
define("I_DB_PASS", "");

// (A5) URL
$uHOST = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$uIDX = strpos($uHOST, "index.php");
if ($uIDX!==false) { $uHOST = substr($uHOST, 0, $uIDX); }
$uHOST = rtrim($uHOST, "/") . "/";

define("I_HTTPS", isset($_SERVER["HTTPS"]));
define("I_HOST", $uHOST);
unset($uHOST); unset($uIDX);

// (A6) MODE SELECTOR
// (B) PRE-CHECKS
// (C) SHOW INSTALLATION HTML
// (D) ACTUAL INSTALLATION
$_IMODE = isset($_POST["install"]) ? "D" : "B";

// (B) PRE CHECKS
if ($_IMODE=="B") {
  // (B1) PHP VERSION
  if (version_compare(PHP_VERSION, I_MIN_PHP, "<")) {
    exit("At least PHP ".I_MIN_PHP." is required. You are using ". PHP_VERSION);
  }

  // (B2) MYSQL PDO
  if (!extension_loaded("pdo_mysql")) {
    exit("PDO MYSQL extension is not enabled.");
  }

  // (B3) APACHE MOD REWRITE
  if (I_APACHE && !in_array("mod_rewrite", apache_get_modules())) {
    exit("Please enable Apache MOD_REWRITE.");
  }

  // (B4) FILES & FOLDERS EXIST + READ WRITE PERMISSIONS
  foreach (I_ALL as $p) {
    if (!file_exists($p)) { exit("$p does not exist!"); }
    if (!is_readable($p)) { exit("Please give PHP read permission to $p"); }
    if (!is_writable($p)) { exit("Please give PHP write permission to $p"); }
  }

  // (B5) REMOVE OLD COPIES OF HTACCESS
  $htaccess = I_BASE . ".htaccess";
  if (file_exists($htaccess)) {
    if (!unlink($htaccess)) { exit("Failed to delete $htaccess - Please delete this file manually"); }
  }
  $htaccess = I_API . ".htaccess";
  if (file_exists($htaccess)) {
    if (!unlink($htaccess)) { exit("Failed to delete $htaccess - Please delete this file manually"); }
  }

  // (B6) ALL GREEN
  $_IMODE = "C";
}

// (C) SHOW INSTALL HTML
if ($_IMODE == "C") { ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Installation</title>
    <style>
    *{font-family: arial, sans-serif;box-sizing:border-box}
    #iHead{display:flex;align-items:center;margin-bottom:20px}#iHead h1{margin:0}#iHead img{margin-right:20px}
    form{max-width:500px;margin:20px auto}h2{margin:10px 0}
    .iSec{background:#f5f5f5;border:1px solid #dbdbdb;padding:20px;margin-bottom:20px}
    select,input,label{font-size:16px;display:block;width:100%}select,input{padding:10px}
    label{color:#88a8ff;font-weight:700;padding:10px 0}
    #gobtn{background:#1a57c5;border:0;color:#fff}
    #gobtn:disabled{background:#838383;color:#bbb}
    .danger{padding:20px;margin-bottom:30px;background:#5542f3;color:#fff;font-weight:700;font-size:20px;line-height:28px}
    .notes{font-size:17px;color:#585858;padding:10px 0}
    </style>
    <script>
    function install () {
      // DISABLE GO BUTTON
      var go = document.getElementById("gobtn");
      go.disabled = true;

      // ADMIN PASSWORD
      var pass = document.getElementsByName("apass")[0],
          cpass = document.getElementsByName("apassc")[0];
      if (pass.value != cpass.value) {
        go.disabled = false;
        alert("Admin passwords do not match!");
        return false;
      }

      // FORM DATA
      var data = new FormData(document.getElementById("iForm"));
      data.append("install", "1");

      // AJAX FETCH
      var url = (document.getElementsByName("https")[0].value=="0" ? "http" : "https")
              + "://"
              + document.getElementsByName("host")[0].value;
      fetch(url, { method:"POST", body:data })
      .then((res) => {
        if (res.status!=200) {
          alert("SERVER ERROR - ${res.status}");
          console.error(res);
        } else { return res.text(); }
      })
      .then((txt) => {
        if (txt=="OK") {
          alert("Installation complete, this page will now reload.");
          location.reload();
        } else { alert(txt); }
      })
      .catch((err) => {
        alert(`Fetch error - ${err.message}`);
        console.error(err);
      })
      .finally(() => { go.disabled = false; });
      return false;
    }

    // CREDITS https://stackoverflow.com/questions/1349404/generate-random-string-characters-in-javascript
    function rnd () {
      var result = "";
      var characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789~!-_=.";
      var charactersLength = characters.length;
      for ( var i = 0; i < 48; i++ ) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
      }
      document.getElementsByName("jwtkey")[0].value = result;
    }
    </script>
  </head>
  <body>
    <?php if (I_APACHE === false) { ?>
    <div class="danger">
      You are not running Apache Web Server.
      This will still work, but you need to manually enable URL rewrite and "translate" /.htaccess and /api/.htaccess on your own.
    </div>
    <?php } ?>

    <form id="iForm" onsubmit="return install()">
      <div id="iHead">
        <img src="assets/favicon.png"/>
        <h1>STORAGE BOXX INSTALLATION</h1>
      </div>

      <div class="iSec">
        <h2>HOST</h2>
        <label>HTTP or HTTPS</label>
        <select name="https">
          <option value="0">http://</option>
          <option value="1"<?=I_HTTPS?" selected":""?>>https://</option>
        </select>
        <label>Domain AND Path</label>
        <input type="text" name="host" required value="<?=I_HOST?>"/>
        <div class="notes">Change this only if wrong, include the path if not deployed in root. E.G. site.com/myproject/</div>
      </div>

      <div class="iSec">
        <h2>API ENDPOINT</h2>
        <label>Enforce HTTPS?</label>
        <select name="apihttps">
          <option value="0">No</option>
          <option value="1"<?=I_HTTPS?" selected":""?>>Yes</option>
        </select>
        <div class="notes">If enforced, API will only respond to HTTPS calls - Recommended to set "yes" for live servers.</div>
        <label>CORS</label>
        <select name="apicors">
          <option value="0">Disallow</option>
          <option value="1">Allow</option>
        </select>
        <div class="notes">Set "allow" if you intend to develop your own mobile app.</div>
      </div>

      <div class="iSec">
        <h2>DATABASE</h2>
        <label>Host</label>
        <input type="text" name="dbhost" required value="<?=I_DB_HOST?>"/>
        <label>Name</label>
        <input type="text" name="dbname" required value="<?=I_DB_NAME?>"/>
        <label>User</label>
        <input type="text" name="dbuser" required value="<?=I_DB_USER?>"/>
        <label>Password</label>
        <input type="password" name="dbpass" value="<?=I_DB_PASS?>"/>
      </div>

      <div class="iSec">
        <h2>JSON WEB TOKEN</h2>
        <label>Secret Key <span onclick="rnd()">[RANDOM]</span></label>
        <input type="text" name="jwtkey" required/>
        <label>Issuer</label>
        <input type="text" name="jwyiss" required value="<?=$_SERVER["HTTP_HOST"]?>"/>
        <div class="notes">Your company name or domain name.</div>
      </div>

      <div class="iSec">
        <h2>ADMIN USER</h2>
        <label>Name</label>
        <input type="text" name="aname" required value="Admin"/>
        <label>Email</label>
        <input type="text" name="aemail" required value="admin@site.com"/>
        <label>Password</label>
        <input type="password" name="apass" required/>
        <label>Confirm Password</label>
        <input type="password" name="apassc" required/>
      </div>

      <input id="gobtn" type="submit" value="Go!"/>
    </form>
  </body>
</html>
<?php }

// (D) INSTALLATION
if ($_IMODE=="D") {
  // (D1) TRY CONNECT TO DATABASE
  try {
    $pdo = new PDO(
      "mysql:host=".$_POST["dbhost"].";charset=utf8",
      $_POST["dbuser"], $_POST["dbpass"], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
  } catch (Exception $ex) { exit("Unable to connect to database - " . $ex->getMessage()); }

  // (D2) CREATE DATABASE
  try {
    $pdo->exec("CREATE DATABASE `".$_POST["dbname"]."`");
    $pdo->exec("USE `".$_POST["dbname"]."`");
  } catch (Exception $ex) { exit("Unable to create database - " . $ex->getMessage()); }

  // (D3) IMPORT SQL FILE
  try {
    $pdo->exec(file_get_contents(I_LIB . "SQL-storage-boxx.sql"));
  } catch (Exception $ex) { exit("Unable to create database - " . $ex->getMessage()); }

  // (D4) CREATE ADMIN USER
  try {
    $stmt = $pdo->prepare("REPLACE INTO `users` (`user_name`, `user_email`, `user_password`) VALUES (?,?,?)");
    $stmt->execute([$_POST["aname"], $_POST["aemail"], password_hash($_POST["apass"], PASSWORD_DEFAULT)]);
  } catch (Exception $ex) {
    exit("Error creating admin user - " . $ex->getMessage());
  }

  // (D5) SETTINGS TO UPDATE
  $hbase = $_POST["https"]=="1" ? "https://" : "http://" . $_POST["host"];
  $hbase = rtrim($hbase, "/") . "/";
  $replace = [
    "HOST_BASE" => $hbase,
    "DB_HOST" => $_POST["dbhost"],
    "DB_NAME" => $_POST["dbname"],
    "DB_USER" => $_POST["dbuser"],
    "DB_PASSWORD" => $_POST["dbpass"],
    "API_CORS" => ($_POST["apicors"]=="1" ? "true" : "false"),
    "API_HTTPS" => ($_POST["apihttps"]=="1" ? "true" : "false"),
    "JWT_SECRET" => $_POST["jwtkey"],
    "JWT_ISSUER" => $_POST["jwyiss"]
  ];
  unset($_POST); unset($hbase);

  // (D6) BACKUP LIB/CORE-CONFIG.PHP
  if (!copy(I_LIB . "CORE-config.php", I_LIB . "CORE-config.bak")) {
    exit("Failed to backup config file - " . I_LIB . "CORE-config.bak");
  }

  // (D7) UPDATE LIB/CORE-CONFIG.PHP
  $go = file(I_LIB . "CORE-config.php") or exit("Cannot read". I_LIB ."CORE-config.php");
  foreach ($go as $j=>$line) { foreach ($replace as $k=>$v) {
    if (strpos($line, "\"$k\"") !== false) {
      if ($k!="API_HTTPS" && $k!="API_CORS") { $v = "\"$v\""; }
      $go[$j] = "define(\"$k\", $v); // CHANGED BY INSTALLER\r\n";
      unset($replace[$k]);
      if (count($replace)==0) { break; }
    }
  }}
  try {
    file_put_contents(I_LIB . "CORE-config.php", implode("", $go));
  } catch (Exception $ex) {
    exit("Error writing to ". I_LIB ."CORE-config.php");
  }
  unset($go);

  // (D8) GENERATE HTACCESS
  require I_LIB . "CORE-go.php";
  $_CORE->load("Route");
  $_CORE->Route->init();

  // (D9) SWAP OUT INDEX
  if (!copy(I_LIB . "INSTALL-index.foo", I_BASE . "index.php")) {
    exit("Failed to create - " . I_BASE . "index.php");
  }

  // (D10) DONE!
  echo "OK";
}
