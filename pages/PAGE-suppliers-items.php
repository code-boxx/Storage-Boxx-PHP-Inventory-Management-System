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
  <button class="btn btn-danger mi me-1" onclick="cb.page(0)">
    reply
  </button>
</div>

<!-- (B) SEARCH BAR -->
<form class="d-flex align-items-stretch head border mb-3 p-2" onsubmit="return items.search()">
  <input type="text" id="item-search" placeholder="Search" class="form-control form-control-sm">
  <button type="submit" class="btn btn-primary mi mx-1">
    search
  </button>
  <button class="btn btn-primary mi" type="button" data-bs-toggle="dropdown">
    add
  </button>
  <ul class="dropdown-menu dropdown-menu-dark">
    <li class="dropdown-item" onclick="items.addEdit()">
      <i class="mi mi-smil">add</i> Add Single
    </li>
    <li class="dropdown-item" onclick="iimport.init()">
      <i class="mi mi-smil">upload</i> Import CSV
    </li>
  </ul>
</form>

<!-- (C) ITEMS LIST -->
<div id="item-list" class="zebra my-4"></div>