<!-- (A) JAVASCRIPT -->
<script src="<?=HOST_ASSETS?>users.js"></script>

<!-- (B) NAVIGATION -->
<!-- (B1) PAGE HEADER -->
<nav class="navbar cb-grey"><div class="container-fluid">
  <h4>Manage Users</h4>
  <div class="d-flex">
    <button class="btn btn-primary" onclick="usr.addEdit()">
      <span class="mi">add</span>
    </button>
  </div>
</div></nav>

<!-- (B2) SEARCH BAR -->
<div class="searchBar"><form class="d-flex" onsubmit="return usr.search()">
  <input type="text" id="user-search" placeholder="Search" class="form-control form-control-sm"/>
  <button class="btn btn-primary">
    <span class="mi">search</span>
  </button>
</form></div>

<!-- (C) USERS LIST -->
<div id="user-list" class="container zebra my-4"></div>
