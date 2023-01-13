<!-- (A) NAVIGATION -->
<div class="d-flex align-items-center mb-3">
  <div class="flex-grow-1">
    <h3 class="mb-0">IMPORT ITEMS</h3>
  </div>
  <button class="btn btn-danger mi" onclick="cb.page(0)">
    reply
  </button>
</div>

<!-- (B) SELECT CSV FILE -->
<div id="item-import-select">
  <div class="head border mb-2 p-2 d-flex align-items-center">
    <input type="file" class="form-control" required accept=".csv" 
           id="item-import-file" onchange="iimport.read()">
  </div>
  <small>
    * CSV Columns - SKU | Item Name | Item Description | Unit | Current Stock | Watch Level<br>
    * <a href="<?=HOST_ASSETS?>0-dummy-items.csv">Example CSV</a>
  </small>
</div>

<!-- (C) ITEMS IMPORT LIST -->
<table id="item-import-table" class="table table-striped d-none">
  <thead><tr class="table-dark">
    <th>SKU</th>
    <th>Name</th>
    <th>Desc</th>
    <th>Unit</th>
    <th>Stock</th>
    <th>Watch</th>
    <th>Status</th>
  </tr></thead>
  <tbody id="item-import-list"></tbody>
<table>