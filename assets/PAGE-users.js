var usr = {
  // (A) SHOW ALL USERS
  pg : 1, // current page
  find : "", // current search
  list : silent => {
    if (silent!==true) { cb.page(1); }
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
  addEdit : id => cb.load({
    page : "users/form", target : "cb-page-2",
    data : { id : id ? id : "" },
    onload : () => cb.page(2)
  }),

  // (E) SAVE USER
  save : () => {
    // (E1) GET DATA
    var data = {
      name : document.getElementById("user_name").value,
      email : document.getElementById("user_email").value,
      password : document.getElementById("user_password").value,
      lvl : document.getElementById("user_level").value
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
      mod : "users", act : "save",
      data : data,
      passmsg : "User Saved",
      onpass : usr.list
    });
    return false;
  },

  // (F) DELETE USER
  //  id : int, user ID
  //  confirm : boolean, confirmed delete
  del : id => cb.modal("Please confirm", "Delete user?", () => cb.api({
    mod : "users", act : "del",
    data : { id: id },
    passmsg : "User Deleted",
    onpass : usr.list
  })),

  // (G) SHOW WRITE NFC PAGE
  hnBtn : null, // html write nfc button
  hnNull : null, // html null token button
  nfcShow : id => cb.load({
    page : "users/nfc", target : "cb-page-2",
    data : { id : id },
    onload : () => {
      usr.hnBtn = document.getElementById("nfc-btn");
      usr.hnNull = document.getElementById("nfc-null");
      if ("NDEFReader" in window) { usr.hnBtn.disabled = false; }
      cb.page(2);
    }
  }),

  // (H) CREATE NEW NFC LOGIN TAG
  nfcNew : id => cb.api({
    mod : "session", act : "nfcadd",
    data : { id : id },
    passmsg : false,
    onpass : res => {
      usr.hnNull.disabled = false;
      nfc.write(res.data);
    }
  }),

  // (I) NULLIFY NFC TOKEN
  nfcNull : id => cb.api({
    mod : "session", act : "nfcdel",
    data : { id : id },
    passmsg : "Login token nullified.",
    onpass : res => usr.hnNull.disabled = true
  }),

  // (J) SHOW WRITE QR PAGE
  hqNull : null, // html null token button
  qrShow : id => cb.load({
    page : "users/qr", target : "cb-page-2",
    data : { id : id },
    onload : () => {
      usr.hqNull = document.getElementById("qr-null");
      cb.page(2);
    }
  }),

  // (K) NULLIFY QR TOKEN
  qrNull : id => cb.api({
    mod : "session", act : "qrdel",
    data : { id : id },
    passmsg : "Login token nullified.",
    onpass : res => usr.hqNull.disabled = true
  }),

  // (L) IMPORT USERS
  import : () => im.init({
    name : "USERS",
    at : 2, back : 1,
    eg : "dummy-users.csv",
    api : { mod : "users", act : "import" },
    after : () => usr.list(true),
    cols : [
      ["Name", "name", true],
      ["Email", "email", true],
      ["Password", "password", true]
    ]
  })
};

// (M) INIT MANAGE USERS
window.addEventListener("load", () => {
  // (M1) LIST USERS
  usr.list();

  // (M2) ATTACH AUTOCOMPLETE
  autocomplete.attach({
    target : document.getElementById("user-search"),
    mod : "autocomplete", act : "user",
    onpick : res => usr.search()
  });

  // (M3) ATTACH NFC READER
  if (("NDEFReader" in window)) { nfc.init(); }
});