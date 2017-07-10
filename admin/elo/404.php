<?php
    header(W2Web::headerStringOfCode(404));

    if (!isset($title))
    {
        $title = '页面不存在';
    }
    if (!isset($message))
    {
        $message = '您看到了这个页面，是因为这个页面并不存在，请前往<a href="/">首页</a>看看呗。';
    }
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf8"/>
    <meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
    <title><?= $title ?></title>
</head>
<body>
    <table align="center" height="100%">
        <tr>
            <td align="center">
                <p><?= $message ?></p>
            </td>
        </tr>
    </table>
</body>
</html>
