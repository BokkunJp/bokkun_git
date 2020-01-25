<?php
function SendMail($header, $response=false)
{
    if (!isset($header)) {
       return false;
    }
    list($to, $title, $body, $addtional_headers, $addtional_parameter) = $header;

    // 差出人名文字化け回避用
    // $from_encoded_name = mb_encode_mimeheader($from_encoded_name, 'ISO-2022-JP-MS');

    if (!isset($to) || empty($to)) {
        echo '入力が不正です。';
        return false;
    }

    if (!isset($title) || empty($title)) {
        $title = '';
    }

        if (!isset($body) || empty($body)) {
        $body = '';
    }

    if (!isset($addtional_headers) || empty($addtional_headers)) {
        $addtional_headers = '';
    }

    if (!isset($addtional_parameter) || empty($addtional_parameter)) {
        $addtional_parameter = '';
    }

    if (mb_send_mail($to, $title, $body, $addtional_headers, $addtional_parameter)) {
        $message = 'メールを送信しました。';
    } else {
      echo 'メールの送信に失敗しました。';
    }

}
