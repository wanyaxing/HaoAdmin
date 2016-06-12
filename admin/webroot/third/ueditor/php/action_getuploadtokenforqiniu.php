<?php
/**
 * 使用HaoConnect查询七牛用TOKEN
 * User: axing
 * Date: 15-04-06
 * Time: 下午6:37
 */

if (!isset($CONFIG['filePathOfHaoConnect']) || !file_exists($CONFIG['filePathOfHaoConnect']))
{
    return '{"errorCode":1,"errorStr":"no lib of HaoConnect FOUND","extraInfo":null,"resultCount":1,"results":null,"timeCost":"0.01036","timeNow":"2016-04-06 18:40:10","modelType":"HaoResult"}';
}

include $CONFIG['filePathOfHaoConnect'];
$result = QiniuConnect::requestGetUploadTokenForQiniu($_REQUEST);
return json_encode($result->properties());

