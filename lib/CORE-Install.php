<?php
// (PHASE A) BASE SETTINGS
// (PHASE B) BASE REQUIREMENTS CHECK
// (PHASE C) UPDATE CHECK
// (PHASE D) INSTALLATION HTML PAGE
// (PHASE E) GENERATE HTACCESS FILE
// (PHASE F) VERIFY HTACCESS FILE + INSTALL
// (PHASE G) CLEAN UP

// (PHASE A) BASE SETTINGS
// (A1) SHOW ERRORS
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
ini_set("log_errors", 0);

// (A2) PROJECT NAME + CORE BOXX MODULES
// @TODO - SET AS NECESSARY
define("I_NAME", "STORAGE BOXX"); // project name
define("I_USER", true); // user module
define("I_PUSH", true); // push notifications module

// (A3) FILE PATHS
// @TODO - SET AS NECESSARY
define("I_BASE", dirname(__DIR__) . DIRECTORY_SEPARATOR);
define("I_ASSETS", I_BASE . "assets" . DIRECTORY_SEPARATOR);
define("I_LIB", I_BASE . "lib" . DIRECTORY_SEPARATOR);
define("I_PAGES", I_BASE . "pages" . DIRECTORY_SEPARATOR);
// define("I_UPLOADS", I_ASSETS . "uploads" . DIRECTORY_SEPARATOR);

// (A4) SQL FILES - FROM OLDEST TO NEWEST VERSIONS
// @TODO - SET AS NECESSARY
define("I_SQL", ["SQL-Storage-Boxx-0.sql", "SQL-Storage-Boxx-1.sql"]);

// (A5) HELPER FUNCTION - IMPORT SQL FILES
function import ($pdo, $from=0) {
  for ($i=$from; $i<count(I_SQL); $i++) {
    try { $pdo->exec(file_get_contents(I_LIB . I_SQL[$i])); }
    catch (Exception $ex) { exit("Unable to import SQL - " . $ex->getMessage()); }
  }
  $stmt = $pdo->prepare("UPDATE `settings` SET `setting_value`=? WHERE `setting_name`=?");
  $stmt->execute([count(I_SQL), "APP_VER"]);
}

// (A6) NEXT PHASE
$_PHASE = isset($_POST["phase"]) ? $_POST["phase"] : "B";

// (PHASE B) BASE REQUIREMENTS CHECK
if ($_PHASE=="B") {
  // (B1) CHECK SETTINGS
  // (B1-1) FILES & FOLDERS TO CHECK FOR READ/WRITE PERMISSIONS
  // @TODO - SET AS NECESSARY
  define("I_ALL", [
    I_BASE, I_ASSETS, I_LIB, I_PAGES, // I_UPLOADS,
    I_LIB . "CORE-Config.php", I_BASE . "index.php", I_BASE . "CB-manifest.json"
  ]);

  // (B1-2) APACHE + PHP VERSION + EXTENSIONS
  // @TODO - SET AS NECESSARY
  define("I_MIN_PHP", "8.0.0");
  define("I_PDO", extension_loaded("pdo_mysql"));
  define("I_OPENSSL", extension_loaded("openssl"));
  define("I_APACHE", strpos(strtolower($_SERVER["SERVER_SOFTWARE"]), "apache")!==false);
  define("I_REWRITE", I_APACHE && function_exists("apache_get_version")
    ? in_array("mod_rewrite", apache_get_modules()) : false
  );

  // (B2) SYSTEM CHECKS
  // (B2-1) PHP VERSION
  if (version_compare(PHP_VERSION, I_MIN_PHP, "<")) {
    exit("At least PHP ".I_MIN_PHP." is required. You are using ".PHP_VERSION);
  }

  // (B2-2) MYSQL PDO
  if (I_PDO===false) { exit("PDO MYSQL extension is not enabled."); }

  // (B2-3) OPENSSL
  if (I_PUSH && I_OPENSSL===false) { exit("OPENSSL extension is not enabled."); }

  // (B2-4) FILES & FOLDERS - READ/WRITE PERMISSIONS
  foreach (I_ALL as $p) {
    if (!file_exists($p)) { exit("$p does not exist!"); }
    if (!is_readable($p)) { exit("Please give PHP read permission to $p"); }
    if (!is_writable($p)) { exit("Please give PHP write permission to $p"); }
  }
  foreach (I_SQL as $p) {
    if (!file_exists(I_LIB . $p)) { exit("$p does not exist!"); }
    if (!is_readable(I_LIB . $p)) { exit("Please give PHP read permission to $p"); }
  }

  // (B3) ALL GREEN
  $_PHASE = file_exists(I_BASE . ".htaccess") ? "C" : "D";
}

// (PHASE C) UPDATE CHECK
if ($_PHASE == "C") { try {
  // (C1) IF CONNECT TO DATABASE OK - IT'S A POSSIBLE UPDATE
  require I_LIB . "CORE-Config.php";
  require I_LIB . "LIB-Core.php";
  $_CORE->load("DB");

  // (C2) CHECK VERSION + AUTO PATCH
  $ver = $_CORE->DB->fetchCol("SELECT `setting_value` FROM `options` WHERE `setting_name`=?", ["APP_VER"]);
  $newest = count(I_SQL);
  if ($ver < $newest) { import($_CORE->DB->pdo, $ver); }

  // (C3) DONE!
  $_RELOAD = true;
  $_PHASE = "G";
} catch (Exception $ex) { $_PHASE = "D"; }}

// (PHASE D) INSTALLATION HTML PAGE
if ($_PHASE == "D") {
  // (D1) DATABASE DEFAULTS
  // @TODO - SET AS NECESSARY
  define("I_DB_HOST", "localhost");
  define("I_DB_NAME", "storageboxx");
  define("I_DB_USER", "root");
  define("I_DB_PASS", "");

  // (D2) URL YOGA
  $uHOST = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
  $uIDX = strpos($uHOST, "index.php");
  if ($uIDX!==false) { $uHOST = substr($uHOST, 0, $uIDX); }
  $uHOST = rtrim($uHOST, "/") . "/";
  define("I_HTTPS", isset($_SERVER["HTTPS"]));
  define("I_HOST", $uHOST);
  unset($uHOST); unset($uIDX);

  // (D3) PUSH NOTIFICATION VAPID KEYS
  if (I_PUSH && I_OPENSSL) {
    require I_LIB . "webpush/autoload.php";
    define("I_VAPID", Minishlink\WebPush\VAPID::createVapidKeys());
  }

// (DD) HTML ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Installation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.5">
    <meta name="robots" content="noindex">

    <!-- (DD1) CSS COSMETICS -->
    <style>
    *{font-family:arial,sans-serif;box-sizing:border-box}body{padding:20px;max-width:600px;margin:0 auto;background:#f4f4f4}
    .danger{padding:15px;margin-bottom:15px;background:#bb2323;color:#fff;font-weight:700;line-height:1.5em}.hide{display:none}
    code{font-family:consolas,monospace;border:1px solid #ecef23;background:#fcff00;padding:0 5px}.danger code {border:0;background:#7c0404}
    #iHead{display:flex;align-items:center;margin-bottom:50px}#iHead h1{margin:0}#iHead img{margin-right:10px}
    h2{font-size:24px;margin:0 0 10px 0}.iSec{background:#fff;border:1px solid #e9e9e9;padding:20px;margin-bottom:20px}
    select,input,label{display:block;width:100%}select,input{font-size:16px;padding:10px}
    label{font-size:14px;font-weight:700;color:#cb2d2d;text-transform:uppercase;padding:10px 0}label:first-child{padding-top:0}
    select,input[type=text],input[type=email],input[type=password]{border:1px solid #e3e3e3}
    .notes{font-size:16px;line-height:24px;color:#767676;padding:10px 0}
    #gobtn{background:#1a57c5;border:0;color:#fff;font-weight:700;cursor:pointer}#gobtn:disabled{background:#838383;color:#bbb}
    </style>

    <script>
    var install = {
      // (DD2) HELPER - AJAX FETCH
      ajax : (url, phase, after) => {
        // (DD2-1) FORM DATA
        let data = new FormData(document.getElementById("iForm"));
        data.append("phase", phase);

        // (DD2-2) AJAX FETCH
        fetch(url, { method:"POST", body:data })
        .then(res => {
          if (res.status==200) { return res.text(); }
          else {
            console.error(res);
            let err = "SERVER ERROR " + res.status;
            if (res.status==404) { err += " - Is the host URL correct? Is 'AllowOverride All' set in Apache?`"; }
            alert(err);
            install.toggle(true);
          }
        })
        .then(txt => {
          if (txt=="OK") { after(); }
          else if (txt!=undefined) {
            alert(txt);
            install.toggle(true);
          }
        })
        .catch(err => {
          alert(`Fetch error - ${err.message}`);
          install.toggle(true);
          console.error(err);
        });
      },

      // (DD3) LOCK/UNLOCK INSTALL FORM
      toggle : enable => {
        if (enable) {
          document.getElementById("gobtn").disabled = false;
          document.getElementById("iForm").onsubmit = install.go;
        } else {
          document.getElementById("gobtn").disabled = true;
          document.getElementById("iForm").onsubmit = false;
        }
      },

      // (DD4) RANDOM JWT KEY GENERATOR
      // CREDITS https://stackoverflow.com/questions/1349404/generate-random-string-characters-in-javascript
      rnd : () => {
        var result = "";
        var characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789~!-_=.";
        var charactersLength = characters.length;
        for ( var i = 0; i < 48; i++ ) {
          result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        document.getElementsByName("jwtkey")[0].value = result;
      },

      // (DD5) TOGGLE CORS
      cors : allowed => {
        let more = document.getElementById("corsmore");
        if (allowed==1) { more.classList.remove("hide"); }
        else { more.classList.add("hide"); }
      },

      // (DD6) INSTALL GO
      go : () => {
        // (DD6-1) LOCK INSTALL FORM
        install.toggle(false);

        <?php if (I_USER) { ?>
        // (DD6-2) ADMIN PASSWORD
        var pass = document.getElementsByName("apass")[0],
            cpass = document.getElementsByName("apassc")[0];
        if (pass.value != cpass.value) {
          alert("Admin passwords do not match!");
          install.toggle(true);
          return false;
        }
  
        // (DD6-3) PASSWORD STRENGTH CHECK - AT LEAST 8 CHARACTERS ALPHANUMERIC
        if (!/^(?=.*[0-9])(?=.*[A-Z]).{8,20}$/i.test(pass.value)) {
          alert("Password must be at least 8 characters alphanumeric");
          install.toggle(true);
          return false;
        }
        <?php } ?>

        // (DD6-4) URL PATH
        let url = (document.getElementsByName("https")[0].value=="0" ? "http" : "https")
                + "://" + document.getElementsByName("host")[0].value;

        // (DD6-5) GENERATE HTACCESS + VERIFY HTACCESS + INSTALL
        install.ajax(url, "E", () => install.ajax(url + "COREVERIFY", "F", () => {
          alert("Installation complete, this page will now reload.");
          location.href = url;
        }));
        return false;
      }
    };

    // (DD7) ENABLE INSTALL FORM ON WINDOW LOAD
    window.onload = () => install.toggle(true);
    </script>
  </head>
  <body>
    <?php if (I_APACHE === false || I_REWRITE === false) { ?>
    <!-- (DD8) WARNINGS -->
    <div class="danger">
      The installer cannot verify if you are running Apache Web Server, or if <code>MOD_REWRITE</code> is enabled.
      You can still try to proceed if you want.
      If you are not running Apache, you need to create your own "translated" <code>.htaccess</code> file.
      See <code>lib/LIB-Route.php &gt; function htaccess()</code>.
    </div>
    <?php } ?>

    <!-- (DD9) HEADER -->
    <div id="iHead">
      <img src="assets/favicon.png">
      <h1><?=I_NAME?> INSTALLATION</h1>
    </div>

    <!-- (DD10) INSTALLATION FORM -->
    <form id="iForm" onsubmit="return false">
      <!-- (DD10-1) HOST URL -->
      <h2>HOST URL</h2>
      <div class="iSec">
        <label>HTTP or HTTPS</label>
        <select name="https">
          <option value="0">http://</option>
          <option value="1"<?=I_HTTPS?" selected":""?>>https://</option>
        </select>
        <label>Domain &amp; Path</label>
        <input type="text" name="host" required value="<?=I_HOST?>">
        <div class="notes">* Change this only if wrong, include the path if not deployed in root. E.G. <code>site.com/myproject/</code></div>
      </div>

      <!-- (DD10-2) API ENDPOINT -->
      <h2>API ENDPOINT</h2>
      <div class="iSec">
        <label>Enforce HTTPS?</label>
        <select name="apihttps">
          <option value="0">No</option>
          <option value="1"<?=I_HTTPS?" selected":""?>>Yes</option>
        </select>
        <div class="notes">* If enforced, API will only respond to HTTPS calls - Recommended to set "yes" for live servers.</div>
        <label>CORS</label>
        <select name="apicors" onchange="install.cors(this.value)">
          <option value="0">Disallow</option>
          <option value="1">Allow</option>
        </select>
        <div class="notes">* Allow CORS only if you intend to develop mobile apps, or let third parties access your system.</div>
        <div id="corsmore" class="hide">
          <label>Allowed CORS Domains</label>
          <input type="text" name="corsallow">
          <div class="notes">* Leave this blank to allow all websites and apps to access your system (not recommended).</div>
          <div class="notes">
            * To restrict which domains can access your system - Enter the domain name (<code>site-a.com</code>), or multiple domains separated by commas (<code>site-a.com, site-b.com</code>).
          </div>
        </div>
      </div>

      <!-- (DD10-3) DATABASE -->
      <h2>DATABASE</h2>
      <div class="iSec">
        <label>Host</label>
        <input type="text" name="dbhost" required value="<?=I_DB_HOST?>">
        <label>Name</label>
        <input type="text" name="dbname" required value="<?=I_DB_NAME?>">
        <label>User</label>
        <input type="text" name="dbuser" required value="<?=I_DB_USER?>">
        <label>Password</label>
        <input type="password" name="dbpass" value="<?=I_DB_PASS?>">
      </div>

      <!-- (DD10-4) EMAIL SEND FROM -->
      <h2>EMAIL</h2>
      <div class="iSec">
        <label>Sent From</label>
        <input type="email" name="mailfrom" value="sys@site.com" required>
      </div>

      <!-- (DD10-5) JWT & ADMIN USER -->
      <?php if (I_USER) { ?>
      <h2>JSON WEB TOKEN</h2>
      <div class="iSec">
        <label>Secret Key <span onclick="install.rnd()">[RANDOM]</span></label>
        <input type="text" name="jwtkey" required>
        <label>Issuer</label>
        <input type="text" name="jwyiss" required value="<?=$_SERVER["HTTP_HOST"]?>">
        <div class="notes">* Your company name or domain name.</div>
      </div>

      <h2>ADMIN USER</h2>
      <div class="iSec">
        <label>Name</label>
        <input type="text" name="aname" required value="Admin">
        <label>Email</label>
        <input type="text" name="aemail" required value="admin@site.com">
        <label>Password</label>
        <input type="password" name="apass" required>
        <div class="notes">* At least 8 characters alphanumeric.</div>
        <label>Confirm Password</label>
        <input type="password" name="apassc" required>
      </div>
      <?php } ?>

      <!-- (DD10-6) PUSH NOTIFICATION -->
      <?php if (I_PUSH) { ?>
      <h2>WEB PUSH VAPID KEYS</h2>
      <div class="iSec">
        <label>Private Key</label>
        <input type="text" name="pushprivate" required value="<?=I_VAPID["privateKey"]?>">
        <label>Public Key</label>
        <input type="text" name="pushpublic" required value="<?=I_VAPID["publicKey"]?>">
        <div class="notes">
          * You can regenerate these with:<br>
          <code>require "lib/webpush/autoload.php";</code><br>
          <code>$keys = Minishlink\WebPush\VAPID::createVapidKeys();</code>
        </div>
      </div>
      <?php } ?>

      <!-- (DD10-7) GO! -->
      <input id="gobtn" type="submit" value="Go!" disabled>
    </form>
  </body>
</html>
<?php }

// (PHASE E) GENERATE HTACCESS FILE
if ($_PHASE == "E") {
  $hbase = ($_POST["https"]=="1" ? "https://" : "http://") . $_POST["host"];
  $hbase = parse_url(rtrim($hbase, "/\\") . "/", PHP_URL_PATH);
  $htaccess = I_BASE . ".htaccess";
  if (file_put_contents($htaccess, implode("\r\n", [
    "RewriteEngine On",
    "RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]",
    "RewriteBase " . $hbase,
    "RewriteRule ^index\.php$ - [L]",
    "RewriteCond %{REQUEST_FILENAME} !-f",
    "RewriteCond %{REQUEST_FILENAME} !-d",
    "RewriteRule . " . $hbase . "index.php [L]"
  ])) === false) { throw new Exception("Failed to create $htaccess"); }
  echo "OK";
}

// (PHASE F) VERIFY HTACCESS FILE + INSTALL
if ($_PHASE == "F") {
  // (F1) ALLOWED API CORS DOMAINS
  if ($_POST["apicors"]==1 && $_POST["corsallow"]!="") {
    if (strpos($_POST["corsallow"], ",")!==false) {
      $_POST["corsallow"] = explode(",", $_POST["corsallow"]);
      foreach ($_POST["corsallow"] as $i=>$j) {
        $j = trim($j);
        if (!filter_var($j, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
          exit("Invalid domain name - $j");
        }
        $_POST["corsallow"][$i] = "\"" . $j . "\"";
      }
      $_POST["corsallow"] = "[" . implode(", ", $_POST["corsallow"]) . "]";
    } else {
      if (!filter_var($_POST["corsallow"], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
        exit("Invalid domain name - " . $_POST["corsallow"]);
      }
      $_POST["corsallow"] = "\"" . $_POST["corsallow"] . "\"";
    }
  }

  // (F2) TRY CONNECT TO DATABASE
  try {
    $pdo = new PDO(
      "mysql:host=".$_POST["dbhost"].";charset=utf8",
      $_POST["dbuser"], $_POST["dbpass"], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
  } catch (Exception $ex) { exit("Unable to connect to database - " . $ex->getMessage()); }

  // (F3) CREATE DATABASE + IMPORT SQL FILE(S)
  try {
    $pdo->exec("CREATE DATABASE `".$_POST["dbname"]."`");
    $pdo->exec("USE `".$_POST["dbname"]."`");
  } catch (Exception $ex) { exit("Unable to create database - " . $ex->getMessage()); }
  import($pdo);

  // (F4) EMAIL FROM
  try {
    $stmt = $pdo->prepare("UPDATE `settings` SET `setting_value`=? WHERE `setting_name`='EMAIL_FROM'");
    $stmt->execute([$_POST["mailfrom"]]);
  } catch (Exception $ex) { exit("Error setting email from - " . $ex->getMessage()); }

  // (F5) CREATE ADMIN USER
  if (I_USER) { try {
    $stmt = $pdo->prepare("REPLACE INTO `users` (`user_name`, `user_email`, `user_password`) VALUES (?,?,?)");
    $stmt->execute([$_POST["aname"], $_POST["aemail"], password_hash($_POST["apass"], PASSWORD_DEFAULT)]);
  } catch (Exception $ex) { exit("Error creating admin user - " . $ex->getMessage()); }}

  // (F6) CORE_CONFIG.PHP SETTINGS TO UPDATE
  $hbase = ($_POST["https"]=="1" ? "https://" : "http://") . $_POST["host"];
  $hbase = rtrim($hbase, "/\\") . "/";
  $replace = [
    "HOST_BASE" => $hbase,
    "DB_HOST" => $_POST["dbhost"],
    "DB_NAME" => $_POST["dbname"],
    "DB_USER" => $_POST["dbuser"],
    "DB_PASSWORD" => $_POST["dbpass"],
    "API_CORS" => ( $_POST["apicors"]=="1" 
      ? ($_POST["corsallow"]=="" ? "true" : $_POST["corsallow"])
      : "false" ),
    "API_HTTPS" => ($_POST["apihttps"]=="1" ? "true" : "false")
  ];
  if (I_USER) {
    $replace["JWT_SECRET"] = $_POST["jwtkey"];
    $replace["JWT_ISSUER"] = $_POST["jwyiss"];
  }
  if (I_PUSH) {
    $replace["PUSH_PRIVATE"] = $_POST["pushprivate"];
    $replace["PUSH_PUBLIC"] = $_POST["pushpublic"];
  }
  unset($_POST);

  // (F6) BACKUP LIB/CORE-CONFIG.PHP
  if (!copy(I_LIB . "CORE-Config.php", I_LIB . "CORE-Config.old")) {
    exit("Failed to backup config file - " . I_LIB . "CORE-Config.old");
  }

  // (F7) UPDATE LIB/CORE-CONFIG.PHP
  $cfg = file(I_LIB . "CORE-Config.php") or exit("Cannot read". I_LIB ."CORE-Config.php");
  foreach ($cfg as $j=>$line) { foreach ($replace as $k=>$v) { if (strpos($line, "\"$k\"") !== false) {
    if ($k!="API_HTTPS" && $k!="API_CORS") { $v = "\"$v\""; }
    $cfg[$j] = "define(\"$k\", $v); // CHANGED BY INSTALLER\r\n";
    unset($replace[$k]);
    if (count($replace)==0) { break; }
  }}}
  try { file_put_contents(I_LIB . "CORE-Config.php", implode("", $cfg)); }
  catch (Exception $ex) { exit("Error writing to ". I_LIB ."CORE-Config.php"); }

  // (F8) UPDATE WEB MANIFEST
  $replace = ["start_url", "scope"];
  $hbase = parse_url(rtrim($hbase, "/\\") . "/", PHP_URL_PATH);
  $cfg = file(I_BASE . "CB-manifest.json") or exit("Cannot read". I_BASE . "CB-manifest.json");
  foreach ($cfg as $j=>$line) { foreach ($replace as $r) { if (strpos($line, "\"$r\"") !== false) {
    $cfg[$j] = "  \"$r\": \"$hbase\",\r\n";
  }}}
  try { file_put_contents(I_BASE . "CB-manifest.json", implode("", $cfg)); }
  catch (Exception $ex) { exit("Error writing to ". I_BASE . "CB-manifest.json"); }
  unset($hbase); unset($cfg); unset($replace);

  // (F9) ALMOST DONE...
  $_PHASE = "G";
}

// (PHASE G) CLEAN UP
if ($_PHASE == "G") {
  // (G1) SWAP OUT INDEX
  file_put_contents(I_BASE . "index.php", implode("\r\n", [
    '<?php',
    'require __DIR__ . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "CORE-Go.php";',
    '$_CORE->load("Route");',
    '$_CORE->Route->run();'
  ]));

  // (G2) INSTALL COMPLETE!
  if (isset($_RELOAD)) { $_CORE->redirect(); }
  else { echo "OK"; }
}