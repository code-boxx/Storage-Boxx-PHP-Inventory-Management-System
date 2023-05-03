<!-- (A) NAVIGATION -->
<div class="d-flex align-items-center mb-3">
  <div class="flex-grow-1">
    <h3 class="mb-0">IMPORT SUPPLIERS</h3>
  </div>
  <button class="btn btn-danger mi" onclick="cb.page(1)">
    reply
  </button>
</div>

<!-- (B) SELECT CSV FILE -->
<div id="sup-import-select">
  <div class="head border mb-2 p-2 d-flex align-items-center">
    <input type="file" class="form-control" required accept=".csv" 
           id="sup-import-file" onchange="simport.read()">
  </div>
  <small>
    * CSV Columns - Name | Tel | Email | Address<br>
    * <a href="<?=HOST_ASSETS?>0-dummy-suppliers.csv">Example CSV</a>
  </small>
</div>

<!-- (C) SUPPLIERS IMPORT LIST -->
<table id="sup-import-table" class="table table-striped d-none">
  <thead><tr class="table-dark">
    <th>Name</th>
    <th>Tel</th>
    <th>Email</th>
    <th>Address</th>
    <th>Status</th>
  </tr></thead>
  <tbody id="sup-import-list"></tbody>
<table>