<?php
// (A) GET USER
$user = $_CORE->autoCall("Users", "get");
if (!is_array($user)) { exit("Invalid user"); }
?>
<h3 class="mb-3">USER NFC LOGIN TOKEN</h3>

<!-- (B) CREATE NEW TOKEN -->
<div class="fw-bold text-danger">CREATE NEW TOKEN</div>
<div class="bg-white border p-4 mb-3">
  <div class="input-group mb-2">
    <div class="input-group-prepend">
      <span class="input-group-text mi">nfc</span>
    </div>
    <input type="text" class="form-control bg-danger text-white" id="nfc-stat" readonly>
  </div>
  <div class="text-secondary">
    * A user can only have one login token. Creating a new token will nullify the previous one.
  </div>
</div>

<!-- (C) NULL NFC TOKEN -->
<div class="fw-bold text-danger">NULLIFY NFC TOKEN</div>
<div class="bg-white border p-4 mb-3">
  <input type="button" id="nfc-null" value="Nullify Login Token" onclick="usr.nfcNull(<?=$_POST["id"]?>)"
         class="btn btn-primary"<?=$user["user_token"]==""?" disabled":""?>>
</div>

<input type="button" value="Back" class="btn btn-danger" onclick="usr.nfcBack()">