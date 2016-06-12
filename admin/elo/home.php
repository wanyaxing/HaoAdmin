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
    <div class="row">
      <nav class="navbar navbar-fixed-top">
        <div id="home_navcontainer" class="container">
          <div class="navbar-header">
            <a class="navbar-brand" href="/">
              <?= AXAPI_PROJECT_TITLE ?>
            </a>
          </div>
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
      </nav>
      <div class="col-md-3">
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
