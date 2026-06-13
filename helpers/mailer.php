<?php
function send_admin_email($subject, $body){
    $admin_email = 'admin@example.com';
    if(function_exists('mail')){
        $headers = 'From: no-reply@desaposi.local' . "\r\n" . 'Content-Type: text/plain; charset=utf-8';
        @mail($admin_email, $subject, $body, $headers);
        return true;
    }
    return false;
}
?>