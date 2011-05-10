<?php

/**
 * TODO: [Security] Don't allow referer-less requests.
 */
if(!$_SERVER['QUERY_STRING']) { // || $_SERVER['HTTP_REFERER'] {
    header("Content-Type: text/plain");
    header("HTTP/1.1 400 Bad Request");
    echo "A bad request was made to the server.";
    exit();
}

$res = explode(',', $_SERVER['QUERY_STRING']);
$type = $res[0];
$file = $res[1];

$mime = null;


switch($type) {
    case "css":
        if(file_exists("../resources/".urlencode($res[1]).".css")) {
            $mime = "text/css";
            // TODO: [FR] Update expires header to last edit date
            $expires = "Expires: Fri, 30 Oct 1998 14:19:41 GMT";
            $output = file_get_contents("../resources/".urlencode($res[1]).".css");
        }
        break;
     case "png":
        if(file_exists("../resources/".urlencode($res[1]).".png")) {
            $mime = "image/png";
            // TODO: [FR] Update expires header to last edit date
            $expires = "Expires: Sat, 1 Jan 2000 00:00:01 GMT";
            $output = file_get_contents("../resources/".urlencode($res[1]).".png");
        }
        break;
    case "js":
        if(file_exists("../resources/".urlencode($res[1]).".js")) {
            $mime = "text/javascript; charset=UTF-8";
            // TODO: [FR] Update expires header to last edit date
            $expires = "Expires: Fri, 30 Oct 1998 14:19:41 GMT";
            $output = file_get_contents("../resources/".urlencode($res[1]).".js");
        }
        break;
}

if ($mime) {
    header("Content-Type: ".$mime);
    header($expires);
    echo $output;
} else {
    header("HTTP/1.1 404 File Not Found");
    echo "The file was not found.";
    exit();
}


?>
