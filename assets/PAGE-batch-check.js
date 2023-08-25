var check = {
  // (A) LOAD MOVEMENT HISTORY "MAIN PAGE"
  sku : null, batch : null, // current sku & batch
  pg : 1, // current page
  go : (sku, batch) => {
    // (A1) SET FLAGS
    check.sku = sku;
    check.batch = batch;
    check.pg = 1;

    // (A2) AJAX LOAD "MAIN PAGE"
    cb.load({
      page : "check-main", target : "cb-page-2",
      data : {
        sku : check.sku,
        batch : check.batch
      },
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
      batch : check.batch,
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