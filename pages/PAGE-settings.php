<?php
// (A) GET ALL SETTINGS
$settings = $_CORE->Settings->getAll();

// (B) SETTINGS LIST
$_PMETA = [
  "load" => [["s", HOST_ASSETS."PAGE-settings.js", "defer"]]
];
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<h3 class="mb-3">SYSTEM SETTINGS</h3>
<form id="set-list" onsubmit="return save()">
  <div class="zebra my-4">
  <?php foreach ($settings as $o) { ?>
    <div class="d-flex align-items-center border p-2">
      <div class="flex-grow-1"><?=$o["setting_description"]?></div>
      <div>
      <input type="text" class="form-control" required
             name="<?=$o["setting_name"]?>" value="<?=$o["setting_value"]?>">
      </div>
    </div>
  <?php } ?>
  </div>
  <input type="submit" class="btn btn-primary" value="Save">
</form>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>