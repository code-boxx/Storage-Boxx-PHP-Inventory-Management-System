<?php
// (A) SOME SETTINGS & STUFF
// (A1) TURN ON ERROR REPORTING
error_reporting(E_ALL & ~E_NOTICE);
ini_set("display_errors", 1);
ini_set("log_errors", 0);

// (A2) PATHS AND STUFFS
$minver = "7.4.0";
$pBASE = __DIR__ . DIRECTORY_SEPARATOR;
$pAPI = $pBASE . "api" . DIRECTORY_SEPARATOR;
$pLIB = $pBASE . "lib" . DIRECTORY_SEPARATOR;
$paths = [$pBASE, $pAPI, $pLIB];

// (A3) URL
$uHTTPS = isset($_SERVER["HTTPS"]);
$uHOST = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$uFULL = $uHTTPS ? "https://" : "http://" . $uHOST;

// (A4) MODE SELECTOR
// (B) PRE-CHECKS
// (C) SHOW INSTALLATION HTML
// (D) ACTUAL INSTALLATION
$mode = isset($_POST["install"]) ? "D" : "B";

// (B) PRE CHECKS
if ($mode=="B") {
  // (B2) PHP VERSION
  if (version_compare(PHP_VERSION, $minver, "<")) {
    exit("At least PHP $minver required. You are using ". PHP_VERSION);
  }

  // (B3) MYSQL PDO
  if (!extension_loaded("pdo_mysql")) {
    exit("PDO MYSQL extension is not enabled.");
  }

  // (B4) FOLDERS EXIST + READ WRITE PERMISSIONS
  foreach ($paths as $p) {
    if (!file_exists($p)) { exit("$p does not exist!"); }
    if (!is_writable($p)) { exit("Please give PHP write permission to $p"); }
  }

  // (B5) ALL GREEN
  $mode = "C";
}

// (C) SHOW INSTALL HTML
if ($mode == "C") { ?>
<!DOCTYPE html>
<html>
  <head>
    <title>Storage Boxx Installation</title>
    <style>
    *{font-family: arial, sans-serif}
    label,select,input{font-size:18px} h1{font-size:24px}
    h1,label,select,input{box-sizing:border-box;display:block;width:100%}
    form{max-width:500px;background:#f5f5f5;padding:20px;margin:20px}
    input,select{padding:10px}
    label{margin:10px 0;color:#5a63d7}
    input[type=submit]{margin-top:20px;background:#4e89f5;border:0;color:#fff}
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
      fetch("<?=$uFULL?>", { method:"POST", body:data })
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
        }  else { alert(txt); }
      })
      .catch((err) => {
        alert("Fetch error - See console");
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
    <form id="iForm" onsubmit="return install()">
      <h1>HOST</h1>
      <label>Enforce HTTPS?</label>
      <select name="https">
        <option value="0">http://</option>
        <option value="1"<?=$uHTTPS?" selected":""?>>https://</option>
      </select>
      <label>Host Name</label>
      <input type="text" name="host" required value="<?=$uHOST?>"/>

      <h1>DATABASE</h1>
      <label>Host</label>
      <input type="text" name="dbhost" required value="localhost"/>
      <label>Name</label>
      <input type="text" name="dbname" required value="storageboxx"/>
      <label>User</label>
      <input type="text" name="dbuser" required value="root"/>
      <label>Password</label>
      <input type="password" name="dbpass"/>

      <h1>JSON WEB TOKEN</h1>
      <label>Secret Key <span onclick="rnd()">[RANDOM]</span></label>
      <input type="text" name="jwtkey" required/>
      <label>Issuer (Your company name or domain name)</label>
      <input type="text" name="jwyiss" required value="<?=$_SERVER["HTTP_HOST"]?>"/>

      <h1>ADMIN USER</h1>
      <label>Name</label>
      <input type="text" name="aname" required value="Admin"/>
      <label>Email</label>
      <input type="text" name="aemail" required value="admin@site.com"/>
      <label>Password</label>
      <input type="password" name="apass" required/>
      <label>Confirm Password</label>
      <input type="password" name="apassc" required/>

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
      "mysql:host=".$_POST["dbhost"].";dbname=".$_POST["dbname"].";charset=utf8",
      $_POST["dbuser"], $_POST["dbpass"], [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
  } catch (Exception $ex) { exit("Unable to connect to database - " . $ex->getMessage()); }

  // (D2) CREATE ADMIN USER
  try {
    $stmt = $pdo->prepare("REPLACE INTO `users` (`user_name`, `user_email`, `user_password`) VALUES (?,?,?)");
    $stmt->execute([$_POST["aname"], $_POST["aemail"], password_hash($_POST["apass"], PASSWORD_DEFAULT)]);
  } catch (Exception $ex) {
    exit("Error creating admin user - " . $ex->getMessage());
  }

  // (D3) SETTINGS TO UPDATE
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

  // (D4) CREATE LIB/GO.PHP
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

  // (D5) GENERATE HTACCESS
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

  // (D6) GENERATE API HTACCESS
  $htaccess = $pAPI . ".htaccess";
  if (file_put_contents($htaccess, implode("\r\n", [
    "RewriteEngine On",
    "RewriteBase ${hbasepath}api/",
    "RewriteRule ^index\.php$ - [L]",
    "RewriteCond %{REQUEST_FILENAME} !-f",
    "RewriteCond %{REQUEST_FILENAME} !-d",
    "RewriteRule . ${hbasepath}api/index.php [L]"
  ])) === false) { exit("Failed to create $htaccess"); }

  // (D7) REMOVE THIS INSTALLER SCRIPT
  rename($pBASE."index.php", $pLIB."install.foo");
  rename($pBASE."index.foo", $pBASE."index.php");

  // (D8) DONE!
  echo "OK";
}
