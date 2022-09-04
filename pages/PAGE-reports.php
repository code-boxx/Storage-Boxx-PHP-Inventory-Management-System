<?php
// (A) MONTH & YEAR
$months = [
  1 => "January", 2 => "Febuary", 3 => "March", 4 => "April",
  5 => "May", 6 => "June", 7 => "July", 8 => "August",
  9 => "September", 10 => "October", 11 => "November", 12 => "December"
];
$monthNow = date("m");
$yearNow = date("Y");

// (B) HTML
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<div class="d-flex flex-wrap">
  <!-- (B1) MOVEMENT CSV -->
  <form class="m-1 p-4 bg-white border" method="post" target="_blank" action="<?=HOST_BASE?>report/movement">
    <h5 class="mb-3">MOVEMENT REPORT</h5>
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text">Month</span>
      </div>
      <select name="month" class="form-control"><?php foreach ($months as $m=>$mth) {
        printf("<option value='%u'%s>%s</option>",
          $m, $m==$monthNow?" selected":"", $mth
        );
      } ?></select>
    </div>
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text">Year</span>
      </div>
      <input type="number" name="year" max="<?=$yearNow?>" step="1" class="form-control" required value="<?=$yearNow?>">
    </div>
    <input type="submit" class="col btn btn-primary" value="CSV">
  </form>

  <!-- (B2) ITEMS LIST -->
  <form class="m-1 p-4 bg-white border" method="post" target="_blank" action="<?=HOST_BASE?>report/items">
    <h5 class="mb-3">ITEMS LIST</h5>
    <div class="input-group mb-3">
      <div class="input-group-prepend">
        <span class="input-group-text">Range</span>
      </div>
      <select class="form-control" name="range">
        <option value="">All Items</option>
        <option value="M">Monitored Items Only</option>
      </select>
    </div>
    <input type="submit" class="col btn btn-primary" value="CSV">
  </form>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>