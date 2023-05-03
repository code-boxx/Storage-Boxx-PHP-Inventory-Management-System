<?php
$_PMETA = ["load" => [["s", HOST_ASSETS."PAGE-push.js", "defer"]]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<h3 class="mb-3">SEND PUSH NOTIFICATION</h3>
<form id="push_form" class="bg-white border p-4 mb-3" onsubmit="return send()">
  <div class="form-floating mb-4">
    <input type="text" class="form-control" id="push_title" required>
    <label>Title</label>
  </div>
  <div class="form-floating mb-4">
    <input type="text" class="form-control" id="push_txt" required>
    <label>Message</label>
  </div>
  <div class="form-floating mb-4">
    <input type="text" class="form-control" id="push_ico" required value="<?=HOST_ASSETS?>push-a.webp">
    <label>Icon</label>
  </div>
  <div class="form-floating mb-4">
    <input type="text" class="form-control" id="push_img" required value="<?=HOST_ASSETS?>push-b.webp">
    <label>Cover Image</label>
  </div>
  <input type="submit" class="col btn btn-primary" value="Send">
</form>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>