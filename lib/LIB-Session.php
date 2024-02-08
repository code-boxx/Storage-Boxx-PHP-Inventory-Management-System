<?php
class Session extends Core {
  // (A) COMMON COOKIE "TEMPLATE"
  private $cookie = [
    "domain" => HOST_NAME,
    "path" => "/",
    "httponly" => true,
    "expires" => 0,
    // "secure" => true,
    "samesite" => "Lax"
  ];

  // (B) CONSTRUCTOR - AUTO VALIDATE JWT COOKIE & RESTORE SESSION DATA
  function __construct ($core) {
    // (B1) INIT - CORE LINKS
    parent::__construct($core);
    $_SESSION = [];
    $valid = false;

    // (B2) DECODE JWT COOKIE
    if (isset($_COOKIE["cbsess"])) { try {
      require PATH_LIB . "JWT/autoload.php";
      $token = Firebase\JWT\JWT::decode(
        $_COOKIE["cbsess"], new Firebase\JWT\Key(JWT_SECRET, JWT_ALGO)
      );
      $valid = is_object($token);
    } catch (Exception $e) { $valid = false; }}

    // (B3) EXPIRED? VALID ISSUER? VALID AUDIENCE?
    if ($valid) {
      $now = strtotime("now");
      $valid = $token->iss == JWT_ISSUER &&
      $token->aud == HOST_NAME &&
      $token->nbf <= $now;
      if ($valid && JWT_EXPIRE!=0) {
        $valid = isset($token->exp) ? ($token->exp < $now) : false;
      }
    }

    // (B4) UNPACK COOKIE DATA INTO SESSION
    if ($valid) {
      $_SESSION = (array) $token->data;
      foreach ($_SESSION as $k=>$v) {
        if (is_object($v)) { $_SESSION[$k] = (array) $v; }
      }
      unset($token);
    }

    // (B5) INVALID SESSION
    if (!$valid && isset($_COOKIE["cbsess"])) {
      $this->destroy();
      throw new Exception("Invalid or expired session.");
    }

    // (B6) OK - VALID SESSION HOOK
    unset($_COOKIE["cbsess"]);
    require PATH_LIB . "HOOK-SESS-Load.php";
  }

  // (C) CREATE CBSESS COOKIE
  function save () {
    // (C1) FILTER SESSION DATA TO PUT INTO COOKIE
    $data = $_SESSION;
    require PATH_LIB . "HOOK-SESS-Save.php";

    // (C2) GENERATE JWT COOKIE
    require PATH_LIB . "JWT/autoload.php";
    $now = strtotime("now");
    $token = [
      "iat" => $now, // issued at
      "nbf" => $now, // not before
      "jti" => base64_encode(random_bytes(16)), // json token id
      "iss" => JWT_ISSUER, // issuer
      "aud" => HOST_NAME, // audience
      "data" => $data // additional data
    ];
    if (JWT_EXPIRE > 0) { $token["exp"] = $now + JWT_EXPIRE; } // expiry
    $token = Firebase\JWT\JWT::encode($token, JWT_SECRET, JWT_ALGO);
    setcookie("cbsess", $token, $this->cookie);
  }

  // (D) DESTROY SESSION + COOKIE
  function destroy () {
    // (D1) EXPIRE HTTP COOKIE
    $options = $this->cookie;
    $options["expires"] = -1;
    setcookie("cbsess", "", $options);

    // (D2) CLEAR ALL SESSION DATA
    $_SESSION = [];
  }
}