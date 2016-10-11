<?php
/**
 * 抓取远程图片
 * User: Jinqn
 * Date: 14-04-14
 * Time: 下午19:18
 */
set_time_limit(0);
include("Uploader.class.php");

/* 上传配置 */
$config = array(
    "pathFormat" => $CONFIG['catcherPathFormat'],
    "maxSize" => $CONFIG['catcherMaxSize'],
    "allowFiles" => $CONFIG['catcherAllowFiles'],
    "oriName" => "remote.png"
);
$fieldName = $CONFIG['catcherFieldName'];

/* 抓取远程图片 */
$list = array();
if (isset($_POST[$fieldName])) {
    $source = $_POST[$fieldName];
} else {
    $source = $_GET[$fieldName];
}
if (!isset($CONFIG['filePathOfHaoConnect']) || !file_exists($CONFIG['filePathOfHaoConnect']))
{
    return '{"state":"NO LIB OF HAOCONNECT FOUND"}';
}
include $CONFIG['filePathOfHaoConnect'];
foreach ($source as $imgUrl) {
    // $item = new Uploader($imgUrl, $config, "remote");
    // $info = $item->getFileInfo();
    $qiniuResult = HaoConnect::post('qiniu/fetch_url_to_qiniu',array('url'=>$imgUrl));
    if ($qiniuResult->isResultsOK())
    {
        array_push($list, array(
            "state" => 'SUCCESS',
            "url" => $qiniuResult->results(),
            "size" => 999,
            "title" => htmlspecialchars('catcherFile'),
            "original" => htmlspecialchars('catcherFile'),
            "source" => htmlspecialchars($imgUrl)
        ));
    }
}

/* 返回抓取数据 */
return json_encode(array(
    'state'=> count($list) ? 'SUCCESS':'ERROR',
    'list'=> $list
));
