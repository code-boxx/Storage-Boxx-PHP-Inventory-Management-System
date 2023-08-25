<?php
// (A) ALREADY SIGNED IN
if (isset($_SESSION["user"])) { $_CORE->redirect(); }

// (B) PART 1 - ENTER EMAIL
if (!isset($_GET["i"]) && !isset($_GET["h"])) {
$_PMETA = ["load" => [["s", HOST_ASSETS."PAGE-forgot.js", "defer"]]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<div class="container">
<div class="row justify-content-center">
<div class="col-md-10 bg-white border">
<div class="row">
  <div class="col-4" style="background:url('<?=HOST_ASSETS?>users.webp') center;background-size:cover"></div>
  <form class="col-8 p-4" onsubmit="return forgot();">
    <img src="<?=HOST_ASSETS?>favicon.png" class="p-2 rounded-circle" style="width:128px;height:128px;background:#f1f1f1">
    <h3 class="mt-4 mb-0">FORGOT PASSWORD</h3>
    <div class="mb-4 text-secondary">No worries. Enter your email, and we will send you a password reset link.</div>

    <div class="form-floating mb-4">
      <input type="email" id="forgot-email" class="form-control" required>
      <label>Email</label>
    </div>

    <button type="submit" class="my-1 btn btn-primary d-flex-inline">
      <i class="ico-sm icon-history"></i> Reset Request
    </button>

    <div class="text-secondary mt-3">
      <a href="<?=HOST_BASE?>login">Back To Login</a>
    </div>
  </form>
</div>
</div>
</div>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; }

// (C) PART 2 - VALIDATION
else {
$_CORE->load("Forgot");
$pass = $_CORE->Forgot->reset($_GET["i"], $_GET["h"]);
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<div class="container">
<div class="row justify-content-center">
<div class="col-md-10 bg-white border">
<div class="row">
  <div class="col-4" style="background:url('<?=HOST_ASSETS?>users.webp') center;background-size:cover"></div>
  <div class="col-8 p-4">
    <img src="<?=HOST_ASSETS?>favicon.png" class="p-2 rounded-circle" style="width:128px;height:128px;background:#f1f1f1">
    <h3 class="my-4"><?=$pass ? "DONE!" : "OH NO..."?></h3>
    <div class="mb-4"><?php
      if ($pass) { echo "A new password has been sent to your email."; }
      else { echo $_CORE->error; }
    ?></div>

    <div class="text-secondary mt-3">
      <a href="<?=HOST_BASE?>login">Back To Login</a>
    </div>
  </div>
</div>
</div>
</div>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; } ?>