<?php
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

    AX_DEBUG('start');

    /**
     * 插入日志，之所以方法放这里，是因为index.php代码改动最少，这个方法存活率最高，因为是用来记日志的嘛。
     * @param  string|array $p_content [description]
     * @param  string $p_type    类型，用于组成文件名
     */
    function file_put_log($p_content='',$p_type='access')
    {
        try {
            $currentUserId = Utility::getCurrentUserID();
        } catch (Exception $e) {
            $currentUserId = 0;
        }
        file_put_contents(sprintf('%s/%s-%s.log'
                            ,AXAPI_ROOT_PATH.'/logs/'
                            ,$p_type
                            ,strftime('%Y%m%d'))
                            ,sprintf("[%s] (%s s) [%s] [%d] [%s] [%s]: %s\n"
                                        ,W2Time::microtimetostr(AX_TIMER_START)
                                        ,number_format(microtime (true) - AX_TIMER_START, 5, '.', '')
                                        ,Utility::getCurrentIP()
                                        ,Utility::getCurrentUserID()
                                        ,count($_POST)>0?'POST':'GET'
                                        ,$GLOBALS['requestPath']
                                        ,is_string($p_content)?$p_content:Utility::json_encode_unicode($p_content)
                                    )
                            ,FILE_APPEND);
    }

    /**
     * 主要用于捕捉致命错误，每次页面处理完之后执行检查
     * @return [type] [description]
     */
    function catch_fatal_error()
    {
      // Getting Last Error
       $last_error =  error_get_last();

        // Check if Last error is of type FATAL
        if(isset($last_error['type']))
        {
            // Fatal Error Occurs
            // Do whatever you want for FATAL Errors
            $message = null;
            switch ($last_error['type']) {
                case E_ERROR:
                    $message = '严重错误：服务器此时无法处理您的请求，请稍后或联系管理员。';
                    break;
                case E_PARSE:
                    $message = '代码拼写错误：是Peter干的吗，请向管理员举报Peter。';
                    break;
                case E_WARNING:
                    $message = '警告：出现不严谨的代码逻辑，请告知管理员这个问题。';
                    break;
                case E_NOTICE:
                    $message = '警告：出现不严谨的代码逻辑，请告知管理员这个问题。';
                    break;
            }

            if (!is_null($message))
            {
                //记录错误日志
                file_put_log($_REQUEST,'error');
                file_put_log($last_error,'error');

                //返回错误信息
                // @ob_end_clean();//要清空缓冲区， 从而删除PHPs " 致命的错误" 消息。

                exit;
            }
        }
    }

    register_shutdown_function('catch_fatal_error');

    $requestPath = preg_replace ("/(\/*[\?#].*$|[\?#].*$|\/*$|\.\.+)/", '', $_SERVER['REQUEST_URI']);
    $requestPath = preg_replace('/\/+/','/',$requestPath);

    if ($requestPath=='' || $requestPath == '/' )
    {
        $requestPath = '/other/welcome';

    }

    header('X-PJAX-URL:'.W2Web::getCurrentUrl());

    $currentUserResult = Utility::getCurrentUserResult();

    //路径中的/other/可以自动补上。
    if (!file_exists(AXAPI_ELO_PATH. $requestPath.'.php') && file_exists('/other/'.$requestPath))
    {
        $requestPath = '/other/'.$requestPath;
    }

    if (file_exists(AXAPI_ELO_PATH. $requestPath.'.php'))
    {
        include AXAPI_ELO_PATH . '/home.php' ;
    }
    else
    {
        include AXAPI_ELO_PATH . '/404.php' ;
    }

    //记录接口日志
    file_put_log($_REQUEST,'access');

    exit;
