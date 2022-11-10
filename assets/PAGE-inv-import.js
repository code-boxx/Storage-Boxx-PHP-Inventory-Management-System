var iimport = {
  // (A) LOAD "IMPORT ITEMS" PAGE
  init : () => {
    cb.page(1);
    cb.load({
      page : "inventory/import",
      target : "cb-page-2"
    });
    return false;
  },

  // (B) READ CSV FILE
  read : () => {
    // (B1) HTML ELEMENTS
    let hSelect = document.getElementById("item-import-select"),
        hFile = document.getElementById("item-import-file"),
        hTable = document.getElementById("item-import-table"),
        hList = document.getElementById("item-import-list");
    hSelect.classList.add("d-none");
    hTable.classList.remove("d-none");

    // (B2) READ SELECTED FILE
    let reader = new FileReader(),
        csv = hFile.files[0], row, col, err, valid = false;

    reader.addEventListener("loadend", () => { try {
      // (B2-1) READ ROW-BY-ROW INTO HTML + CHECK VALID
      CSV.parse(reader.result).forEach(r => {
        row = document.createElement("tr");
        if (r.length != 5) {
          row.className = "table-danger fw-bold";
          row.innerHTML = `<td colspan="6">?</td><td>Invalid Row</td>`;
        } else {
          err = null;
          for (let i=0; i<5; i++) {
            if (r[i]==null) { err = "Missing Data"; }
            col = document.createElement("td");
            col.innerHTML = r[i]==null?"":r[i];
            row.appendChild(col);
          }
          col = document.createElement("td");
          col.innerHTML = err==null ? "" : err;
          row.appendChild(col);
          if (err==null) {
            row.className = "valid";
            valid = true;
          } else { row.className = "table-danger fw-bold"; }
        }
        hList.appendChild(row);
      });

      // (B2-2) START BUTTON
      if (valid) {
        row = document.createElement("tr");
        row.innerHTML = `<td colspan="6">
          <button id="item-import-go" class="btn btn-primary" onclick="iimport.go(1)">Start Import</button>
        </td>`;
        hList.appendChild(row);
      }
    } catch (err) {
      cb.modal("Error opening CSV file", err.message)
      console.error(err);
    }});
    reader.readAsText(csv);
  },

  // (C) START IMPORT
  go : first => {
    // (C1) BLOCK SCREEN & DISABLE BUTTON ON INIT CALL
    if (first) {
      document.getElementById("item-import-go").disabled = true;
      cb.loading(true);
    }

    // (C2) IMPORT ENTRY
    let row = document.querySelector("#item-import-list .valid");
    if (row!=null) {
      let col = row.querySelectorAll("td");
      cb.api({
        mod : "inventory", req : "import",
        passmsg : false, loading : false, nofail : true,
        data : {
          sku : col[0].innerHTML,
          name : col[1].innerHTML,
          desc : col[2].innerHTML,
          unit : col[3].innerHTML,
          low : col[4].innerHTML
        },
        onpass : () => {
          row.classList.remove("valid");
          col[5].innerHTML = "OK";
          iimport.go();
        },
        onfail : msg => {
          row.classList.remove("valid");
          row.classList.add("table-danger");
          col[5].innerHTML = msg;
          iimport.go();
        }
      });
    }

    // (C3) ALL DONE
    else {
      let btn = document.getElementById("item-import-go");
      btn.innerHTML = "Done - Go Back";
      btn.onclick = () => cb.page(0);
      btn.disabled = false;
      inv.list(true);
      cb.loading(false);
    }
  }
};