<?php
/**
 * 供前端ajax进行请求，将相应表单转化调用对应的HaoConnect接口。
 */
ini_set('display_errors',1);            //错误信息
ini_set('display_startup_errors',1);    //启动错误信息

error_reporting(-1);                    //打印出所有的 错误信息

date_default_timezone_set('Asia/Shanghai');//设定时区

define("AX_TIMER_START", microtime (true));//记录请求开始时间


    //加载配置文件
    require_once(__dir__.'/../config.php');

    //加载HaoConnect核心类
    require_once(AXAPI_ROOT_PATH.'/lib/HaoConnect/HaoConnect.php');

    //加载基础方法
    require_once(AXAPI_ROOT_PATH.'/components/Utility.php');


//配置禁用的接口
$disableParms = array('axapi/create_mhc_with_table_name','axapi/update_codes_of_hao_connect','user/login');

//提取接口方法
$urlParam     = $_REQUEST['url_param'];
$urlParam     = strtolower(trim(trim($urlParam),'/'));
unset($_REQUEST['url_param']);
list($apiController,$apiAction) = explode('/',$urlParam);

//禁用过滤，因为驼峰和下划线都要兼容，所以这里想要过滤就得复杂一点。
$urlParamLower = strtolower( HaoUtility::camelCase($apiController.'/'.$apiAction) );
$isInDisable = false;
foreach ($disableParms as $dParm) {
	if ($urlParamLower == strtolower( HaoUtility::camelCase($dParm) ) )
	{
		$isInDisable = true;
		break;
	}
}
if (!isset($apiController,$apiAction) || $isInDisable)
{
	echo json_encode(array(
                            'errorCode'  => -1,
                            'errorStr'   => '不支持使用HaoAJAX转发该接口哦。',
                            'resultCount'=> 0,
                            'results'    => $_REQUEST,
                            'extraInfo'  => null
                        ));
	exit;
}

if (Utility::getHeaderValue('Referer')==null || strpos(Utility::getHeaderValue('Referer'),'http://'.$_SERVER['HTTP_HOST'])!==0)
{
    echo json_encode(array(
                            'errorCode'  => -1,
                            'errorStr'   => '错误的调用方法，请联系管理员。',
                            'resultCount'=> 0,
                            'results'    => $_REQUEST,
                            'extraInfo'  => null
                        ));
    exit;
}

//调用对应接口方法
$content = '';
try {
    $method = new ReflectionMethod(HaoUtility::camelCase($apiController.'Connect'), HaoUtility::camelCase('request'.$apiAction));
    $result = $method->invoke(null,$_REQUEST);
} catch (Exception $e) {
	$method       = $_SERVER['REQUEST_METHOD'];
	$result = HaoConnect::request($urlParam,$_REQUEST,$method);
}

if (Utility::getHeaderValue('X-Requested-With') == 'XMLHttpRequest' && Utility::getHeaderValue('X-PJAX')==null)
{
    echo json_encode($result->properties());
}
else
{
    if ($result->isResultsOK())
    {
        if (Utility::getHeaderValue('Referer') && strpos(Utility::getHeaderValue('Referer'),'http://'.$_SERVER['HTTP_HOST'])===0)
        {
            header('location:'.Utility::getHeaderValue('Referer'),302);
            exit;
        }
        else
        {
            header(W2Web::headerStringOfCode(500));
            echo 'no auth here.';
            exit;
        }
    }
    else
    {
        header(W2Web::headerStringOfCode(500));
        echo $result->errorStr;
        exit;
    }
}
exit;

