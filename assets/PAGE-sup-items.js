var items = {
  // (A) LOAD SUPPLIER ITEMS PAGE
  pg : 1, // current page
  find : "", // current search
  id : null, // current supplier id
  init : id => {
    items.pg = 1;
    items.find = "";
    items.id = id;
    cb.page(2);
    cb.load({
      page : "sup/items",
      target : "cb-page-2",
      data : { id : id },
      onload : () => items.list()
    });
  },

  // (B) LIST : SHOW SUPPLIER ITEMS
  list : silent => {
    if (silent!==true) { cb.page(2); }
    cb.load({
      page : "sup/items/list", target : "item-list",
      data : {
        id : items.id,
        page : items.pg,
        search : items.find
      },
      onload : () => autocomplete.attach({
        target : document.getElementById("item-search"),
        mod : "autocomplete", act : "supitem",
        data : { sid : items.id },
        onpick : items.search
      })
    });
  },

  // (C) GO TO PAGE
  //  pg : page number
  goToPage : pg => { if (pg!=items.pg) {
    items.pg = pg;
    items.list();
  }},

  // (D) SEARCH ITEMS
  search : () => {
    items.find = document.getElementById("item-search").value;
    items.pg = 1;
    items.list();
    return false;
  },

  // (E) SHOW ADD/EDIT DOCKET
  //  id : item sku, for edit only
  addEdit : sku => cb.load({
    page : "sup/items/form", target : "cb-page-3",
    data : {
      sku : sku ? sku : "",
      id : items.id
    },
    onload : () => {
      cb.page(3);
      autocomplete.attach({
        target : document.getElementById("item-sku"),
        mod : "autocomplete", act : "sku"
      });
    }
  }),

  // (F) SAVE ITEM
  save : () => {
    // (F1) AUTO SUPPLIER SKU
    var sku = document.getElementById("item-sku"),
        ssku = document.getElementById("item-ssku"),
        osku = document.getElementById("item-osku");
    if (ssku.value=="") { ssku.value = sku.value; }

    // (F2) GET FORM DATA
    var data = {
      id : items.id,
      sku : sku.value,
      ssku : ssku.value,
      price : document.getElementById("item-price").value
    };
    if (osku.value!="") { data.osku = osku.value; }

    // (F3) AJAX
    cb.api({
      mod : "suppliers", act : "saveItem",
      data : data,
      passmsg : "Supplier item saved",
      onpass : items.list
    });
    return false;
  },

  // (G) DELETE ITEM
  //  sku : item sku
  del : sku => cb.modal(
    `<i class="icon icon-warning"></i> Remove Item?`, 
    `<strong class="text-danger">Item will also be removed from all related PO and movement history.</strong>`, () => cb.api({
    mod : "suppliers", act : "delItem",
    data : { id : items.id, sku : sku },
    passmsg : "Supplier item deleted",
    onpass : items.list
  })),

  // (H) IMPORT SUPPLIER ITEMS
  import : () => im.init({
    name : "SUPPLIER ITEMS",
    at : 3, back : 2,
    eg : "dummy-supplier-items.csv",
    api : { mod : "suppliers", act : "importItem" },
    after : () => items.list(true),
    data : { id : items.id },
    cols : [
      ["SKU", "sku", true],
      ["Supplier SKU", "ssku", false],
      ["Price", "price", "N"]
    ]
  })
};