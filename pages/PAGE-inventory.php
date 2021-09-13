<!-- (A) JAVASCRIPT -->
<script src="<?=HOST_ASSETS?>inventory.js"></script>
<script src="<?=HOST_ASSETS?>checker.js"></script>

<!-- (B) NAVIGATION -->
<nav class="navbar text-white sb-grey">
<div class="container-fluid">
  <h4>Manage Inventory</h4>
  <form class="d-flex" onsubmit="return inv.search()">
    <input type="text" id="inv-search" placeholder="Search" class="form-control form-control-sm"/>
    <button class="btn btn-primary">
      <span class="mi">search</span>
    </button>
    <button class="btn btn-primary" onclick="inv.addEdit()">
      <span class="mi">add</span>
    </button>
  </form>
</div>
</nav>

<!-- (C) INVENTORY LIST -->
<div id="inv-list" class="zebra my-4"></div>
