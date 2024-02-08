<?php
$_PMETA = ["load" => [
  ["s", HOST_ASSETS."PAGE-push.js", "defer"]
]];
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
    <input type="text" class="form-control" id="push_ico" required value="<?=HOST_ASSETS?>ico-512.png">
    <label>Icon</label>
  </div>

  <div class="form-floating mb-4">
    <input type="text" class="form-control" id="push_img" required value="<?=HOST_ASSETS?>banner.webp">
    <label>Cover Image</label>
  </div>

  <button type="submit" class="my-1 btn btn-primary d-flex-inline">
    <i class="ico-sm icon-arrow-right"></i> Send
  </button>
</form>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>