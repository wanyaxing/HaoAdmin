<?php
    if ($requestPath=='/home')
    {
        $requestPath='/welcome';
    }

    if (strpos($_SERVER['HTTP_USER_AGENT'],'MSIE ') !==false && (strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 6.") || strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 7.")  || strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 8.") || strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 9.")))
    {
        include AXAPI_ELO_PATH . '/ie_no_more.php' ;
        exit;
    }

    //获得正文内容
    ob_start();
    try {
        include AXAPI_ELO_PATH.$requestPath.'.php';
        $main_content = ob_get_clean();
    } catch (Exception $e) {
        $main_content = $e->getMessage();
    }

    //配置标题等参数
    if (!isset($PAGE_TITLE))                                { $PAGE_TITLE       = AXAPI_PROJECT_TITLE;                    }
    if (!isset($PAGE_KEYWORDS)    || $PAGE_KEYWORDS=='' )   { $PAGE_KEYWORDS    = $PAGE_TITLE;                    }
    if (!isset($PAGE_DESCRIPTION) || $PAGE_DESCRIPTION=='' ){ $PAGE_DESCRIPTION = $PAGE_TITLE;                    }
    if (!isset($PAGE_ICON))                                 { $PAGE_ICON        = W2Web::getCurrentHost().'/images/logo.png'; }
    $main_content = preg_replace('/(<\w+)/',sprintf('$1 page_title="%s" page_keywords="%s" page_description="%s" page_icon="%s"',$PAGE_TITLE,$PAGE_KEYWORDS,$PAGE_DESCRIPTION,$PAGE_ICON),$main_content,1);

    //ajax请求 或 根目录文件 只输出正文
    if (Utility::getHeaderValue('X-Requested-With') == 'XMLHttpRequest' || preg_match('/^\/[^\/]+$/', $requestPath))
    {
        echo $main_content;
        exit;
    }

    //获得侧边栏数据
    ob_start();
    include AXAPI_ELO_PATH.'/side.php';
    $side_content = ob_get_clean();

//开始组装完整正文
?>
<?php include AXAPI_ELO_PATH.'/header.php'; ?>
<?php if (!is_object($currentUserResult)): ?>
<body>
<script type="text/javascript">
    $(function(){
        HaoAdmin.user_login();
    });
</script>
</body>
</html>
<?php else: ?>
<body>
    <div class="container" id="home_container">
        <nav class="navbar navbar-default">
            <div id="home_navcontainer" class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#haoadmin-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">
                        <?= AXAPI_PROJECT_TITLE ?>
                    </a>
                </div>
                <div class="collapse navbar-collapse" id="haoadmin-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" role="button"><span class="glyphicon glyphicon-user"></span><?= Utility::getCurrentUserName() ?><span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href=/edit/user_detail?id=<?= Utility::getCurrentUserId() ?> >个人中心</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="javascript:;" onclick="HaoAdmin.user_login()" >退出</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="row" id="div_alert_notice">
        </div>
        <div class="row">
            <div id="side_div" class="col-md-3">
            <div id="side_content" class="panel-group nav" role="tablist"><?= $side_content?></div>
            </div>
            <div class="col-md-9">
                    <div id="main_content"><?= $main_content ?></div>
            </div>
        </div>
    </div>
</body>
</html>
<?php endif; ?>
