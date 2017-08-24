<?php
  if (!isset($title)){$title = AXAPI_PROJECT_TITLE;}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=414,user-scalable=0, target-densitydpi=device-dpi">
<title><?= $PAGE_TITLE ?></title>
<meta name="keywords" content="<?= $PAGE_KEYWORDS ?>" />
<meta name="description" content="<?= $PAGE_DESCRIPTION ?>" />
<link rel="apple-touch-icon" href="<?= $PAGE_ICON ?>" />
<link rel="stylesheet" type="text/css" href="/third/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="/third/chosen/chosen.css">
<link rel="stylesheet" type="text/css" href="/third/haouploader/css/haouploader.css">
<link rel="stylesheet" type="text/css" href="/third/jquery-confirm/css/jquery-confirm.css">
<link rel="stylesheet" type="text/css" href="/third/datetimepicker/jquery.datetimepicker.min.css">
<link rel="stylesheet" type="text/css" href="/third/bootstrap-switch/css/bootstrap3/bootstrap-switch.css">
<link rel="stylesheet" type="text/css" href="/third/nprogress/nprogress.css">
<link rel="stylesheet" type="text/css" href="/css/haoadmin.css">
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="/third/jquery/1.11.3/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/third/bootstrap/js/bootstrap.min.js"></script>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="/third/bootstrap/html5shiv.min.js"></script>
  <script src="/third/bootstrap/respond.min.js"></script>
<![endif]-->
<script type="text/javascript" src="/third/jquery-confirm/dist/jquery-confirm.min.js"></script>
<script type="text/javascript" src="/third/jquery.pjax.js"></script>
<script type="text/javascript" src="/third/jquery.ez-bg-resize.js"></script>
<script type="text/javascript" src="/third/haoconnect/haoresult.js"></script>
<script type="text/javascript" src="/third/haoconnect/haoconnect.js"></script>
<script type="text/javascript" src="/third/nprogress/nprogress.js"></script>
<script type="text/javascript" src="/third/jquery-html5Validate.js"></script>
<script type="text/javascript" src="/third/LABjs/LAB.min.js"></script>
<script type="text/javascript" src="/js/haoadmin.js"></script>
<script type="text/javascript" src="/js/adminpro.js"></script>
<?php if (is_object($currentUserResult)): ?>
<script type="text/javascript">
    AMAP_WEBAPI_KEY = "<?= AMAP_WEBAPI_KEY ?>";
</script>
<script type="text/javascript" src="/js/easemob.js"></script>
<?php endif; ?>
</head>
