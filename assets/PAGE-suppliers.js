var sup = {
  // (A) LIST () : SHOW ALL SUPPLIERS
  pg : 1, // current page
  find : "", // current search
  list : silent => {
    if (silent!==true) { cb.page(1); }
    cb.load({
      page : "suppliers/list", target : "sup-list",
      data : {
        page : sup.pg,
        search : sup.find
      }
    });
  },

  // (B) GO TO PAGE
  //  pg : page number
  goToPage : pg => { if (pg!=sup.pg) {
    sup.pg = pg;
    sup.list();
  }},

  // (C) SEARCH SUPPLIERS
  search : () => {
    sup.find = document.getElementById("sup-search").value;
    sup.pg = 1;
    sup.list();
    return false;
  },

  // (D) SHOW ADD/EDIT DOCKET
  //  id : supplier id, for edit only
  addEdit : id => cb.load({
    page : "suppliers/form", target : "cb-page-2",
    data : { id : id ? id : "" },
    onload : () => cb.page(2)
  }),

  // (E) SAVE SUPPLIER
  save : () => {
    // (E1) GET DATA
    var data = {
      name : document.getElementById("sup-name").value,
      tel : document.getElementById("sup-tel").value,
      email : document.getElementById("sup-email").value,
      addr : document.getElementById("sup-address").value
    };
    var id = document.getElementById("sup-id").value;
    if (id!="") { data.id = id; }

    // (E2) AJAX
    cb.api({
      mod : "suppliers", act : "save",
      data : data,
      passmsg : "Supplier saved",
      onpass : sup.list
    });
    return false;
  },

  // (F) DELETE SUPPLIER
  //  id : supplier id
  del : id => cb.modal("Please confirm", `Delete supplier? This supplier and its items will be lost!`, () => cb.api({
    mod : "suppliers", act : "del",
    data : { id : id },
    passmsg : "Supplier deleted",
    onpass : sup.list
  })),

  // (G) SUPPLIER ITEMS CSV DOWNLOAD
  csv : id => {
    document.getElementById("sup-csv-id").value = id;
    document.getElementById("sup-csv").submit();
  }
};
window.addEventListener("load", sup.list);