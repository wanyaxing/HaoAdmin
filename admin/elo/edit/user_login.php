<?php
	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$requestResult = HaoConnect::request('axapi/check_captcha',$_REQUEST,'post');
		if ($requestResult->isResultsOK())
		{
			$requestResult = UserConnect::requestLogin($_REQUEST);
			if ($requestResult->isResultsOK() && $requestResult->find('level')>5)
			{

			}
			else
			{
				UserConnect::requestLogOut();
			}
			if ($requestResult->isResultsOK() && $requestResult->find('level')<5)
			{
				$requestResult = HaoResult::instanceModel('',-1,'该账号无权使用后台管理页面哦');
			}
		}
		echo json_encode($requestResult->properties());
		exit;
	}

	if (Utility::getCurrentUserID()>0)
	{
		UserConnect::requestLogOut();
	}

?>
<form class="clearfix" action="" method="post">
	<div class="form-group" required>
		<label>用户名</label>
		<input name="account" type="text" class="form-control" placeholder="账户/手机号/邮箱">
	</div>
	<div class="form-group" required>
		<label>密码</label>
		<input name="password" type="password" class="form-control" placeholder="密码">
	</div>
	<div class="form-group">
		<label>验证码</label>
		<div class="row">
			<div class="col-xs-9" required>
				<input name="captcha_code" type="string" class="form-control" placeholder="验证码">
			</div>
			<div class="col-xs-3">
				<input name="captcha_key" type="hidden">
			</div>
		</div>
	</div>

	<!-- <div class="checkbox">
		<label>
		  <input type="checkbox"> 记住登录状态
		</label>
	</div> -->
	<button type="button" class="btn btn-default" onclick="HaoAdmin.show('/edit/user_add');">注册</button>
	<button type="submit" class="btn btn-success pull-right">登录</button>
</form>
