<?php
class JWT extends Core {
  // (A) CREATE JWT TOKEN
  //  $data : custom data array, include your own data if required
  //          but make sure "user_id" is included
  function create ($data) {
    require PATH_LIB . "/jwt/autoload.php";
    $now = strtotime("now");
    $token = [
      "iat" => $now, // ISSUED AT
      "ndf" => $now, // NOT BEFORE
      "jti" => base64_encode(random_bytes(16)), // JSON TOKEN ID
      "iss" => JWT_ISSUER, // ISSUER
      "aud" => HOST_NAME, // AUDIENCE
      "data" => $data // ADDITIONAL DATA
    ];
    if (JWT_EXPIRE > 0) { $token["exp"] = $now + JWT_EXPIRE; } // EXPIRY
    $token = Firebase\JWT\JWT::encode($token, JWT_SECRET, JWT_ALGO);
    setcookie("jwt", $token, 0, "/", HOST_NAME, API_HTTPS);
  }

  // (B) VERIFY JWT TOKEN
  //  $set : set the user data into $globals["_user"]
  function verify ($set=true) {
    // (B1) JWT COOKIE SET?
    $valid = isset($_COOKIE["jwt"]);

    // (B2) DECODE JWT COOKIE
    if ($valid) {
      require PATH_LIB . "/jwt/autoload.php";
      try { $token = Firebase\JWT\JWT::decode($_COOKIE["jwt"], JWT_SECRET, [JWT_ALGO]); }
      catch (Exception $e) { $valid = false; }
      if ($valid) { $valid = is_object($token); }
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
      if ($set) {
        $GLOBALS["_USER"] = $this->DB->fetch(
          "SELECT * FROM `users` WHERE `user_id`=?",
          [$token->data->user_id]
        );
        unset($GLOBALS["_USER"]["user_password"]);
      }
      return true;
    } else {
      $this->error = "Invalid or expired token";
      return false;
    }
  }
}
