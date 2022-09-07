<?php
$_PMETA = ["load" => [["s", HOST_ASSETS."PAGE-push.js", "defer"]]];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<h3 class="mb-3">SEND PUSH NOTIFICATION</h3>
<form id="push_form" class="bg-white border p-4 mb-3" onsubmit="return send()">
  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text mi">format_quote</span>
    </div>
    <input type="text" class="form-control" id="push_title" required placeholder="Title">
  </div>
  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text mi">textsms</span>
    </div>
    <input type="text" class="form-control" id="push_txt" required placeholder="Message">
  </div>
  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text mi">info</span>
    </div>
    <input type="text" class="form-control" id="push_ico" required placeholder="Icon" value="<?=HOST_ASSETS?>push-ico.png">
  </div>
  <div class="input-group mb-3">
    <div class="input-group-prepend">
      <span class="input-group-text mi">image</span>
    </div>
    <input type="text" class="form-control" id="push_img" required placeholder="Image" value="<?=HOST_ASSETS?>push-bg.png">
  </div>
  <input type="submit" class="col btn btn-primary" value="Send">
</form>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>