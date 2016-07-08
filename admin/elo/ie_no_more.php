<?php
    header('HTTP/1.0 500 Internal Server Error');
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf8"/>
    <meta name="viewport" content="width=320; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
    <title>为了更好的体验，请使用更先进的浏览器。</title>
</head>
<body>
    <table align="center" height="100%">
        <tr>
            <td align="left">
                <?php
                    if (strpos($_SERVER['HTTP_USER_AGENT'],'MSIE ') !==false && (strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 6.") || strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 7.")  || strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 8.") || strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 9.")))
                    {
                        echo '<h2>您的浏览器有点老，请升级！</h2>';
                        echo '<p>检测到您的浏览器是9.0版以下的IE，它太古老了，既不太安全，浏览速度也很缓慢，更不能很好体验我们为您定制的各项服务，建议您使用更先进的浏览器。</p>';
                        echo '<p>如您使用的是360，搜狗等双核浏览器，也可以开启高速浏览模式。（无需更换浏览器）或者，升级到下面的浏览器，一劳永逸！</p>';
                    }
                    else
                    {
                        echo '<h2>为了更好的体验，请使用更先进的浏览器。</h2>';
                    }
                ?>
                推荐使用：<a href="http://chrome.360.cn/" title="360极速浏览器" target="_blank">360极速浏览器</a>、<a href="http://firefox.com.cn/download/" target="_blank" title="火狐浏览器" >火狐浏览器</a>、<a href="http://www.google.cn/chrome/browser/" title="谷歌浏览器" target="_blank">谷歌浏览器</a>
                <p>我们也支持 <a href="https://www.apple.com/safari/" title="Safari" target="_blank">Safari</a>、<a href="http://browser.qq.com/" title="QQ浏览器" target="_blank">QQ浏览器</a> 等其它浏览器 </p>
            </td>
        </tr>
    </table>
</body>
</html>
