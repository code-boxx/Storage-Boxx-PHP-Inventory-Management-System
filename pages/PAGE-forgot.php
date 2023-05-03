<?php
// (A) ALREADY SIGNED IN
if (isset($_CORE->Session->data["user"])) { $_CORE->redirect(); }

// (B) PART 1 - ENTER EMAIL
if (!isset($_GET["i"]) && !isset($_GET["h"])) {
$_PMETA = ["load" => [["s", HOST_ASSETS."PAGE-forgot.js", "defer"]]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<div class="row justify-content-center">
<div class="col-md-10 bg-white border" style="max-width:1000px">
  <div class="row">
    <div class="col-3" style="background:url('<?=HOST_ASSETS?>forgot.webp') center;background-size:cover"></div>
    <form class="col-9 p-4" onsubmit="return forgot();">
      <img src="<?=HOST_ASSETS?>favicon.png" class="p-2 rounded-circle" style="background:#f1f1f1">
      <h3 class="mt-4">FORGOT PASSWORD</h3>
      <div class="mb-4">Enter your email below, a reset link will be sent.</div>

      <div class="form-floating mb-4">
        <input type="email" id="forgot-email" class="form-control" required>
        <label>Email</label>
      </div>

      <input type="submit" class="btn btn-primary py-2 mb-4" value="Reset Request">
      <div><a href="<?=HOST_BASE?>login">Back To Login</a></div>
    </form>
  </div>
</div>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; }

// (C) PART 2 - VALIDATION
else {
$_CORE->load("Forgot");
$pass = $_CORE->Forgot->reset($_GET["i"], $_GET["h"]);
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<div class="row justify-content-center">
<div class="col-md-10 bg-white border" style="max-width:1000px">
  <div class="row">
    <div class="col-3" style="background:url('<?=HOST_ASSETS?>forgot.webp') center;background-size:cover"></div>
    <div class="col-9 p-4">
      <img src="<?=HOST_ASSETS?>favicon.png" class="p-2 mb-3 mt-5 rounded-circle" style="background:#f1f1f1">
      <h3 class="my-4"><?=$pass?"DONE!":"OOOOOPPPSSSSSS...."?></h3>
      <div class="mb-4"><?php
        if ($pass) { echo "OK - New password sent to your email."; }
        else { echo $_CORE->error; }
      ?></div>
      <div class="mb-5"><a href="<?=HOST_BASE?>login">Back To Login</a></div>
    </div>
  </div>
</div>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; } ?>