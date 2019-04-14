<?php
if (isset($_SERVER['HTTPS'])) {
    $http = '//';
} else {
    $http = 'http://';
}
$domain = $_SERVER['SERVER_NAME'];

$url = $http.$domain;
$private = $url. '/private/';

// 定数などの定義
require dirname(__DIR__). '\common\InitFunction.php';
require dirname(__DIR__). '\common\Word\Message.php';

$Agent = $_SERVER['HTTP_USER_AGENT'];
if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
function GetSelf_Admin() {
    return $_SERVER['PHP_SELF'];
}

function GetPrevPage_Admin() {
    return filter_input(INPUT_SERVER, 'HTTP_REFERER');
}


function getURI_Admin() {
    return $_SERVER['REQUEST_URI'];
}
