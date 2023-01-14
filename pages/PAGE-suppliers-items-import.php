<!-- (A) NAVIGATION -->
<div class="d-flex align-items-center mb-3">
  <div class="flex-grow-1">
    <h3 class="mb-0">IMPORT SUPPLIER ITEMS</h3>
    <small class="text-danger fw-bold">Existing supplier items will be overridden!</small>
  </div>
  <button class="btn btn-danger mi" onclick="cb.page(1)">
    reply
  </button>
</div>

<!-- (B) SELECT CSV FILE -->
<div id="sup-items-import-select">
  <div class="head border mb-2 p-2 d-flex align-items-center">
    <input type="file" class="form-control" required accept=".csv" 
           id="sup-items-file" onchange="iimport.read()">
  </div>
  <small>
    * CSV Columns - SKU | SSKU | Unit Price<br>
    * <a href="<?=HOST_ASSETS?>0-dummy-sup-items.csv">Example CSV</a>
  </small>
</div>

<!-- (C) SUPPLIER ITEMS IMPORT LIST -->
<table id="sup-items-import-table" class="table table-striped d-none">
  <thead><tr class="table-dark">
    <th>SKU</th>
    <th>SSKU</th>
    <th>Unit Price</th>
    <th>Status</th>
  </tr></thead>
  <tbody id="sup-items-import-list"></tbody>
<table>