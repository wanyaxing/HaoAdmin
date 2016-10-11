<?php
/**
 * 使用HaoConnect查询七牛用TOKEN
 * User: axing
 * Date: 15-04-06
 * Time: 下午6:37
 */
include $_SERVER['DOCUMENT_ROOT'].'/../lib/HaoConnect/HaoConnect.php'; //此处默认HaoConnect库所在/lib/处于/webroot/同级目录。

$result = QiniuConnect::requestGetUploadTokenForQiniu($_REQUEST);
return json_encode($result->properties());

