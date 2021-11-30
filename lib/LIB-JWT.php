<?php
class JWT extends Core {
  // (A) CREATE JWT TOKEN
  //  $user : user data
  function create ($user) {
    require PATH_LIB . "/jwt/autoload.php";
    $now = strtotime("now");
    $token = [
      "iat" => $now, // ISSUED AT
      "ndf" => $now, // NOT BEFORE
      "jti" => base64_encode(random_bytes(16)), // JSON TOKEN ID
      "iss" => JWT_ISSUER, // ISSUER
      "aud" => HOST_NAME, // AUDIENCE
      "data" => []
    ];
    if (JWT_EXPIRE > 0) { $token["exp"] = $now + JWT_EXPIRE; }
    foreach ($user as $k=>$v) {
      if ($k!="user_password") { $token["data"][$k] = $v; }
    }
    $token = Firebase\JWT\JWT::encode($token, JWT_SECRET, JWT_ALGO);
    setcookie("jwt", $token, 0, "/", HOST_NAME, API_HTTPS);
  }

  // (B) VERIFY JWT TOKEN
  //  $set : SET THE USER DATA INTO $GLOBALS["_USER"]
  function verify ($set=true) {
    // (B1) JWT COOKIE SET?
    $valid = isset($_COOKIE["jwt"]);

    // (B2) DECODE JWT COOKIE
    if ($valid) {
      require PATH_LIB . "/jwt/autoload.php";
      try { $token = Firebase\JWT\JWT::decode($_COOKIE["jwt"], JWT_SECRET, [JWT_ALGO]); }
      catch (Exception $e) { $valid = false; }
      $valid = is_object($token);
    }

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

    // (B4) OK - "REGISTER" USER?
    if ($valid) {
      if ($set) { $GLOBALS["_USER"] = (array) $token->data; }
      return true;
    } else {
      $this->error = "Invalid or expired token";
      return false;
    }
  }
}
