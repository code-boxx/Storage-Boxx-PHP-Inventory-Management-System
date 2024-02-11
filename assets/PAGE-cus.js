var cus = {
  // (A) SHOW ALL CUSTOMERS
  pg : 1, // current page
  find : "", // current search
  list : silent => {
    if (silent!==true) { cb.page(1); }
    cb.load({
      page : "cus/list", target : "cus-list",
      data : {
        page : cus.pg,
        search : cus.find
      }
    });
  },

  // (B) GO TO PAGE
  //  pg : page number
  goToPage : pg => { if (pg!=cus.pg) {
    cus.pg = pg;
    cus.list();
  }},

  // (C) SEARCH CUSTOMERS
  search : () => {
    cus.find = document.getElementById("cus-search").value;
    cus.pg = 1;
    cus.list();
    return false;
  },

  // (D) SHOW ADD/EDIT DOCKET
  //  id : customer id, for edit only
  addEdit : id => cb.load({
    page : "cus/form", target : "cb-page-2",
    data : { id : id ? id : "" },
    onload : () => cb.page(2)
  }),

  // (E) SAVE CUSTOMER
  save : () => {
    // (E1) GET DATA
    var data = {
      name : document.getElementById("cus-name").value,
      tel : document.getElementById("cus-tel").value,
      email : document.getElementById("cus-email").value,
      addr : document.getElementById("cus-address").value
    };
    var id = document.getElementById("cus-id").value;
    if (id!="") { data.id = id; }

    // (E2) AJAX
    cb.api({
      mod : "customers", act : "save",
      data : data,
      passmsg : "Customer saved",
      onpass : cus.list
    });
    return false;
  },

  // (F) DELETE CUSTOMER
  //  id : customer id
  del : id => cb.modal(
    `<i class="icon icon-warning"></i> Delete Customer?`,
    `<strong class="text-danger">This customer will be removed. All related orders and movement history will be deleted as well.</strong>`,
    () => cb.api({
      mod : "customers", act : "del",
      data : { id : id },
      passmsg : "Customer deleted",
      onpass : cus.list
    })
  ),

  // (H) IMPORT CUSTOMERS
  import : () => im.init({
    name : "CUSTOMERS",
    at : 2, back : 1,
    eg : "dummy-customers.csv",
    api : { mod : "customers", act : "import" },
    after : () => cus.list(true),
    cols : [
      ["Name", "name", true],
      ["Tel", "tel", true],
      ["Email", "email", true],
      ["Address", "addr", false]
    ]
  })
};

// (I) PAGE INIT
window.addEventListener("load", () => {
  // (I1) DRAW LIST
  cus.list();

  // (I2) ATTACH AUTOCOMPLETE
  autocomplete.attach({
    target : document.getElementById("cus-search"),
    mod : "autocomplete", act : "cus",
    onpick : res => cus.search()
  });
});