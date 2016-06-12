<?php
	header(W2Web::headerStringOfCode(503));

    if (!isset($title))
    {
        $title = '出现了一些状况。';
    }
    if (!isset($message))
    {
        $message = '您看到了这个页面，是因为服务器在处理页面时出现了一些小小的问题，请稍后再试。';
    }
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf8"/>
    <meta name="viewport" content="width=768; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
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
