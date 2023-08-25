<?php
// (A) GET SUPPLIER
$sup = $_CORE->autoCall("Suppliers", "get"); 
?>
<!-- (B) NAVIGATION -->
<div class="d-flex align-items-center mb-3">
  <div class="flex-grow-1">
    <h3 class="mb-0">SUPPLIER ITEMS</h3>
    <small class="fw-bold"><?=$sup["sup_name"]?></small>
  </div>
  <button type="button" class="btn btn-danger p-3 mx-1 ico-sm icon-undo2" onclick="cb.page(1)"></button>
</div>

<!-- (C) SEARCH BAR -->
<form class="d-flex align-items-stretch head border mb-3 p-2" onsubmit="return items.search()">
  <input type="text" id="item-search" placeholder="Search" class="form-control form-control-sm">
  <button type="submit" class="btn btn-primary p-3 mx-1 ico-sm icon-search"></button>
  <button class="btn btn-primary p-3 ico-sm icon-arrow-right" type="button" data-bs-toggle="dropdown"></button>
  <ul class="dropdown-menu dropdown-menu-dark">
    <li class="dropdown-item" onclick="items.addEdit()">
      <i class="text-secondary ico-sm icon-plus"></i> Add Single
    </li>
    <li class="dropdown-item" onclick="items.import()">
      <i class="text-secondary ico-sm icon-upload3"></i> Import CSV
    </li>
  </ul>
</form>

<!-- (D) ITEMS LIST -->
<div id="item-list" class="zebra my-4"></div>