<?php
// (A) GET MOVEMENT HISTORY
$move = $_CORE->autoCall("Inventory", "getMove");
$mv = ["I"=>"login", "O"=>"logout", "T"=>"done_all"];

// (B) OUTPUT MOVEMENT HISTORY ?>
<div class="d-flex align-items-center p-2 bg-primary text-white">
  <div class="mi mi-smol mx-1">login</div>
  <div class="mx-1">Stock In</div>
  <div class="mi mi-smol mx-1">logout</div>
  <div class="mx-1">Stock Out</div>
  <div class="mi mi-smol mx-1">done_all</div>
  <div class="mx-1">Stock Take</div>
</div>

<?php if (is_array($move["data"])) { foreach ($move["data"] as $m) { ?>
<div class="d-flex align-items-center p-2">
  <div class="flex-grow-1">
    <div class="fw-bold">
      <?=$m["mvt_date"]?> (<?=$m["user_name"]?>)
    </div>
    <div class="text-secondary">
      <?=$m["mvt_notes"]?>
    </div>
  </div>
  <div class="pe-3">
    <div class="mi mi-smol mx-1"><?=$mv[$m["mvt_direction"]]?></div> <?=$m["mvt_qty"]?>
  </div>
</div>
<?php }} else { ?>
<div class="d-flex align-items-center p-2">No movement history.</div>
<?php }

// (C) PAGINATION
$_CORE->load("Page");
$_CORE->Page->draw($move["page"], "check.goToPage");
