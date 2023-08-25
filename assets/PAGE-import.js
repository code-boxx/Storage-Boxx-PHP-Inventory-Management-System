var im = {
  // (A) PROPERTIES
  // (A1) "IMPORT PROFILE", SEE (B) BELOW
  api : null,
  after : null,
  data : null,
  cols : null,

  // (A2) HTML ELEMENTS
  hSelect : null, // select file section
  hPick : null, // file picker
  hTable : null, // import section
  hList : null, // import list
  hBack : null, // back button
  hGo : null, // import go button

  // (B) LOAD IMPORT PAGE
  // name : item, user, etc...
  // at : load import page to cb-page-at
  // back : cb-page-back to return to
  // eg : example csv file
  // api : api to call - { mod : "module", act: "action" }
  // after : function to call after import
  // data : additional data to append, if any
  // cols : required columns
  //  [NAME, PARAMETER, CHECK]
  //  [NAME, PARAMETER, true] : must be defined
  //  [NAME, PARAMETER, false] : can be empty
  //  [NAME, PARAMETER, "N"] : must be number
  init : profile => {
    // (B1) "SET IMPORT SESSION"
    im.api = profile.api;
    im.after = profile.after;
    im.data = profile.data ? profile.data : null ;
    im.cols = profile.cols;

    // (B2) LOAD IMPORT PAGE
    let cols = [];
    for (let c of profile.cols) { cols.push(c[0]); }
    cb.page(profile.at);
    cb.load({
      page : "import",
      target : "cb-page-" + profile.at,
      data : {
        name : profile.name,
        back : profile.back,
        eg : profile.eg,
        cols : JSON.stringify(cols)
      },
      onload : () => {
        im.hSelect = document.getElementById("import-select");
        im.hPick = document.getElementById("import-file");
        im.hTable = document.getElementById("import-table");
        im.hList = document.getElementById("import-list");
        im.hBack = document.getElementById("import-back");
        im.hGo = document.getElementById("import-go");
      }
    });
  },

  // (C) READ CSV FILE
  read : () => {
    // (C1) SWITCH SECTION
    im.hSelect.classList.add("d-none");
    im.hTable.classList.remove("d-none");

    // (C2) FLAGS & HOLDERS
    let row, col, // table row & column
        clen = im.cols.length, // expected number of columns
        err, valid = false, // error & validity
        reader = new FileReader();

    // (C3) READ SELECTED CSV & GENERATE HTML TABLE
    reader.addEventListener("loadend", () => { try {
      // (C3-1) READ ROWS + CHECK VALID
      CSV.parse(reader.result).forEach(r => {
        // CREATE NEW ROW
        row = document.createElement("tr");

        // INVALID ROW
        if (r.length != clen) {
          row.className = "table-danger fw-bold";
          row.innerHTML = `<td colspan="${clen}">?</td><td>Invalid Row</td>`;
        }
        
        // CHECK ROW
        else {
          // DATA COLUMNS
          err = null;
          for (let i=0; i<clen; i++) {
            col = document.createElement("td");
            col.innerHTML = r[i];
            row.appendChild(col);
            if (err==null) {
              if (im.cols[i][2]=="N" && isNaN(r[i])) {
                err = `${im.cols[i][0]} - Invalid number`;
              }
              else if (im.cols[i][2]==true && r[i]==null) {
                err = `${im.cols[i][0]} - Cannot be empty`;
              }
            }
          }

          // STATUS COLUMN
          col = document.createElement("td");
          col.innerHTML = err==null ? "" : err ;
          row.appendChild(col);

          // VALID ROW?
          if (err==null) { row.className = "valid"; valid = true; }
          else { row.className = "table-danger fw-bold"; }
        }
        im.hList.appendChild(row);
      });

      // (C3-2) HAS AT LEAST ONE VALID ROW TO IMPORT
      if (valid) { im.hGo.disabled = false; }
    } catch (err) {
      cb.modal("Error opening CSV file", err.message)
      console.error(err);
    }});

    // (C4) GO!
    reader.readAsText(im.hPick.files[0]);
  },

  // (D) START IMPORT
  go : first => {
    // (D1) BLOCK SCREEN & DISABLE BUTTONS
    if (first) {
      im.hBack.disabled = true;
      im.hGo.disabled = true;
      cb.loading(true);
    }

    // (D2) IMPORT ENTRY
    let row = im.hList.querySelector(".valid");
    if (row!=null) {
      // (D2-1) DATA GATHER
      let data = {}, cols = row.querySelectorAll("td");
      for (let i=0; i<im.cols.length; i++) {
        data[im.cols[i][1]] = cols[i].innerHTML;
      }
      if (im.data != null) { Object.assign(data, im.data); }

      // (D2-2) SEND DATA TO API
      cb.api({
        mod : im.api.mod, act : im.api.act,
        passmsg : false, loading : false, nofail : true,
        data : data,
        onpass : () => {
          row.classList.remove("valid");
          cols[cols.length - 1].innerHTML = "OK";
          im.go();
        },
        onfail : msg => {
          row.classList.remove("valid");
          row.classList.add("table-danger");
          cols[cols.length - 1].innerHTML = msg;
          im.go();
        }
      });
    }

    // (D3) ALL DONE
    else {
      im.hBack.disabled = false;
      im.after();
      cb.loading(false);
      cb.modal("Done", "All entries has been imported.");
    }
  }
};