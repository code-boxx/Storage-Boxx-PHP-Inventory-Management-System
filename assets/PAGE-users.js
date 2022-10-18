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
  addEdit : id => cb.load({
    page : "users/form", target : "cb-page-2",
    data : { id : id ? id : "" },
    onload : () => cb.page(1)
  }),

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
      mod : "users", req : "save", data : data,
      passmsg : "User Saved",
      onpass : usr.list
    });
    return false;
  },

  // (F) DELETE USER
  //  id : int, user ID
  //  confirm : boolean, confirmed delete
  del : id => cb.modal("Please confirm", "Delete user?", () => cb.api({
    mod : "users", req : "del",
    data : { id: id },
    passmsg : "User Deleted",
    onpass : usr.list
  })),

  // (G) SHOW WRITE NFC PAGE
  nfcShow : id => cb.load({
    page : "users/nfc", target : "cb-page-2",
    data : { id : id },
    onload : () => {
      if ("NDEFReader" in window) {
        document.getElementById("nfc-stat").onclick = () => usr.nfcNew(id);
        usr.nfcLog(1, `Click here to create a new token`);
      } else {
        usr.nfcLog(0, "Web NFC is not supported in your browser/device.");
      }
      cb.page(1);
    }
  }),

  // (H) SHOW "WRITE NFC TAG" STATUS ON SCREEN
  nfcLog : (status, msg) => {
    let hLog = document.getElementById("nfc-stat");
    if (status == 1) {
      hLog.classList.remove("bg-danger");
      hLog.classList.add("bg-success");
    } else {
      hLog.classList.remove("bg-success");
      hLog.classList.add("bg-danger");
    }
    hLog.value = msg;
  },

  // (I) CREATE NEW NFC LOGIN TAG
  nfcNew : id => {
    cb.api({
      mod : "users", req : "token",
      data : { id : id },
      passmsg : false,
      onpass : res => {
        document.getElementById("nfc-stat").onclick = "";
        document.getElementById("nfc-null").disabled = false;
        nfc.onwrite = () => {
          usr.nfcLog(1, `Done - Login token created`);
          nfc.standby();
        };
        nfc.onerror = err => {
          console.error(err);
          usr.nfcLog(0, "ERROR - " + err.message);
          nfc.stop();
        };
        nfc.write(res.data);
        usr.nfcLog(1, `Ready - Tap to write NFC token`);
      }
    });
  },

  // (J) NULLIFY NFC TOKEN
  nfcNull : id => {
    cb.api({
      mod : "users", req : "notoken",
      data : { id : id },
      passmsg : "Login token nullified.",
      onpass : res => document.getElementById("nfc-null").disabled = true
    });
  },

  // (K) END WRITE NFC SESSION
  nfcBack : () => {
    nfc.stop();
    cb.page(0);
  }
};
window.addEventListener("load", usr.list);