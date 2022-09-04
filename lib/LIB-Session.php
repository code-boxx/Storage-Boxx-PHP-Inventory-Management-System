<?php
class Session extends Core {
  // (A) COMMON COOKIE "TEMPLATE"
  private $cookie = [
    "domain" => HOST_NAME,
    "path" => "/",
    "httponly" => true,
    "expires" => 0
    // "secure" => true,
    // "samesite" => "None"
  ];

  // (B) CONSTRUCTOR - AUTO VALIDATE JWT COOKIE & RESTORE $_SESS
  function __construct ($core) {
    // (B1) INIT - CORE LINKS
    parent::__construct($core);
    global $_SESS;

    // (B2) DECODE JWT COOKIE
    $valid = false;
    if (isset($_COOKIE["cbsess"])) { try {
      require PATH_LIB . "jwt/autoload.php";
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

    // (B4) UNPACK COOKIE DATA
    if ($valid) {
      $_SESS = (array) $token->data;
      foreach ($_SESS as $k=>$v) {
        if (is_object($v)) { $_SESS[$k] = (array) $v; }
      }
      unset($token);
    }

    // (B5) GET USER FROM DATABASE
    if ($valid && isset($_SESS["user"])) {
      $user = $this->DB->fetch(
        "SELECT * FROM `users` WHERE `user_id`=?", [$_SESS["user"]["user_id"]]
      );
      $valid = is_array($user);
      if ($valid) {
        unset($user["user_password"]);
        $_SESS["user"] = $user;
      }
    }

    // (B6) INVALID SESSION
    if (!$valid && isset($_COOKIE["cbsess"])) {
      $this->destroy();
      throw new Exception("Invalid or expired session.");
    }

    // (B7) OK
    unset($_COOKIE["cbsess"]);
  }

  // (C) CREATE CBSESS COOKIE
  function create () {
    // (C1) GRAB ALL DATA FROM $_SESS - EXCEPT USER
    global $_SESS;
    $data = $_SESS;
    if (isset($data["user"])) {
      $data["user"] = ["user_id" => $data["user"]["user_id"]];
    }

    // (C2) GENERATE JWT COOKIE
    require PATH_LIB . "jwt/autoload.php";
    $now = strtotime("now");
    $token = [
      "iat" => $now, // ISSUED AT
      "nbf" => $now, // NOT BEFORE
      "jti" => base64_encode(random_bytes(16)), // JSON TOKEN ID
      "iss" => JWT_ISSUER, // ISSUER
      "aud" => HOST_NAME, // AUDIENCE
      "data" => $data // ADDITIONAL DATA
    ];
    if (JWT_EXPIRE > 0) { $token["exp"] = $now + JWT_EXPIRE; } // EXPIRY
    $token = Firebase\JWT\JWT::encode($token, JWT_SECRET, JWT_ALGO);
    setcookie("cbsess", $token, $this->cookie);
  }

  // (D) DESTROY SESSION + COOKIE
  function destroy () {
    // (D1) EXPIRE HTTP COOKIE
    $options = $this->cookie;
    $options["expires"] = -1;
    setcookie("cbsess", "", $options);

    // (D2) CLEAR ALL SESSION VARS
    global $_SESS;
    $_SESS = [];
  }
}