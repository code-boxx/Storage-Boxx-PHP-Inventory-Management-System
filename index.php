<?php
// (A) SOME SETTINGS & STUFF
// (A1) TURN ON ERROR REPORTING
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
ini_set("log_errors", 0);

// (A2) SYSTEM
$minver = "7.4.0";
$isapache = apache_get_version() !== false;

// (A3) PATHS & IMPORTANT FILES
$pBASE = __DIR__ . DIRECTORY_SEPARATOR;
$pAPI = $pBASE . "api" . DIRECTORY_SEPARATOR;
$pLIB = $pBASE . "lib" . DIRECTORY_SEPARATOR;
$pALL = [$pBASE, $pAPI, $pLIB, $pBASE."index.foo", $pLIB."GO.foo", $pLIB."storage-boxx.sql"];

// (A4) URL
$uHTTPS = isset($_SERVER["HTTPS"]);
$uHOST = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$uIDX = strpos($uHOST, "index.php");
if ($uIDX!==false) { $uHOST = substr($uHOST, 0, $uIDX); }
$uHOST = rtrim($uHOST, "/") . "/";
$uFULL = $uHTTPS ? "https://" : "http://" . $uHOST;

// (A5) MODE SELECTOR
// (B) PRE-CHECKS
// (C) SHOW INSTALLATION HTML
// (D) ACTUAL INSTALLATION
$mode = isset($_POST["install"]) ? "D" : "B";

// (B) PRE CHECKS
if ($mode=="B") {
  // (B1) PHP VERSION
  if (version_compare(PHP_VERSION, $minver, "<")) {
    exit("At least PHP $minver is required. You are using ". PHP_VERSION);
  }

  // (B2) MYSQL PDO
  if (!extension_loaded("pdo_mysql")) {
    exit("PDO MYSQL extension is not enabled.");
  }

  // (B3) APACHE MOD REWRITE
  if ($isapache && !in_array("mod_rewrite", apache_get_modules())) {
    exit("Please enable Apache MOD_REWRITE.");
  }

  // (B4) FILES & FOLDERS EXIST + READ WRITE PERMISSIONS
  foreach ($pALL as $p) {
    if (!file_exists($p)) { exit("$p does not exist!"); }
    if (!is_readable($p)) { exit("Please give PHP read permission to $p"); }
    if (!is_writable($p)) { exit("Please give PHP write permission to $p"); }
  }

  // (B5) REMOVE OLD COPIES OF HTACCESS
  $htaccess = $pBASE . ".htaccess";
  if (file_exists($htaccess)) {
    if (!unlink($htaccess)) { exit("Failed to delete $htaccess - Please delete this file manually"); }
  }
  $htaccess = $pAPI . ".htaccess";
  if (file_exists($htaccess)) {
    if (!unlink($htaccess)) { exit("Failed to delete $htaccess - Please delete this file manually"); }
  }

  // (B6) ALL GREEN
  $mode = "C";
}
unset($uIDX); unset($pALL);

// (C) SHOW INSTALL HTML
if ($mode == "C") { ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Storage Boxx Installation</title>
    <style>
    *{font-family: arial, sans-serif}
    label,select,input{font-size:18px} h1{font-size:24px;margin:0 0 20px 0}
    h1,label,select,input{box-sizing:border-box;display:block;width:100%}
    form{max-width:500px;margin:20px auto 20px auto}
    .iSec{background:#f5f5f5;border:1px solid #dbdbdb;padding:20px;margin-bottom:20px}
    input,select{padding:10px}
    label{margin:10px 0;color:#5a63d7}
    input[type=submit]{background:#4e89f5;border:0;color:#fff}
    .danger{padding:20px;margin-bottom:30px;background:#5542f3;color:#fff;font-weight:700;font-size:20px;line-height:28px}
    </style>
    <script>
    function install () {
      // ADMIN PASSWORD
      var pass = document.getElementsByName("apass")[0],
          cpass = document.getElementsByName("apassc")[0];
      if (pass.value != cpass.value) {
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
      });
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
    <?php if ($isapache === false) { ?>
    <div class="danger">
      You are not running Apache Web Server.
      This will still work, but you need to manually enable URL rewrite and "translate" /.htaccess and /api/.htaccess on your own.
    </div>
    <?php } ?>

    <form id="iForm" onsubmit="return install()">
      <div class="iSec">
        <h1>HOST</h1>
        <label>HTTP or HTTPS</label>
        <select name="https">
          <option value="0">http://</option>
          <option value="1"<?=$uHTTPS?" selected":""?>>https://</option>
        </select>
        <label>Host (Change this only if wrong, include the path if not deployed in root. E.G. site.com/storage-boxx/)</label>
        <input type="text" name="host" required value="<?=$uHOST?>"/>
      </div>

      <div class="iSec">
        <h1>DATABASE</h1>
        <label>Host</label>
        <input type="text" name="dbhost" required value="localhost"/>
        <label>Name</label>
        <input type="text" name="dbname" required value="storageboxx"/>
        <label>User</label>
        <input type="text" name="dbuser" required value="root"/>
        <label>Password</label>
        <input type="password" name="dbpass"/>
      </div>

      <div class="iSec">
        <h1>JSON WEB TOKEN</h1>
        <label>Secret Key <span onclick="rnd()">[RANDOM]</span></label>
        <input type="text" name="jwtkey" required/>
        <label>Issuer (Your company name or domain name)</label>
        <input type="text" name="jwyiss" required value="<?=$_SERVER["HTTP_HOST"]?>"/>
      </div>

      <div class="iSec">
        <h1>ADMIN USER</h1>
        <label>Name</label>
        <input type="text" name="aname" required value="Admin"/>
        <label>Email</label>
        <input type="text" name="aemail" required value="admin@site.com"/>
        <label>Password</label>
        <input type="password" name="apass" required/>
        <label>Confirm Password</label>
        <input type="password" name="apassc" required/>
      </div>

      <input type="submit" value="Go!"/>
    </form>
  </body>
</html>
<?php }

// (D) INSTALLATION
if ($mode=="D") {
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
    $pdo->exec(file_get_contents($pLIB."storage-boxx.sql"));
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
    "API_HTTPS" => ($_POST["https"]=="1" ? "true" : "false"),
    "JWT_SECRET" => $_POST["jwtkey"],
    "JWT_ISSUER" => $_POST["jwyiss"]
  ];
  unset($_POST["aname"]); unset($_POST["aemail"]);
  unset($_POST["apass"]); unset($_POST["apassc"]);
  unset($_POST["install"]);

  // (D6) CREATE LIB/GO.PHP
  $go = file($pLIB . "GO.foo") or exit("Cannot read $pLIB" . "GO.foo");
  foreach ($go as $j=>$line) { foreach ($replace as $k=>$v) {
    if (strpos($line, "\"$k\"") !== false) {
      if ($k!="API_HTTPS") { $v = "\"$v\""; }
      $go[$j] = "define(\"$k\", $v); // CHANGED BY INSTALLER\r\n";
      unset($replace[$k]);
      if (count($replace)==0) { break; }
    }
  }}
  try {
    file_put_contents($pLIB . "GO.php", implode("", $go));
  } catch (Exception $ex) {
    exit("Error writing to ${pLIB}GO.php");
  }
  unset($go);

  // (D7) GENERATE HTACCESS
  $hbasepath = parse_url($hbase, PHP_URL_PATH);
  $htaccess = $pBASE . ".htaccess";
  if (file_put_contents($htaccess, implode("\r\n", [
    "RewriteEngine On",
    "RewriteBase $hbasepath",
    "RewriteRule ^index\.php$ - [L]",
    "RewriteCond %{REQUEST_FILENAME} !-f",
    "RewriteCond %{REQUEST_FILENAME} !-d",
    "RewriteRule . ".$hbasepath."index.php [L]"
  ])) === false) { exit("Failed to create $htaccess"); }

  // (D8) GENERATE API HTACCESS
  $htaccess = $pAPI . ".htaccess";
  if (file_put_contents($htaccess, implode("\r\n", [
    "RewriteEngine On",
    "RewriteBase ${hbasepath}api/",
    "RewriteRule ^index\.php$ - [L]",
    "RewriteCond %{REQUEST_FILENAME} !-f",
    "RewriteCond %{REQUEST_FILENAME} !-d",
    "RewriteRule . ${hbasepath}api/index.php [L]"
  ])) === false) { exit("Failed to create $htaccess"); }

  // (D9) REMOVE THIS INSTALLER SCRIPT
  rename($pBASE."index.php", $pLIB."install.foo");
  rename($pBASE."index.foo", $pBASE."index.php");

  // (D10) DONE!
  echo "OK";
}
