<?php
class WAIN extends Core {
  // (A) SETTINGS & INIT
  private $timeout = 30;
  private $wa;
  function __construct ($core) {
    // (A1) "CORE LINK"
    parent::__construct($core);

    // (A2) LOAD PHP WEBAUTHN & USER MODULE
    require PATH_LIB . "WebAuthn/autoload.php";
    $this->wa = new lbuchs\WebAuthn\WebAuthn(HOST_NAME, HOST_NAME);
    $core->load("Users");
  }

  // (B) HELPER - CREATE CHALLENGE KEY
  function setChallenge ($id) : void {
     $this->Users->hashAdd(
      $id, "PLC", 
      bin2hex(($this->wa->getChallenge())->getBinaryString())
    );
  }

  // (C) HELPER - GET CHALLENGE KEY
  function getChallenge ($id) {
    $challenge = $this->Users->hashGet($id, "PLC");
    if (!is_array($challenge)) {
      $this->error = "Invalid credentials";
      return false;
    }
    return hex2bin($challenge["hash_code"]);
  }

  // (D) HELPER - GET USER & CREDENTIAL
  //  $email : user email
  function getUser ($email) {
    $user = $this->Users->get($email, "PL");
    if (!is_array($user)) {
      $this->error = "Invalid user";
      return false;
    }
    if ($user["hash_code"]==null) {
      $this->error = "Please register for passwordless login first.";
      return false;
    }
    if ($user["user_level"]=="S") {
      $this->error = "Invalid user or password.";
      return false;
    }
    $user["hash_code"] = json_decode($user["hash_code"]);
    $user["hash_code"]->credentialId = hex2bin($user["hash_code"]->credentialId);
    $user["hash_code"]->AAGUID = hex2bin($user["hash_code"]->AAGUID);
    return $user;
  }

  // (E) REGISTRATION PART 1 - GENERATE PUBLIC KEY
  function regA () {
    $args = $this->wa->getCreateArgs(
      \decbin($_SESSION["user"]["user_id"]), $_SESSION["user"]["user_email"], $_SESSION["user"]["user_name"],
      $this->timeout, false, true
    );
    $this->setChallenge($_SESSION["user"]["user_id"]);
    return json_encode($args);
  }

  // (F) REGISTRATION PART 2 - CHECK & SAVE CREDENTIAL
  function regB () {
    // (F1) GET CHALLENGE
    $challenge = $this->getChallenge($_SESSION["user"]["user_id"]);

    // (F2) VERIFY & CREATE CREDENTIAL
    try {
      $data = $this->wa->processCreate(
        base64_decode($_POST["client"]),
        base64_decode($_POST["attest"]),
        $challenge,
        true, true, false
      );
      $data->credentialId = bin2hex($data->credentialId);
      $data->AAGUID = bin2hex($data->AAGUID);
      $data = json_encode((array)$data);
    } catch (Exception $ex) {
      $this->error = $ex->getMessage();
      return false;
    }

    // (F3) SAVE
    $this->Users->hashAdd($_SESSION["user"]["user_id"], "PL", $data);
    $this->Users->hashDel($_SESSION["user"]["user_id"], "PLC");
    return true;
  }

  // (G) UNREGISTER
  function unreg () {
    $this->Users->hashDel($_SESSION["user"]["user_id"], "PL");
    $this->Users->hashDel($_SESSION["user"]["user_id"], "PLC");
    return true;
  }

  // (H) LOGIN VALIDATION PART 1 - GENERATE PUBLIC KEY
  //  $email : user email
  function loginA ($email) {
    $user = $this->getUser($email);
    if ($user===false) { return false; }
    $args = $this->wa->getGetArgs([$user["hash_code"]->credentialId], $this->timeout);
    $this->setChallenge($user["user_id"]);
    return json_encode($args);
  }

  // (I) LOGIN VALIDATION PART 2 - CHECK & PROCEED
  function loginB ($email) {
    // (I1) GET USER, CREDENTIAL, CHALLENGE
    $user = $this->getUser($email);
    if ($user===false) { return false; }
    $challenge = $this->getChallenge($user["user_id"]);
    $id = base64_decode($_POST["id"]);

    // (I2) CHECK CREDENTIAL
    if ($user["hash_code"]->credentialId !== $id) {
      $this->error = "Invalid credentials";
      return false;
    }
    $this->wa->processGet(
      base64_decode($_POST["client"]),
      base64_decode($_POST["auth"]),
      base64_decode($_POST["sig"]),
      $user["hash_code"]->credentialPublicKey,
      $challenge
    );

    // (I3) PROCESS LOGIN
    unset($user["user_password"]);
    unset($user["hash_code"]);
    unset($user["hash_time"]);
    unset($user["hash_tries"]);
    $_SESSION["user"] = $user;
    $this->Session->save();
    return true;
  }
}