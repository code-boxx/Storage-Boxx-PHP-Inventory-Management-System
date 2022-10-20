<?php
// (A) ALREADY SIGNED IN
if (isset($_SESS["user"])) { $_CORE->redirect(); }

// (B) HTML PAGE
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."PAGE-nfc.js", "defer"],
  ["s", HOST_ASSETS."PAGE-login.js", "defer"]
]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<div class="row justify-content-center">
<div class="col-md-10 bg-white border" style="max-width:1000px">
<div class="row">
  <div class="col-2" style="background:url('<?=HOST_ASSETS?>book.jpg');background-size:cover"></div>
  <form class="col-10 p-4" onsubmit="return login.go();">
    <img src="<?=HOST_ASSETS?>favicon.png" class="p-2 mb-3 rounded-circle" style="background:#f1f1f1">
    <h3 class="m-0">SIGN IN</h3>
    <div class="mb-3 text-secondary">
      Enter your email/password<span id="nfc-login-a" class="d-none"> or click on "NFC"</span>
    </div>

    <div class="input-group mb-4">
      <div class="input-group-prepend">
        <span class="input-group-text mi">email</span>
      </div>
      <input type="email" id="login-email" class="form-control" placeholder="Email" required>
    </div>

    <div class="input-group mb-4">
      <div class="input-group-prepend">
        <span class="input-group-text mi">lock</span>
      </div>
      <input type="password" id="login-pass" class="form-control" placeholder="Password" required>
    </div>

    <div class="d-flex align-items-stretch mb-4">
      <input type="submit" class="btn btn-primary" value="Sign In">
      <button id="nfc-login-b" class="btn btn-primary d-flex align-items-center ms-2 d-none" type="button" onclick="login.nfc()">
        <i class="mi">nfc</i> <span id="nfc-login-c" class="ms-2">NFC</span>
      </button>
    </div>
    <div><a href="<?=HOST_BASE?>forgot">Forgot Password</a></div>
  </form>
</div>
</div>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>