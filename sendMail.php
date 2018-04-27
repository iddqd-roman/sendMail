<?php

function sendMail($c) {
    $content_type = isset($c['content_type']) ? $c['content_type'] : 'text/plain';
    if(isset($c['files']) && is_array($c['files']) && count($c['files']) > 0){
        $uid = md5(uniqid(time()));
        $header = "From: ".$c['from_name']." <".$c['from_email'].">\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
        
        $mp = "--".$uid."\r\n";
        $mp .= "Content-Type: ".$content_type."; charset=utf-8\r\n";
        $mp .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $mp .= $c['message']."\r\n\r\n";
        $mp .= "--".$uid."\r\n";
        
        foreach($c['files'] as $file){
            $file_size = filesize($file['location']);
            $handle = fopen($file['location'], "r");
            $content = fread($handle, $file_size);
            fclose($handle);
            $content = chunk_split(base64_encode($content));
            $uid = md5(uniqid(time()));
            $filename = !empty($file['new_filename']) ? 
                $file['new_filename'] : basename($file['location']);
            
            $mp .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n";
            $mp .= "Content-Transfer-Encoding: base64\r\n";
            $mp .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
            $mp .= $content."\r\n\r\n";
            $mp .= "--".$uid . "\r\n";
        }
        
        return mail($c['to_email'], $c['subject'], $mp, $header);
    }
    $c['from_name'] = "=?UTF-8?B?".base64_encode($c['from_name'])."?=";
    $c['subject'] = "=?UTF-8?B?".base64_encode($c['subject'])."?=";
    $headers = "From: " . $c['from_name'] . " <" . $c['from_email'] . ">\r\n" .
        "MIME-Version: 1.0" . "\r\n" .
        "Content-Type: ".$content_type."; charset=UTF-8" . "\r\n";
    return mail($c['to_email'], $c['subject'], $c['message'], $headers);
}
