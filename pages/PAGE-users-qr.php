<?php
// (A) GET USER
$_POST["hash"] = "QR";
$user = $_CORE->autoCall("Users", "get");
if (!is_array($user)) { exit("Invalid user"); } ?>
<h3 class="mb-3">USER QR LOGIN TOKEN</h3>

<!-- (B) CREATE NEW TOKEN -->
<div class="fw-bold text-danger">CREATE NEW TOKEN</div>
<form class="bg-white border p-4 mb-3" onsubmit="usr.hqNull.disabled = false"
      method="post" target="_blank" action="<?=HOST_BASE?>report/qr">
  <input type="hidden" name="for" value="user">
  <input type="hidden" name="id" value="<?=$_POST["id"]?>">
  <button id="qr-btn" class="my-1 btn btn-primary d-flex-inline" type="submit">
    <i class="ico-sm icon-qrcode"></i> Create
  </button>
  <div class="text-secondary mt-2">
    * A user can only have one login token, creating a new token will nullify the previous one.
  </div>
</form>

<!-- (C) NULL QR TOKEN -->
<div class="fw-bold text-danger">NULLIFY QR TOKEN</div>
<div class="bg-white border p-4 mb-3">
  <button id="qr-null" class="my-1 btn btn-primary d-flex-inline"
          onclick="usr.qrNull(<?=$_POST["id"]?>)"<?=$user["hash_code"]==""?" disabled":""?>>
    <i class="ico-sm icon-blocked"></i> Nullify
  </button>
  <div class="text-secondary mt-2">
    * The user's QR login token will be nullified, but the login email/password remains unaffected.
  </div>
</div>

<button type="button" class="my-1 btn btn-danger d-flex-inline" onclick="cb.page(1)">
  <i class="ico-sm icon-undo2"></i> Back
</button>