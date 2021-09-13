<!-- (A) JAVASCRIPT -->
<script src="<?=HOST_ASSETS?>users.js"></script>

<!-- (B) NAVIGATION -->
<nav class="navbar text-white sb-grey">
<div class="container-fluid">
  <h4>Manage Users</h4>
  <form class="d-flex" onsubmit="return usr.search()">
    <input type="text" id="user-search" placeholder="Search" class="form-control form-control-sm"/>
    <button class="btn btn-primary">
      <span class="mi">search</span>
    </button>
    <button class="btn btn-primary" onclick="usr.addEdit()">
      <span class="mi">add</span>
    </button>
  </form>
</div>
</nav>

<!-- (C) USERS LIST -->
<div id="user-list" class="zebra my-4"></div>
