var usr = {
  // (A) SHOW ALL USERS
  pg : 1, // CURRENT PAGE
  find : "", // CURRENT SEARCH
  list : () => {
    sb.page(1);
    sb.load({
      page : "users/list",
      target : "user-list",
      data : {
        page : usr.pg,
        search : usr.find
      }
    });
  },

  // (B) GO TO PAGE
  //  pg : int, page number
  goToPage : (pg) => { if (pg!=usr.pg) {
    usr.pg = pg;
    usr.list();
  }},

  // (C) SEARCH USER
  search : () => {
    usr.find = document.getElementById("user-search").value;
    usr.pg = 1;
    usr.list();
    return false;
  },

  // (D) SHOW ADD/EDIT DOCKET
  // id : user ID, for edit only
  addEdit : (id) => {
    sb.load({
      page : "users/form",
      target : "sb-page-2",
      data : { id : id ? id : "" },
      onload : () => { sb.page(2); }
    });
  },

  // (E) SAVE USER
  save : () => {
    // (E1) GET DATA
    var data = {
      name : document.getElementById("user_name").value,
      email : document.getElementById("user_email").value,
      password : document.getElementById("user_password").value
    };
    var id = document.getElementById("user_id").value;
    if (id!="") { data.id = id; }

    // (E2) AJAX
    sb.api({
      mod : "users",
      req : "save",
      data : data,
      passmsg : "User Saved",
      onpass : usr.list
    });
    return false;
  },

  // (F) DELETE USER
  //  id : int, user ID
  //  confirm : boolean, confirmed delete
  del : (id, confirm) => {
    if (confirm) {
      sb.api({
        mod : "users",
        req : "del",
        data : { id: id },
        passmsg : "User Deleted",
        onpass : usr.list
      });
    } else {
      sb.modal("Please confirm", "Delete user?", () => {
        usr.del(id, true);
      });
    }
  }
};
window.addEventListener("load", usr.list);
