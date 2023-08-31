<?php
// (A) PAGE META
$_PMETA = ["load" => [
  ["l", HOST_ASSETS."PAGE-ai.css"],
  ["s", HOST_ASSETS."PAGE-ai.js", "defer"],
  ["s", HOST_ASSETS."PAGE-ai-sr.js", "defer"]
]];

// (B) HTML PAGE
require PATH_PAGES . "TEMPLATE-top.php"; ?>
<div id="ai-wrap">
  <!-- (B1) CHAT HISTORY -->
  <div id="ai-chat"></div>

  <!-- (B2) QUERY -->
  <form id="ai-query" class="d-flex align-items-stretch head border p-2 w-100" onsubmit="return chat.send()">
    <input type="text" id="ai-txt" placeholder="Question" 
           class="form-control form-control-sm" autocomplete="off" required disabled>
    <button type="button" id="ai-sr" class="btn btn-primary p-3 ms-1 ico-sm icon-mic d-none" disabled></button>
    <button type="submit" id="ai-go" class="btn btn-primary p-3 ms-1 ico-sm icon-play3" disabled></button>
  </form>
</div>
<?php require PATH_PAGES . "TEMPLATE-bottom.php"; ?>