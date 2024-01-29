<?php
class NFCIN extends Core {
  // (A) INIT
  private $nlen = 12; // 12 characters nfc login random hash
  function __construct ($core) {
    parent::__construct($core);
    $core->load("Users");
  }

  // (B) CREATE NEW NFC LOGIN TOKEN
  //  $id : user id
  function add ($id) {
    // (B1) CHECK VALID USER
    if (!is_array($this->Users->get($id))) {
      $this->error = "Invalid user";
      return false;
    }

    // (B2) UPDATE TOKEN
    $token = $this->Core->random($this->nlen);
    $this->Users->hashAdd($id, "NFC", password_hash($token, PASSWORD_DEFAULT));

    // (B3) RETURN ENCODED TOKEN
    require PATH_LIB . "JWT/autoload.php";
    return Firebase\JWT\JWT::encode([$id, $token], JWT_SECRET, JWT_ALGO);
  }

  // (C) NULLIFY NFC TOKEN
  //  $id : user id
  function del ($id) {
    $this->Users->hashDel($id, "NFC");
    return true;
  }

  // (D) NFC TOKEN LOGIN
  function login ($token) {
    // (D1) DECODE TOKEN
    $valid = true;
    try {
      require PATH_LIB . "JWT/autoload.php";
      $token = Firebase\JWT\JWT::decode(
        $token, new Firebase\JWT\Key(JWT_SECRET, JWT_ALGO)
      );
      $valid = is_object($token);
      if ($valid) {
        $token = (array) $token;
        $valid = count($token)==2;
      }
    } catch (Exception $e) { $valid = false; }

    // (D2) VERIFY TOKEN
    if ($valid) {
      $user = $this->Users->get($token[0], "NFC");
      $valid = (is_array($user) && password_verify($token[1], $user["hash_code"]));
    }

    // (D3) SESSION START
    if ($valid) {
      $_SESSION["user"] = $user;
      unset($_SESSION["user"]["user_password"]);
      unset($_SESSION["user"]["hash_code"]);
      unset($_SESSION["user"]["hash_time"]);
      unset($_SESSION["user"]["hash_tries"]);
      $this->Session->save();
      return true;
    }

    // (D4) NADA
    $this->error = "Invalid token";
    return false;
  }
}