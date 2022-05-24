<?php
// (A) ALREADY SIGNED IN
if (isset($_SESS["user"])) {
  $_CORE->redirect();
}

// (B) LOGIN PAGE
$_PMETA = ["load" => [
  ["s", HOST_ASSETS . "PAGE-login.js", "defer"]
]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<div class="row justify-content-center">
  <div class="col-md-10 bg-white border">
    <div class="row">
      <div class="col-4" style="background:url('<?= HOST_ASSETS ?>desk.jpg') center"></div>
      <form class="col-8 p-5" onsubmit="return signin();">
        <img src="<?= HOST_ASSETS ?>favicon.png" class="p-1 bg-primary rounded-circle" />
        <h3 class="my-4">PLEASE SIGN IN</h3>

        <div class="input-group mb-4">
          <span class="input-group-text mi" title="Email">email</span>
          <input type="email" id="user_email" class="form-control" placeholder="Email" required />
        </div>

        <div class="input-group mb-4">
          <span class="input-group-text mi" title="Password">lock</span>
          <input type="password" id="user_password" class="form-control" placeholder="Password" required />
          <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibility()"><span class="mi" id="visibility">visibility</span></button>
        </div>

        <input type="submit" class="btn btn-primary py-2 mb-4" value="Sign in" />
    </div>
  </div>
</div>
</div>
<script>
  function toggleVisibility() {
    var x = document.getElementById("user_password");
    var y = document.getElementById("visibility");
    if (x.type === "password") {
      x.type = "text";
      y.innerHTML = "visibility_off";
    } else {
      x.type = "password";
      y.innerHTML = "visibility";
    }
  }
</script>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>