<?php
  echo '你好，此处不支持直接注册，请联系管理员。';
  exit;
?>
<form class="clearfix">
  <div class="form-group">
    <label>手机号</label>
    <input type="email" class="form-control" placeholder="手机号" name="telephone">
  </div>
  <div class="form-group">
    <label>验证码</label>
    <input type="text" class="form-control" placeholder="验证码" name="verify_code">
  </div>
  <div class="form-group">
    <label>密码</label>
    <input type="text" class="form-control" placeholder="密码" name="password">
  </div>
  <button type="button" class="btn btn-default" onclick="HaoAdmin.user_login()">登录</button>
  <button type="submit" class="btn btn-success pull-right">注册</button>
</form>
