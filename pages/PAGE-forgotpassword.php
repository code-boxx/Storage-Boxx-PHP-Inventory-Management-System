<?php
// (A) ALREADY SIGNED IN
if (isset($_SESS["user"])) { $_CORE->redirect(); }

// (B) LOGIN PAGE
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."PAGE-login.js", "defer"]
]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<div class="row justify-content-center">
<div class="col-md-10 bg-white border">
  <div class="row">
    <div class="col-4" style="background:url('<?=HOST_ASSETS?>desk.jpg') center"></div>
    <form class="col-8 p-5" onsubmit="return signin();">
      <div style="display: flex">
        <img src="<?= HOST_ASSETS ?>favicon.png" class="p-1 bg-primary rounded-circle" />
        <h3 style="display: flex; align-Items: center; justify-Content: center;">Storage-Boxx</h3>
      </div>
      <h3 class="my-4">FORGOT PASSWORD</h3>
      <h5>Please verify your email</h5>
      <div class="input-group mb-4">
        <div class="input-group-prepend">
          <span class="input-group-text mi" title="Email">email</span>
        </div>
        <input type="email" id="user_email" class="form-control" placeholder="Email" required/>
      </div>
      
      <div style="float: right;">
        <input type="submit" class="btn btn-primary py-2 mb-4" value="Send Verification Link"/>
      </div>
      
    </div>
  </div>
</div>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>
