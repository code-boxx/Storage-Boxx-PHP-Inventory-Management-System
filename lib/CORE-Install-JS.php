<style>.notes,.notes *{font-size:16px;padding:3px;color:#8b8b8b}</style>
<script>
var install = {
  // (A) HELPER - AJAX FETCH
  ajax : (url, phase, after) => {
    // (A1) FORM DATA
    let data = new FormData(document.getElementById("iForm"));
    data.append("phase", phase);

    // (A2) AJAX FETCH
    fetch(url, { method:"POST", body:data })
    .then(res => {
      if (res.status==200) { return res.text(); }
      else {
        console.error(res);
        let err = "SERVER ERROR " + res.status;
        if (res.status==404) { err += " - Is the host URL correct? Is 'AllowOverride All' set in Apache?`"; }
        alert(err);
        install.toggle(true);
      }
    })
    .then(txt => {
      if (txt=="OK") { after(); }
      else if (txt!=undefined) {
        alert(txt);
        install.toggle(true);
      }
    })
    .catch(err => {
      alert(`Fetch error - ${err.message}`);
      install.toggle(true);
      console.error(err);
    });
  },

  // (B) LOCK/UNLOCK INSTALL FORM
  toggle : enable => {
    if (enable) {
      document.getElementById("gobtn").disabled = false;
      document.getElementById("iForm").onsubmit = install.go;
    } else {
      document.getElementById("gobtn").disabled = true;
      document.getElementById("iForm").onsubmit = false;
    }
  },

  // (C) RANDOM JWT KEY GENERATOR
  // CREDITS : https://stackoverflow.com/questions/1349404/generate-random-string-characters-in-javascript
  rnd : () => {
    var result = "";
    var characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789~!-_=.";
    var charactersLength = characters.length;
    for ( var i = 0; i < 48; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    document.getElementsByName("jwtkey")[0].value = result;
  },

  // (D) TOGGLE CORS
  cors : allowed => {
    let more = document.getElementById("corsmore");
    if (allowed==1) { more.classList.remove("d-none"); }
    else { more.classList.add("d-none"); }
  },

  // (E) INSTALL GO
  go : () => {
    // (E1) LOCK INSTALL FORM
    install.toggle(false);

    <?php if (I_USER) { ?>
    // (E2) ADMIN PASSWORD
    var pass = document.getElementsByName("apass")[0],
        cpass = document.getElementsByName("apassc")[0];
    if (pass.value != cpass.value) {
      alert("Admin passwords do not match!");
      install.toggle(true);
      return false;
    }

    // (E3) PASSWORD STRENGTH CHECK - AT LEAST 8 CHARACTERS ALPHANUMERIC
    if (!/^(?=.*[0-9])(?=.*[A-Z]).{8,20}$/i.test(pass.value)) {
      alert("Password must be at least 8 characters alphanumeric");
      install.toggle(true);
      return false;
    }
    <?php } ?>

    // (E4) URL PATH
    let url = (document.getElementsByName("https")[0].value=="0" ? "http" : "https")
            + "://" + document.getElementsByName("host")[0].value;

    // (E5) GENERATE HTACCESS + VERIFY HTACCESS + INSTALL
    install.ajax(url, "E", () => install.ajax(url + "COREVERIFY", "F", () => {
      alert("Installation complete, this page will now reload.");
      location.href = url;
    }));
    return false;
  }
};

// (F) GENERATE RANDOM JWT KEY + ENABLE INSTALL FORM ON WINDOW LOAD
window.onload = () => {
  install.rnd();
  install.toggle(true);
};
</script>
