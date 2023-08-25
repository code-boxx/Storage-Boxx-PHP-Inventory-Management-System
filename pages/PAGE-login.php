<?php
// (A) ALREADY SIGNED IN
if (isset($_SESSION["user"])) { $_CORE->redirect(); }

// (B) PAGE META & SCRIPTS
$_PMETA = ["load" => [
  // NFC LOGIN
  ["s", HOST_ASSETS."PAGE-nfc.js", "defer"],
  ["s", HOST_ASSETS."PAGE-wa-helper.js", "defer"],
  ["s", HOST_ASSETS."PAGE-login.js", "defer"]
]];

// (C) HTML PAGE
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<div class="container">
  <?php if ($_CORE->error!="") { ?>
  <!-- (C1) ERROR MESSAGE -->
  <div class="p-2 mb-3 text-light bg-danger"><?=$_CORE->error?></div>  
  <?php } ?>

  <!-- (C2) LOGIN FORM -->
  <div class="row justify-content-center">
  <div class="col-md-10 bg-white border">
  <div class="row">
    <div class="col-4" style="background:url('<?=HOST_ASSETS?>users.webp') center;background-size:cover"></div>
    <div class="col-8 p-4">
      <form onsubmit="return login();">
        <!-- (C2-1) NORMAL LOGIN -->
        <img src="<?=HOST_ASSETS?>favicon.png" class="p-2 rounded-circle" style="width:128px;height:128px;background:#f1f1f1">
        <h3 class="my-4">PLEASE SIGN IN</h3>

        <div class="form-floating mb-4">
          <input type="email" id="login-email" class="form-control" required>
          <label>Email</label>
        </div>

        <div class="form-floating mb-4">
          <input type="password" id="login-pass" class="form-control" required>
          <label>Password</label>
        </div>

        <button type="submit" class="my-1 btn btn-primary d-flex-inline">
          <i class="ico-sm icon-enter"></i> Sign In
        </button>

        <!-- (C2-2) MORE LOGIN -->
        <!-- (LOGIN WITH WEBAUTHN) -->
        <button type="button" id="wa-in" onclick="wa.go()" disabled class="my-1 btn btn-primary d-flex-inline">
          <i class="ico-sm icon-key"></i> Passwordless
        </button>
        <!-- (LOGIN WITH NFC) -->
        <button type="button" id="nfc-a" onclick="nin.go()" disabled class="my-1 btn btn-primary d-flex-inline">
          <i class="ico-sm icon-feed"></i> <span id="nfc-b">NFC</span>
        </button>
      </form>

      <!-- (C2-3) SOCIAL LOGIN -->

      <!-- (C2-4) FORGOT & NEW ACCOUNT -->
      <div class="text-secondary mt-3">
        <a href="<?=HOST_BASE?>forgot">Forgot Password</a>
      </div>
    </div>
  </div>
  </div>
  </div>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>