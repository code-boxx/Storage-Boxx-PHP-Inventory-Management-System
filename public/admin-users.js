var usr = {
  // (A) LIST () : SHOW ALL USERS
  pg : 1, // CURRENT PAGE
  find : "", // CURRENT SEARCH
  list : function ()  {
    common.ajax({
      url : urlroot + "users-ajax-list",
      target : "user-list",
      data : {
        pg : usr.pg,
        search : usr.find
      }
    });
  },

  // (B) GOTOPAGE () : GO TO PAGE
  //  pg : page number
  goToPage : function (pg) { if (pg!=usr.pg) {
    usr.pg = pg;
    usr.list();
  }},

  // (C) SEARCH() : SEARCH USER
  search : function () {
    usr.find = document.getElementById("user-search").value;
    usr.pg = 1;
    usr.list();
    return false;
  },

  // (D) ADDEDIT () : SHOW ADD/EDIT DOCKET
  // id : user ID, for edit only
  addEdit : function (id) {
    common.ajax({
      url : urlroot + "users-ajax-form",
      data : { id : id ? id : "" },
      target : "pageB",
      onpass : function () { common.page("B"); }
    });
  },

  // (E) SAVE () : SAVE USER
  save : function () {
    // (E1) GET DATA
    var data = {
      reqA : "save",
      name : document.getElementById("user_name").value,
      email : document.getElementById("user_email").value
    };
    var id = document.getElementById("user_id").value;
    var pass = document.getElementById("user_password").value;
    if (id!="") { data.id = id; }
    if (pass!="") { data.pass = pass; }

    // (E2) AJAX
    common.ajax({
      url : urlapi + "User",
      data : data,
      apass : "User save OK",
      onpass : function () {
        usr.list();
        common.page('A');
      }
    });
    return false;
  },

  // (F) DEL () : DELETE USER
  // id : user ID
  del : function (id) { if (confirm("Delete user?")) {
    common.ajax({
      url : urlapi + "User",
      data : {
        reqA : "del",
        id : id
      },
      apass : "User deleted",
      onpass : function () {
        usr.list();
        common.page('A');
      }
    });
  }}
};
window.addEventListener("load", usr.list);