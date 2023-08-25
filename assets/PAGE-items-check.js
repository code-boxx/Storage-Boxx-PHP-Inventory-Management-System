var check = {
  // (A) LOAD MOVEMENT HISTORY "MAIN PAGE"
  sku : null, // current sku
  pg : 1, // current page
  go : sku => {
    // (A1) SET FLAGS
    check.sku = sku;
    check.pg = 1;

    // (A2) AJAX LOAD "MAIN PAGE"
    cb.load({
      page : "check-main", target : "cb-page-2",
      data : { sku : check.sku },
      onload : () => {
        cb.page(2);
        check.list();
      }
    });
  },

  // (B) SHOW ITEM MOVEMENT HISTORY
  list : () => cb.load({
    page : "check/list", target : "check-list",
    data : {
      sku : check.sku,
      page : check.pg
    }
  }),

  // (C) GO TO PAGE
  //  pg : int, page number
  goToPage : pg => { if (pg!=check.pg) {
    check.pg = pg;
    check.list();
  }}
};