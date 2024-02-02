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
<h3 class="mb-3">REPORTS</h3>
<div class="d-flex flex-wrap">
  <!-- (B1) MOVEMENT CSV -->
  <form class="m-1 p-4 bg-white border" method="post" target="_blank" action="<?=HOST_BASE?>report/movement">
    <div class="fw-bold text-danger mb-2">ITEMS MOVEMENT</div>
    <div class="form-floating mb-4">
      <select class="form-select" name="range">
        <option value="A">All Items</option>
        <option value="S">Summary</option>
      </select>
      <label>Range</label>
    </div>
    <div class="form-floating mb-4">
      <select name="month" class="form-select"><?php foreach ($months as $m=>$mth) {
        printf("<option value='%u'%s>%s</option>",
          $m, $m==$monthNow?" selected":"", $mth
        );
      } ?></select>
      <label>Month</label>
    </div>
    <div class="form-floating mb-4">
      <input type="number" name="year" max="<?=$yearNow?>" step="1" class="form-control" required value="<?=$yearNow?>">
      <label>Year</label>
    </div>
    <input type="submit" class="w-100 col btn btn-primary" value="CSV">
  </form>

  <!-- (B2) ITEMS LIST -->
  <form class="m-1 p-4 bg-white border" method="post" target="_blank" action="<?=HOST_BASE?>report/items">
    <div class="fw-bold text-danger mb-2">ITEMS LIST</div>
    <div class="form-floating mb-4">
      <select class="form-select" name="range">
        <option value="">All Items</option>
        <option value="M">Monitored Items Only</option>
      </select>
      <label>Range</label>
    </div>
    <input type="submit" class="w-100 col btn btn-primary" value="CSV">
  </form>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>