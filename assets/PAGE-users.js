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
  hnStat : null, // html write nfc button status
  hnNull : null, // html null token button
  nfcShow : id => cb.load({
    page : "users/nfc", target : "cb-page-2",
    data : { id : id },
    onload : () => {
      usr.hnBtn = document.getElementById("nfc-btn");
      usr.hnStat = document.getElementById("nfc-stat");
      usr.hnNull = document.getElementById("nfc-null");
      if ("NDEFReader" in window) {
        usr.hnStat.innerHTML = "Create Login Token";
        usr.hnBtn.disabled = false;
      } else {
        usr.hnStat.innerHTML = "Web NFC not available";
      }
      cb.page(2);
    }
  }),

  // (H) CREATE NEW NFC LOGIN TAG
  nfcNew : id => {
    // (H1) DISABLE "WRITE NFC" BUTTON
    usr.hnBtn.disabled = true;

    // (H2) REGISTER WITH SERVER + GET JWT
    cb.api({
      mod : "session", act : "nfcadd",
      data : { id : id },
      passmsg : false,
      onpass : res => {
        // (H2-1) ENABLE "NULLIFY" BUTTTON
        usr.hnNull.disabled = false;

        // (H2-2) ON SUCCESSFUL NFC WRITE
        nfc.onwrite = () => {
          nfc.standby();
          cb.modal("Successful", "Login token successfully created.");
          usr.hnStat.innerHTML = "Done";
        };

        // (H2-3) ON FAILED NFC WRITE
        nfc.onerror = err => {
          nfc.stop();
          console.error(err);
          cb.modal("ERROR", err.message);
          usr.hnStat.innerHTML = "ERROR!";
          usr.hnBtn.disabled = false;
        };

        // (H2-4) START NFC WRITE
        nfc.write(res.data);
        usr.hnStat.innerHTML = "Tap NFC tag to write";
      }
    })
  },

  // (I) NULLIFY NFC TOKEN
  nfcNull : id => cb.api({
    mod : "session", act : "nfcdel",
    data : { id : id },
    passmsg : "Login token nullified.",
    onpass : res => usr.hnNull.disabled = true
  }),

  // (J) END WRITE NFC SESSION
  nfcBack : () => {
    nfc.stop();
    cb.page(1);
  },

  // (K) SHOW WRITE QR PAGE
  hqNull : null, // html null token button
  qrShow : id => cb.load({
    page : "users/qr", target : "cb-page-2",
    data : { id : id },
    onload : () => {
      usr.hqNull = document.getElementById("qr-null");
      cb.page(2);
    }
  }),

  // (L) NULLIFY QR TOKEN
  qrNull : id => cb.api({
    mod : "session", act : "qrdel",
    data : { id : id },
    passmsg : "Login token nullified.",
    onpass : res => usr.hqNull.disabled = true
  }),

  // (M) IMPORT USERS
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

window.addEventListener("load", () => {
  usr.list();
  autocomplete.attach({
    target : document.getElementById("user-search"),
    mod : "autocomplete", act : "user",
    onpick : res => usr.search()
  });
});