<?php

// Standard page begin
function html_page_begin($title) {
        return <<<_END
<!DOCTYPE html>
<html>
<head>
<title>$title</title>
<meta charset="utf-8" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link rel="icon" href="favicon.png" type="image/png">
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<center>
<h1>$title</h1>

_END;
}

// Page end, scripts and footer
function html_page_end() {
        return <<<_END
<hr width=10%>
<p>Nichash-to-currency converter by sau412</p>
</center>
</body>
</html>

_END;
}

// Global message
function html_message_global() {
        $result="";

        $global_message=get_variable("global_message");
        if($global_message!='') {
                $result.="<div class='message_global'>$global_message</div>";
        }

        return $result;
}

function html_message($message) {
        return "<div style='background:yellow;'>".html_escape($message)."</div>";
}

function html_address_url($address) {
        global $address_url;
        $address_begin=substr($address,0,10);
        $address_end=substr($address,-10,10);
        //$result="<div class='url_with_qr_container'><a href='$address_url$address'>$address_begin......$address_end</a>, <a href='#'>copy</a>, $send_to_link, $address_book_link<br><img src='qr.php?str=$address'></div></div>";
        $result="<div class='url_with_qr_container'>$address_begin......$address_end <div class='qr'>$address<br><a href='$address_url$address'>block explorer</a></div></div>";
        return $result;
}

function html_tx_url($tx) {
        global $tx_url;
        if($tx=='') return '';
        $tx_begin=substr($tx,0,10);
        $tx_end=substr($tx,-10,10);
        $result="<div class='url_with_qr_container'>$tx_begin......$tx_end <div class='qr'>$tx<br><a href='$tx_url$tx'>block explorer</a></div></div>";
        return $result;
}

?>
