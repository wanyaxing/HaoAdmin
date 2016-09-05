<?php
    if ($requestPath=='/home')
    {
        $requestPath='/welcome';
    }
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
          <div id="side_content" class="panel-group nav" role="tablist">
            <?php include AXAPI_ELO_PATH.'/side.php'; ?>
          </div>
        </div>
        <div class="col-md-9">
          <div id="main_content"><?php include AXAPI_ELO_PATH.$requestPath.'.php'; ?></div>
        </div>
      </div>
  </div>
</body>
</html>
<?php endif; ?>
