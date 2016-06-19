<?php

	$urlParam = 'cache/empty_cache';

?>
<form class="form-horizontal clearfix" method="post">
	<input name="url_param" type="hidden" class="form-control" placeholder="ID" value="<?= $urlParam; ?>">
	<center>您确定要这么做吗？</center>
	<!-- <button type="button" class="btn btn-danger center-block">删除</button> -->
	<button type="submit" class="btn btn-success pull-right">提交</button>
</form>

