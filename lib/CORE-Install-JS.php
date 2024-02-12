<script>
var install = {
  // (A) HELPER - AJAX FETCH
  ajax : (url, phase, after) => {
    // (A1) FORM DATA
    let data = new FormData(document.getElementById("iForm"));
    data.append("phase", phase);

    // (A2) AJAX FETCH
    fetch(url, { method:"POST", body:data })
    .then(async res => {
      if (res.status==200) { return res.text(); }
      else {
        install.toggle(true);
        console.error(await res.text());
        let err = "SERVER ERROR - " + res.status
        if (res.status==404) { err += ". Please make sure that the host URL is correct, AllowOverride is properly set in Apache."; }
        alert(err);
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
      console.error(err);
      install.toggle(true);
      alert(`Fetch error - ${err.message}`);
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

    // (E2) URL DUMMY PROOFING TO THE MAX!
    let hHost = document.getElementsByName("host")[0],
        domain = hHost.value;
    domain = domain.trim();
    domain = domain.replaceAll("\\", "/");
    domain = domain.replace(/\/\/+/g, "/");
    if (domain.charAt(0) == "/") { domain = domain.substring(1); }
    if (domain.charAt(domain.length-1) != "/") { domain = domain + "/"; }
    hHost.value = domain;

    // (E3) DUMMY PROOFING - API ENFORCE HTTPS BUT HOST URL IS NOT HTTPS
    let https = document.getElementsByName("https")[0].value,
        enforce = document.getElementsByName("apihttps")[0].value;
    if (enforce==1 && https==0) {
      alert("Please set your HOST URL to HTTPS if you want to enforce HTTPS for API calls. Also make sure that you have a valid SSL cert.");
      install.toggle(true);
      return false;
    }

    <?php if (I_USER) { ?>
    // (E4) ADMIN PASSWORD
    var pass = document.getElementsByName("apass")[0],
        cpass = document.getElementsByName("apassc")[0];
    if (pass.value != cpass.value) {
      alert("Admin passwords do not match!");
      install.toggle(true);
      return false;
    }

    // (E5) PASSWORD STRENGTH CHECK - AT LEAST 8 CHARACTERS ALPHANUMERIC
    if (!/^(?=.*[0-9])(?=.*[A-Z]).{8,20}$/i.test(pass.value)) {
      alert("Password must be at least 8 characters alphanumeric");
      install.toggle(true);
      return false;
    }
    <?php } ?>

    // (E6) URL PATH
    let url = (document.getElementsByName("https")[0].value=="0" ? "http" : "https")
            + "://" + domain ;

    // (E7) GENERATE HTACCESS
    install.ajax(url, "E1", () => {
      // (E8) VERIFY HTACCESS
      install.ajax(url + "installer/test/", "E2", () => {
        // (E9) PROCEED INSTALLATION
        install.ajax(url, "F", () => install.celebrate(url));
      });
    });
    return false;
  },

  // (F) INSTALLATION COMPLETE - CELEBRATE!
  celebrate : url => {
    document.getElementById("iForm").innerHTML = "";
    document.getElementById("iDone").href = url;
    document.getElementById("iCelebrate").classList.remove("d-none");
    confetti({
      particleCount: 100,
      spread: 70,
      origin: { y: 0.6 },
    });
  }
};

// (G) GENERATE RANDOM JWT KEY + ENABLE INSTALL FORM ON WINDOW LOAD
window.onload = () => {
  install.rnd();
  install.toggle(true);
};
</script>