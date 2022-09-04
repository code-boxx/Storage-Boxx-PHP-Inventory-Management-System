var usr = {
  // (A) SHOW ALL USERS
  pg : 1, // current page
  find : "", // current search
  list : () => {
    cb.page(0);
    cb.load({
      page : "users/list", target : "user-list",
      data : {
        page : usr.pg,
        search : usr.find
      }
    });
  },

  // (B) GO TO PAGE
  //  pg : int, page number
  goToPage : pg => { if (pg!=usr.pg) {
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
  addEdit : id => {
    cb.load({
      page : "users/form", target : "cb-page-2",
      data : { id : id ? id : "" },
      onload : () => { cb.page(1); }
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

    // (E2) PASSWORD STRENGTH
    if (!cb.checker(data.password)) {
      cb.modal("Error", "Password must be at least 8 characters alphanumeric");
      return false;
    }

    // (E3) AJAX
    cb.api({
      mod : "users", req : "save",
      data : data,
      passmsg : "User Saved",
      onpass : usr.list
    });
    return false;
  },

  // (F) DELETE USER
  //  id : int, user ID
  //  confirm : boolean, confirmed delete
  del : id => {
    cb.modal("Please confirm", "Delete user?", () => {
      cb.api({
        mod : "users", req : "del",
        data : { id: id },
        passmsg : "User Deleted",
        onpass : usr.list
      });
    });
  }
};
window.addEventListener("load", usr.list);