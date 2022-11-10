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
    * CSV file format - sku | name | description | unit | watch level
  </small>
</div>

<!-- (C) ITEMS IMPORT LIST -->
<table id="item-import-table" class="table table-striped d-none">
  <thead><tr class="table-dark">
    <th>SKU</th>
    <th>Name</th>
    <th>Description</th>
    <th>Unit</th>
    <th>Watch</th>
    <th>Status</th>
  </tr></thead>
  <tbody id="item-import-list"></tbody>
<table>