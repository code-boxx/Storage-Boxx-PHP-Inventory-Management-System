<?php
class Mail {
  // (A) SEND () : SEND EMAIL
  //  $to : recipients, string or array of emails.
  //  $subject : email subject
  //  $body : email message, HTML OK.
  //  $cc : optional cc, string or array of emails.
  //  $bcc : optional bcc, string or array of emails.
  function send ($to, $subject, $body, $cc=null, $bcc=null) {
    // (A1) BUILD PARTIAL HEADERS
    $headers = [
      "MIME-Version: 1.0",
      "Content-type: text/html; charset=utf-8",
      "From: " . EMAIL_FROM
    ];

    // (A2) ADD CC + BCC
    if ($cc != null) {
      $headers[] = "Cc: " . (is_array($cc) ? implode(", ", $cc) : $cc);
    }
    if ($bcc != null) {
      $headers[] = "Bcc: " . (is_array($bcc) ? implode(", ", $bcc) : $bcc);
    }
    
    // (A3) SEND!
    return @mail(
      is_array($to) ? implode(", ", $to) : $to, 
      $subject, $body, implode("\r\n", $headers)
    );
  }
}