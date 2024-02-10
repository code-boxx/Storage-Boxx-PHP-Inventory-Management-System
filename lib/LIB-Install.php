<?php
// (PHASE A) BASE SETTINGS
// @TODO - ADD FILES, SQL, PHP VERSION, REQUIRED MODULES AS YOUR PROJECT NEEDS

// (A1) FILES & FOLDERS THAT REQUIRE READ/WRITE PERMISSION
define("I_ALL", [
  PATH_BASE, PATH_ASSETS, PATH_LIB, PATH_PAGES, // PATH_UPLOADS,
  PATH_LIB . "CORE-Config.php",
  PATH_BASE . "index.php",
  PATH_BASE . "CB-manifest.json"
]);

// (A2) SQL FILES - FROM OLDEST TO NEWEST VERSIONS
define("I_SQL", ["SQL-Storage-Boxx-0.sql"]);

// (A3) APACHE + PHP + EXTENSIONS
define("I_APACHE", strpos(strtolower($_SERVER["SERVER_SOFTWARE"]), "apache")!==false);
define("I_MIN_PHP", "8.0.0");
define("I_REWRITE", I_APACHE && function_exists("apache_get_version")
  ? in_array("mod_rewrite", apache_get_modules()) : false
);
define("I_PDO", extension_loaded("pdo_mysql"));
define("I_OPENSSL", extension_loaded("openssl"));

// (A4) CORE BOXX MODULES & SETTINGS
define("I_USER", defined("USR_LVL")); // user module
define("I_PUSH", defined("PUSH_PUBLIC")); // push notifications module
define("I_CO", true); // include company name, address, tel, email

class Install extends Core {
  // (PHASE B) BASE REQUIREMENTS & SYSTEM CHECK
  function B () {
    // (B1) PHP VERSION
    if (version_compare(PHP_VERSION, I_MIN_PHP, "<")) {
      exit("At least PHP ".I_MIN_PHP." is required. You are using ".PHP_VERSION);
    }

    // (B2) MOD REWRITE
    if (I_APACHE && !I_REWRITE) { exit("Please enable Apache MOD_REWRITE"); }

    // (B3) MYSQL PDO
    if (I_PDO===false) { exit("PDO MYSQL extension is not enabled."); }

    // (B4) OPENSSL
    if (I_PUSH && I_OPENSSL===false) { exit("OPENSSL extension is not enabled."); }

    // (B5) FILES & FOLDERS - READ/WRITE PERMISSIONS
    foreach (I_ALL as $p) {
      if (!file_exists($p)) { exit("$p does not exist!"); }
      if (!is_readable($p)) { exit("Please give PHP read permission to $p"); }
      if (!is_writable($p)) { exit("Please give PHP write permission to $p"); }
    }
    foreach (I_SQL as $p) {
      if (!file_exists(PATH_LIB . $p)) { exit("$p does not exist!"); }
      if (!is_readable(PATH_LIB . $p)) { exit("Please give PHP read permission to $p"); }
    }

    // (B6) ALL GREEN
    return file_exists(PATH_BASE . ".htaccess") ? "C" : "D";
  }

  // (PHASE C) UPDATE CHECK
  function C () {
    // (C1) TRY TO CONNECT TO DATABASE
    try { $this->Core->load("DB"); }
    catch (Exception $ex) { return "D"; }

    // (C2) IF CONNECT OK - VERSION CHECK
    $ver = $this->DB->fetchCol(
      "SELECT `setting_value` FROM `settings` WHERE `setting_name`=?",
      ["APP_VER"]
    );
    $all = count(I_SQL);

    // (C3) AUTO UPDATE
    if ($ver < $all) { for ($i=$ver; $i < $all; $i++) {
      $this->DB->query(file_get_contents(PATH_LIB . I_SQL[$i]));
    }}
    $this->DB->update(
      "settings", ["setting_value"], "`setting_name`=?", [$all, "APP_VER"]
    );

    // (C4) DONE
    define("I_RELOAD", true);
    return "G";
  }

  // (PHASE D) INSTALLATION HTML PAGE
  function D () {
    require PATH_LIB . "CORE-Install-HTML.php";
    exit();
  }

  // (PHASE E1) GENERATE HTACCESS FILE
  function E1 () {
    $hbase = ($_POST["https"]=="1" ? "https://" : "http://") . $_POST["host"];
    $hbase = parse_url(rtrim($hbase, "/\\") . "/", PHP_URL_PATH);
    $this->Core->load("Route");
    $this->Route->init($hbase);
    exit("OK");
  }

  // (PHASE E2) VERIFY HTACCESS
  function E2 () {
    $check = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $len = strlen($check);
    if ($len < 15) { exit("INVALID HOST URL"); }
    $check = substr($check, $len-15, $len);
    exit($check=="installer/test/" ? "OK" : "INVALID HOST URL");
  }

  // (PHASE F) INSTALL
  function F () {
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
        "mysql:host=".$_POST["dbhost"].";charset=utf8mb4",
        $_POST["dbuser"], $_POST["dbpass"], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
      ]);
    } catch (Exception $ex) {
      exit("Unable to connect to database - " . $ex->getMessage());
    }

    // (F3) CREATE DATABASE
    try {
      $pdo->exec("CREATE DATABASE IF NOT EXISTS `".$_POST["dbname"]."`");
      $pdo->exec("USE `".$_POST["dbname"]."`");
    } catch (Exception $ex) {
      exit("Unable to create database - " . $ex->getMessage());
    }

    // (F4) IMPORT SQL FILES
    for ($i=0; $i<count(I_SQL); $i++) {
      try { $pdo->exec(file_get_contents(PATH_LIB . I_SQL[$i])); }
      catch (Exception $ex) { exit("Unable to import SQL - " . $ex->getMessage()); }
    }
    $stmt = $pdo->prepare("UPDATE `settings` SET `setting_value`=? WHERE `setting_name`=?");
    $stmt->execute([count(I_SQL), "APP_VER"]);

    // (F5) EMAIL FROM
    try {
      $stmt = $pdo->prepare("UPDATE `settings` SET `setting_value`=? WHERE `setting_name`='EMAIL_FROM'");
      $stmt->execute([$_POST["mailfrom"]]);
    } catch (Exception $ex) {
      exit("Error setting email from - " . $ex->getMessage());
    }

    // (F6) COMPANY INFO
    if (I_CO) { try {
      $stmt = $pdo->prepare("REPLACE INTO `settings` (`setting_name`, `setting_description`, `setting_value`, `setting_group`) VALUES (?,?,?,?), (?,?,?,?), (?,?,?,?), (?,?,?,?)");
      $stmt->execute([
        "CO_NAME", "Company Name", $_POST["coname"], 1,
        "CO_EMAIL", "Company Email", $_POST["coemail"], 1,
        "CO_TEL", "Company Telephone", $_POST["cotel"], 1,
        "CO_ADDRESS", "Company Address", $_POST["coaddr"], 1
      ]);
    } catch (Exception $ex) {
      exit("Error updating company info - " . $ex->getMessage());
    }}

    // (F7) CREATE ADMIN USER
    if (I_USER) { try {
      $stmt = $pdo->prepare("REPLACE INTO `users` (`user_name`, `user_email`, `user_level`, `user_password`) VALUES (?,?,?,?)");
      $stmt->execute([$_POST["aname"], $_POST["aemail"], "A", password_hash($_POST["apass"], PASSWORD_DEFAULT)]);
    } catch (Exception $ex) {
      exit("Error creating admin user - " . $ex->getMessage());
    }}
    $stmt = null; $pdo = null;

    // (F8) TIMEZONE
    date_default_timezone_set($_POST["timezone"]);
    $now = ["o" => (new DateTime())->getOffset()];
    $now["h"] = floor(abs($now["o"]) / 3600);
    $now["m"] = floor((abs($now["o"]) - ($now["h"] * 3600)) / 60);

    // (F9) CORE_CONFIG.PHP SETTINGS TO UPDATE
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
      "API_HTTPS" => ($_POST["apihttps"]=="1" ? "true" : "false"),
      "JWT_SECRET" => $_POST["jwtkey"],
      "JWT_ISSUER" => $_POST["jwyiss"],
      "SYS_TZ" => $_POST["timezone"],
      "SYS_TZ_OFFSET" => sprintf("%s%02d:%02d", $now["o"]<0 ? "-" : "+", $now["h"], $now["m"])
    ];
    if (I_PUSH) {
      $replace["PUSH_PRIVATE"] = $_POST["pushprivate"];
      $replace["PUSH_PUBLIC"] = $_POST["pushpublic"];
    }
    unset($_POST); unset($now); unset($hbase);

    // (F10) BACKUP LIB/CORE-CONFIG.PHP
    if (!copy(PATH_LIB . "CORE-Config.php", PATH_LIB . "CORE-Config.old")) {
      exit("Failed to backup config file - " . PATH_LIB . "CORE-Config.old");
    }

    // (F11) UPDATE LIB/CORE-CONFIG.PHP
    $cfg = file(PATH_LIB . "CORE-Config.php") or exit("Cannot read". PATH_LIB ."CORE-Config.php");
    foreach ($cfg as $j=>$line) { foreach ($replace as $k=>$v) { if (strpos($line, "\"$k\"") !== false) {
      if ($k!="API_HTTPS" && $k!="API_CORS") { $v = "\"$v\""; }
      $cfg[$j] = "define(\"$k\", $v); // CHANGED BY INSTALLER\r\n";
      unset($replace[$k]);
      if (count($replace)==0) { break; }
    }}}
    try { file_put_contents(PATH_LIB . "CORE-Config.php", implode("", $cfg)); }
    catch (Exception $ex) { exit("Error writing to ". PATH_LIB ."CORE-Config.php"); }

    // (F12) ALMOST DONE...
    return "G";
  }

  // (PHASE G) CLEAN UP
  function G () {
    // (G1) GENERATE LIST OF ASSET FILES FOR CLIENTS TO CACHE
    $this->Core->load("CCache");
    $this->CCache->init();

    // (G2) SWAP OUT INDEX
    file_put_contents(PATH_BASE . "index.php", <<<EOF
    <?php
    require __DIR__ . DIRECTORY_SEPARATOR . "lib" . DIRECTORY_SEPARATOR . "CORE-Go.php";
    \$_CORE->load("Route");
    \$_CORE->Route->run();
    EOF);

    // (G3) INSTALL COMPLETE!
    if (defined("I_RELOAD")) { $this->Core->redirect(); }
    exit("OK");
  }
}