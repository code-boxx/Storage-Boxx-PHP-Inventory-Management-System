<?php
class Mail extends Core {
  // (A) SEND HTML EMAIL
  // $mail : array, email to send
  //  to : email string, or an array of email strings
  //  cc : email string, or an array of email strings (optional)
  //  bcc : email string, or an array of email strings (optional)
  //  from : email string (optional)
  //  attach : file (string) or files (array) to attach (optional)
  //  subject : subject of email
  //  body : email body
  //  template : email template file (has precedence over body)
  //  vars : array of variables for template
  function send ($mail) {
    // (A1) CHECKS
    if (!isset($mail["to"]) || !isset($mail["subject"]) ||
       (!isset($mail["body"]) && !isset($mail["template"]))) {
      $this->error = "Please set to, subject, body (or template).";
      return false;
    }

    // (A2) ATTACHMENT CHECK
    if (isset($mail["attach"])) {
      if (!is_array($mail["attach"])) { $mail["attach"] = [$mail["attach"]]; }
      foreach ($mail["attach"] as $f) { if (!file_exists($f)) {
        $this->error = "$f does not exist!";
        return false;
      }}
    }

    // (A3) TEMPLATE FILE CHECK
    if (isset($mail["template"]) && !file_exists($mail["template"])) {
      $this->error = "Template ". $mail["template"] ." does not exist!";
      return false;
    }

    // (A4) BUILD MAIL HEADERS
    $boundary = isset($mail["attach"]) ? md5(time()) : null ;
    $headers = [
      "MIME-Version: 1.0",
      "Content-type: " . (isset($mail["attach"])
        ? "multipart/mixed; boundary=\"$boundary\""
        : "text/html; charset=utf-8"),
      "From: " . (isset($mail["from"]) ? $mail["from"] : EMAIL_FROM)
    ];
    if (isset($mail["cc"])) {
      $headers[] = "Cc: " . (is_array($mail["cc"]) ? implode(", ", $mail["cc"]) : $mail["cc"]);
    }
    if (isset($mail["bcc"])) {
      $headers[] = "Bcc: " . (is_array($mail["bcc"]) ? implode(", ", $mail["bcc"]) : $mail["bcc"]);
    }
    $headers = implode("\r\n", $headers);

    // (A5) BUILD TEMPLATE
    if (isset($mail["template"])) {
      $mail["body"] = $this->template(
        $mail["template"], is_array($mail["vars"]) ? $mail["vars"] : null
      );
    }

    // (A6) ADD ATTACHMENT(S)
    if (isset($mail["attach"])) {
      // (A6-1) MAIL MESSAGE
      $mail["body"] = implode("\r\n", [
        "--$boundary",
        "Content-type: text/html; charset=utf-8",
        "", $mail["body"]
      ]);

      // (A6-2) MAIL ATTACHMENTS
      $attachments = count($mail["attach"]) - 1;
      for ($i=0; $i<=$attachments; $i++) {
        $mail["body"] .= implode("\r\n", [
          "", "--$boundary",
          "Content-Type: ".mime_content_type($mail["attach"][$i])."; name=\"".basename($mail["attach"][$i])."\"",
          "Content-Transfer-Encoding: base64",
          "Content-Disposition: attachment",
          "", chunk_split(base64_encode(file_get_contents($mail["attach"][$i]))),
          "--$boundary"
        ]);
        if ($i==$attachments) { $mail["body"] .= "--"; }
      }
    }

    // (A7) MAIL SEND
    if (is_array($mail["to"])) { $mail["to"] = implode(", ", $mail["to"]); }
    if (@mail($mail["to"], $mail["subject"], $mail["body"], $headers)) { return true; }
    else {
      $this->error = "Error sending mail";
      return false;
    }
  }

  // (B) LOAD TEMPLATE
  function template ($file, $vars=null) {
    ob_start();
    if ($vars!==null) { extract($vars); }
    include $file;
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }
}