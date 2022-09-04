var check = {
  // (A) LOAD MOVEMENT HISTORY "MAIN PAGE"
  //  sku : string, item sku
  load : sku => {
    cb.load({
      page : "icheck", target : "cb-page-2",
      data : { sku : sku },
      onload : () => {
        check.sku = sku;
        check.pg = 1;
        cb.page(1);
        check.list();
      }
    });
  },

  // (B) SHOW ITEM MOVEMENT HISTORY
  sku : null, // current item
  pg : 1, // current page
  list : () => {
    cb.load({
      page : "icheck/list", target : "i-history",
      data : {
        sku : check.sku,
        page : check.pg
      }
    });
  },

  // (C) GO TO PAGE
  //  pg : int, page number
  goToPage : pg => { if (pg!=check.pg) {
    check.pg = pg;
    check.list();
  }},

  // (D) VERIFY VALID SKU BEFORE SHOW HISTORY
  verify : () => {
    var field = document.getElementById("mvt-check");
    cb.api({
      mod : "inventory", req : "get",
      data : { sku : field.value },
      passmsg : false,
      onpass : (res) => {
        if (res.data===null) {
          cb.modal("Invalid Item", "SKU is not found in database.");
        } else {
          check.load(field.value);
          field.value = "";
        }
      }
    });
    return false;
  }
};