<?php
// (A) ALREADY SIGNED IN
if (isset($_SESSION['user'])) {
  header('Location: ' . URL_BASE);
  exit();
}

// (B) HTML CONTENT
require PATH_PAGES . "TEMPLATE-top.php" ; ?>
<!-- (B1) JAVASCRIPT -->
<script>
function signin () {
  common.ajax({
    url : urlapi + "User",
    data : {
      req : "login",
      email : document.getElementById("user_email").value,
      pass : document.getElementById("user_password").value
    },
    apass : false,
    onpass : function () { location.href = urlroot; }
  });
  return false;
}
</script>

<!-- (B2) LOGIN FORM -->
<form class="standard" style="margin: 50px auto 0 auto;" onsubmit="return signin();">
  <h1>PLEASE SIGN IN</h1>
  <label for="user_email">Email</label>
  <input type="email" id="user_email" required autofocus/>
  <label for="user_password">Password</label>
  <input type="password" id="user_password" required/>
  <input type="submit" value="Sign in"/>
</form>
<?php require PATH_PAGES . "TEMPLATE-bottom.php" ;