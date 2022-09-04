<?php
// (A) ALREADY SIGNED IN
if (isset($_SESS["user"])) { $_CORE->redirect(); }

// (B) HTML PAGE
$_PMETA = ["load" => [["s", HOST_ASSETS."PAGE-login.js", "defer"]]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<div class="row justify-content-center">
<div class="col-md-10 bg-white border" style="max-width:1000px">
  <div class="row">
    <div class="col-4" style="background:url('<?=HOST_ASSETS?>book.jpg') center"></div>
    <form class="col-8 p-5" onsubmit="return login();">
      <img src="<?=HOST_ASSETS?>favicon.png" class="p-2 rounded-circle" style="background:#f1f1f1">
      <h3 class="my-4">PLEASE SIGN IN</h3>

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

      <input type="submit" class="btn btn-primary py-2 mb-4" value="Sign in">
    </div>
  </div>
</div>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>