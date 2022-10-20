<?php
// (A) GET USER
$user = $_CORE->autoCall("Users", "get");
if (!is_array($user)) { exit("Invalid user"); }
?>
<h3 class="mb-3">USER NFC LOGIN TOKEN</h3>

<!-- (B) CREATE NEW TOKEN -->
<div class="fw-bold text-danger">CREATE NEW TOKEN</div>
<div class="bg-white border p-4 mb-3">
  <button id="nfc-btn" disabled class="btn btn-primary d-flex align-items-center" onclick="usr.nfcNew(<?=$_POST["id"]?>)">
    <i class="mi me-2">nfc</i> <span id="nfc-stat">Initializing</span>
  </button>
  <div class="text-secondary mt-2">
    * A user can only have one login token, creating a new token will nullify the previous one.
  </div>
</div>

<!-- (C) NULL NFC TOKEN -->
<div class="fw-bold text-danger">NULLIFY NFC TOKEN</div>
<div class="bg-white border p-4 mb-3">
  <input type="button" id="nfc-null" value="Nullify Login Token" onclick="usr.nfcNull(<?=$_POST["id"]?>)"
         class="btn btn-primary"<?=$user["user_token"]==""?" disabled":""?>>
  <div class="text-secondary mt-2">
    * The user's NFC login token will be nullified, but the login email/password remains unaffected.
  </div>
</div>

<input type="button" value="Back" class="btn btn-danger" onclick="usr.nfcBack()">