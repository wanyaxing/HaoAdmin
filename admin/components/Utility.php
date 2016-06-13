<?php
/**
 * 自定义的一些方法工具
 * @package conf
 * @author axing
 * @since 1.0
 * @version 1.0
 */

class Utility
{

	/**
	 * 静态变量，存储当前用户ID
	 * @var array
	 */
	protected static $_CURRENTUSERID = null;
	protected static $_CURRENTUSER_RESULT = null;

	/**
	 * 静态变量，存储优化后的HEADERS信息
	 * @var array
	 */
	protected static $_HEADERS = null;

    /**
     * 提取请求中的headers信息，
     * 并复制一份首字母大写其他字母小写的key值，
     * 最后存储到$_HEADERS变量中供使用
     * @return array 优化后的headers信息
     */
	public static function getallheadersUcfirst()
	{
		if (static::$_HEADERS === null)
		{
			static::$_HEADERS = getallheaders();
			foreach (static::$_HEADERS as $key => $value) {
				static::$_HEADERS[ucfirst(strtolower($key))] = $value;
			}
		}
		return static::$_HEADERS;
	}

	public static function getHeaderValue($p_key)
	{
		$_headers = Utility::getallheadersUcfirst();
		$p_key = ucfirst(strtolower($p_key));
		if (array_key_exists($p_key,$_headers))
		{
			return $_headers[$p_key];
		}
		return null;
	}


	public static function setCurrentUserID($p_userID=null)
	{
		static::$_CURRENTUSERID = $p_userID;
	}

    /** 获得当前登录用户ID */
	public static function getCurrentUserID()
	{
		if (is_null(static::$_CURRENTUSERID))
		{
			$cookieUserId    = W2Web::loadCookie('Userid');
			$cookieLogintime = W2Web::loadCookie('Logintime');
			$cookieCheckcode = W2Web::loadCookie('Checkcode');

			if ($cookieUserId>0)
			{
				static::setCurrentUserID($cookieUserId);
			}
		}
		return static::$_CURRENTUSERID ;
	}

	/**
	 * 获得当前用户的信息
	 * @return HaoResult
	 */
	public static function getCurrentUserResult()
	{
		if (is_null(static::$_CURRENTUSER_RESULT))
		{
			$requestResult = UserConnect::requestGetMyDetail();
			if ($requestResult->isResultsOK())
			{
				static::$_CURRENTUSER_RESULT = $requestResult;
			}
		}
		return static::$_CURRENTUSER_RESULT ;
	}

    /** 从当前登录用户的真实姓名／昵称／手机号等里根据优先级挑一个非空的字段出来。 */
	public static function getCurrentUserName()
	{
		$detailResult = Utility::getCurrentUserResult();
		if (is_object($detailResult))
		{
			return W2String::getValidValue($detailResult->find('realname') , $detailResult->find('username') , $detailResult->find('telephone') , $detailResult->find('email') ,'admin');
		}
		return 'admin';
	}

    /** 获得当前用户的IP */
	public static function getCurrentIP()
	{
		$onlineip = null;
	    if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
	    {
	    	$onlineip = $_SERVER['REMOTE_ADDR'];
	    }
	    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown'))
	    {
	    	$onlineip = getenv('HTTP_CLIENT_IP');
	    }
	    elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown'))
		{
			$onlineip = getenv('HTTP_X_FORWARDED_FOR');
		}
		elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown'))
	    {
	    	$onlineip = getenv('REMOTE_ADDR');
	    }
		return $onlineip;
	}

    /** PHP5.4以上使用JSON_UNESCAPED_UNICODE编码json字符，否则只能自己实现了。 */
    public static function json_encode_unicode($data) {
	    if (defined('JSON_UNESCAPED_UNICODE')) {
	        return json_encode($data, JSON_UNESCAPED_UNICODE);
	    }
	    return preg_replace_callback('/(?<!\\\\)\\\\u([0-9a-f]{4})/i',
		    function($m) {
		        $d = pack("H*", $m[1]);
		        $r = mb_convert_encoding($d, "UTF8", "UTF-16BE");
		        return $r !== "?" && $r !== "" ? $r: $m[0];
		    },
		    json_encode($data)
	    );
	}

	/**
	 * 转换$_REQUEST字典，去除无效参数。
	 * @param  array  $paramsDefault 默认参数
	 * @param  array  $paramsLocked  锁定参数
	 * @return array                params
	 */
	public static function getParamsOfListInRequest($paramsDefault=array(),$paramsLocked=array())
	{
		$params = $_REQUEST;
		if (isset($params['search_type'],$params['search_word']) )
		{
			$params[$params['search_type']] = $params['search_word'];
			unset($params['search_type']);
			unset($params['search_word']);
		}
		$params = array_filter($params,function($val){
			return $val!==null && $val!=='';
		});
		if (!isset($params['page_max']))
		{
			$params['iscountall'] = 1;
		}
		if (!isset($params['page']))
		{
			$params['page'] = 1;
		}
		if (isset($params['is_only_filter_with_request']) && $params['is_only_filter_with_request'])
		{
			// $params = $params;
		}
		else
		{
			$params = W2Array::merge($paramsDefault,$params,$paramsLocked);
		}
		return $params;
	}


    /** 创建带sort功能的th字符串。 */
    public static function getThWithOrder($name,$order_key,$params=null)
    {
    	if (is_null($params)){ $params = $_REQUEST; }
    	if (isset($params['order']) && $params['order']==$order_key)
    	{
    		$reverse = 'desc';
    		if (isset($params['isreverse']) && $params['isreverse']== 0)
    		{
    			$reverse = 'asc';
    			$params['isreverse'] = 1;
    		}
    		else
    		{
    			$params['isreverse'] = 0;
    		}
    	}
    	else
    	{
	    	$reverse = 'both';
    		$params['isreverse'] = ($order_key=='id')?'0':'1';
    	}
    	$params['order'] = $order_key;
    	$params['page'] = 1;
        return '<th class="sortable '.$reverse.'"><a href="?'.http_build_query($params).'">'.$name.'</a></th>'."\n";
    }

    /** 根据给定的参数，创建翻页导航组。 */
    public static function strOfPagination($params=null)
    {
    	$s = '<nav>'."\n"
			.'  <ul class="pagination">'."\n";
		// 上一页按钮
		$s.= '    <li class="'
						.($params['page']==1
							?'disabled'
						.'">'."\n"
			.'        <span aria-hidden="true">&laquo;</span>'."\n"
							:''
						.'">'."\n"
			.'      <a href="?'.http_build_query(array_merge($params,array('page'=>$params['page']-1))).'" aria-label="Previous">'."\n"
			.'        <span aria-hidden="true">&laquo;</span>'."\n"
			.'      </a>'."\n"
						)
			.'    </li>'."\n";
		// 为了保证7个分页按钮，还是要仔细算算啊。
		$iList = array();
		// 1.取出当前页前后2个按钮共五个按钮
		for ($i=$params['page']-2; $i <= $params['page']+2; $i++) {
			if ($i>=1 && $i<=$params['page_max'] && !in_array($i,$iList))
			{
				$iList[] = $i;
			}
		}
		// 2.从第一页开始取数，加上前面取过的，至少6个为止。
		for ($i=1; $i <= $params['page_max'] ; $i++) {
			if ($i>=1 && $i<=$params['page_max'] && !in_array($i,$iList) && count($iList)<6)
			{
				$iList[] = $i;
			}
		}
		// 3.从最后一页开始取数，加上前面取过的，取满7个。
		for ($i == $params['page_max'] ; $i>=1; $i--) {
			if ($i>=1 && $i<=$params['page_max'] && !in_array($i,$iList) && count($iList)<7)
			{
				$iList[] = $i;
			}
		}
		// 4.重新排序，准备输出
		sort($iList);
		// 5.依次输出，当数字为当前页的左右连续翻页的边际页时，显示...
		foreach ($iList as $index=>$i) {
			# code...
			$s.= '    <li class="'.($params['page']==$i?'disabled':'').'">'
						.'<a href="?'.http_build_query(array_merge($params,array('page'=>$i))).'">'
								.((
									 ($i<$params['page'] && $index>0 && $iList[$index-1]!=$i-1)
									 || ($i>$params['page'] && $index<count($iList)-1 && $iList[$index+1]!=$i+1)
								  )?'...'
									:$i)
						.'</a>'
					.'</li>'."\n";
		}

		// 下一页按钮
		$s.= '    <li class="'
					.($params['page']==$params['page_max']
						?'disabled'
					.'">'."\n"
			.'        <span aria-hidden="true">&raquo;</span>'."\n"
						:''
					.'">'."\n"
			.'      <a href="?'.http_build_query(array_merge($params,array('page'=>$params['page']+1))).'" aria-label="Next">'."\n"
			.'        <span aria-hidden="true">&raquo;</span>'."\n"
			.'      </a>'."\n"
						)
			.'    </li>'."\n";
		$s.= '  </ul>'."\n";
		$s.= '  <ul class="pagination pagination-count">'."\n"
			.'    <li class="">'."\n"
			.'      <span>'.$params['count_total']."条记录\n"
			.'      </span>'."\n"
			.'    </li>'."\n"
			.'  </ul>'."\n"
			.'</nav>';
		return $s;
    }


    /** 获得面包屑导航的代码 */
    public static function strOfBreadCrumb($breadCrumb)
    {
    	$s = '<ol class="breadcrumb">'."\n"
			.'  <li><a href="/">'.AXAPI_PROJECT_TITLE.'</a></li>'."\n"
			.'  <li>'.$breadCrumb['parent'].'</li>'."\n"
			.'  <li class="active"><a href="'.$breadCrumb['url'].'">'.$breadCrumb['name'].'</a></li>'."\n"
			.'</ol>'."\n";
		return $s;
    }

    /** 返回当前文件相对根目录的路径 */
    public static function getCurrentFileUrl()
    {
    	$_dbt = debug_backtrace();
    	return substr(str_replace(realpath(AXAPI_ELO_PATH),'',$_dbt[0]['file']),0,-1 - strlen(pathinfo($_dbt[0]['file'],PATHINFO_EXTENSION)));
    }

    /** 根据提供的值，创建支持单张图片上传的input，并赋予对应的值。 */
    public static function strOfPhoto($name,$value=null,$isMultiple=false)
    {
        if (!isset($value))
        {
            if (isset($GLOBALS['params'][$name]))
            {
                $value = $GLOBALS['params'][$name];
            }
            else if (isset($_REQUEST[$name]))
            {
                $value = $_REQUEST[$name];
            }
        }

        $s  = '<input name="'.$name.'" type="hidden" class="form-control input_which_upload_to_qiniu" value="'.$value.'" '.($isMultiple?'multiple':'').'>';
        return $s;
    }

    /** 根据提供的值，创建支持单张图片上传的input，并赋予对应的值。 */
    public static function strOfPhotoMultiple($name,$value=null)
    {
        return static::strOfPhoto($name,$value,true);
    }

    /** 根据提供的值，创建支持切换样式的checkbox，并选中对应的值。 */
    public static function strOfSwitch($name,$value=null)
    {
        if (!isset($value))
        {
            if (isset($GLOBALS['params'][$name]))
            {
                $value = $GLOBALS['params'][$name];
            }
            else if (isset($_REQUEST[$name]))
            {
                $value = $_REQUEST[$name];
            }
        }

        $s  = '<input name="'.$name.'" type="checkbox" class="form-control bootstrap-switch" value="1" '. ($value==true?'checked':'') .'>';
        return $s;
    }

    /** 根据提供的值，创建支持切换样式的checkbox，并选中对应的值。 */
    public static function strOfTime($name,$value=null)
    {
        if (!isset($value))
        {
            if (isset($GLOBALS['params'][$name]))
            {
                $value = $GLOBALS['params'][$name];
            }
            else if (isset($_REQUEST[$name]))
            {
                $value = $_REQUEST[$name];
            }
        }

        $s  = '<input name="'.$name.'" type="string" class="form-control datetimepicker" value="'.$value.'" >';
        return $s;
    }
    /** 根据提供的值，创建富文本样式的输入去，并填充对应的值。 */
    public static function strOfText($name,$value=null)
    {
        if (!isset($value))
        {
            if (isset($GLOBALS['params'][$name]))
            {
                $value = $GLOBALS['params'][$name];
            }
            else if (isset($_REQUEST[$name]))
            {
                $value = $_REQUEST[$name];
            }
        }

        $s  = '<script name="'.$name.'" type="text/plain" >'.$value.'</script>';
        return $s;
    }

    /** 根据提供的值，创建支持切换样式的checkbox，并选中对应的值。 */
    public static function strOfCaptcha()
    {
        return '<div class="row">'
                .'<div class="col-xs-10" required>'
                    .'<input name="captcha_code" type="string" class="form-control" placeholder="验证码">'
                .'</div>'
                .'<div class="col-xs-2">'
                    .'<input name="captcha_key" type="hidden">'
                .'</div>'
            .'</div>';;
    }

    /** 根据提供的键值对，创建options字符串，并选中对应的值。 */
    public static function strOfSelect($name,$options,$value=null)
    {
    	if (!isset($value))
    	{
    		if (isset($GLOBALS['params'][$name]))
    		{
	    		$value = $GLOBALS['params'][$name];
    		}
    		else if (isset($_REQUEST[$name]))
    		{
	    		$value = $_REQUEST[$name];
    		}
    	}

    	$s  = '<select name="'.$name.'" class="form-control" '.(isset($GLOBALS['breadCrumb']['params'][$name])?'disabled':'').'> ';
        foreach ($options as $oValue => $oName)
        {
            $s .= 	'<option value="' . $oValue . '"' . (strval($value)===strval($oValue)?' selected':'') . '>'
	            	  	.$oName
            	   	.'</option> ';
        }
    	$s .= '</select>'."\n";
        return $s;
    }

    /** 根据提供的键值对，创建radio组，并选中对应的值。 */
    public static function strOfRadio($name,$options,$value=null)
    {
    	if (!isset($value))
    	{
    		if (isset($GLOBALS['params'][$name]))
    		{
	    		$value = $GLOBALS['params'][$name];
    		}
    		else if (isset($_REQUEST[$name]))
    		{
	    		$value = $_REQUEST[$name];
    		}
    	}

    	$s  = '<div class="btn-group" data-toggle="buttons">';
    	//.(isset($GLOBALS['breadCrumb']['params'][$name])?'disabled':'').'> ';
        foreach ($options as $oValue => $oName)
        {
            $s .= 	'<label class="btn btn-default' . (strval($value)===strval($oValue)?' active':'') . '">'
		            	.'<input type="radio" name="'.$name.'" autocomplete="off" value="'.$oValue.'"' . (strval($value)===strval($oValue)?' checked':'') . '>'
			            .$oName
		            .'</label>';
        }
    	$s .= '</div>'."\n";
        return $s;
    }

    /** 根据提供的键值对，以及参数，创建options字符串。 */
    public static function strOfChosen($name,$strAttrs,$selectValues=null,$selectNames=null,$options=null,$isMultiple=false,$placeholder=null)
    {
    	if (!isset($selectValues))
    	{
    		if (isset($GLOBALS['params'][$name]))
    		{
	    		$selectValues = explode(',',$GLOBALS['params'][$name]);
    		}
    		else
    		{
    			$selectValues = array();
    		}
    	}
    	else if (!is_array($selectValues))
    	{
    		$selectValues = explode(',',$selectValues);
    	}

    	if (!isset($selectNames))
    	{
    		$selectNames = $selectValues;
    	}
    	else if (!is_array($selectNames))
    	{
    		$selectNames = explode(',',$selectNames);
    	}

    	if (!isset($options))
    	{
    		$options = array();
    	}
		foreach ($selectValues as $key=>$oValue)
		{
			if (!isset($options[$oValue]))
			{
				$options[$oValue] = $selectNames[$key];
			}
		}
		if (count($options)==0 || !isset($options['']))
		{
			$options = W2Array::merge(array(''=>'...'),$options);
		}


    	$s  = '<select name="'.$name.'" class="form-control select_chosen"'
    						.' ' . $strAttrs
    						.(isset($GLOBALS['breadCrumb']['params'][$name])?' disabled':'')
    						.(!is_null($placeholder)?' data-placeholder="'.$placeholder.'"':'')
    						.($isMultiple?' multiple':'')
    						.'> ';
        foreach ($options as $oValue => $oName)
        {
            $s .= 	'<option value="' . $oValue . '"' . (in_array($oValue,$selectValues)?' selected':'') . '>'
	            	  	.$oName
            	   	.'</option> ';
        }
    	$s .= '</select>'."\n";
        return $s;
    }
    /** 根据提供的键值对，创建标签类型的options字符串。用户可以手动增加标签。 （默认多选）*/
    public static function strOfChosenWithTags($name,$selectValues=null,$options=null,$isMultiple=true,$placeholder=null)
    {
        $strAttrs =  'search-type="tags" ';
        if (!W2Array::isList($options))
        {
            $tmp = array();
            foreach ($options as $op) {
                $tmp[$op] = $op;
            }
            $options = $tmp;
        }
        return static::strOfChosen($name,$strAttrs,$selectValues,null,$options,$isMultiple,$placeholder);
    }
    /** 根据提供的键值对，创建分类类型的options字符串。用户可以手动增加分类。（默认单选） */
    public static function strOfChosenWithCategory($name,$selectValues=null,$options=null,$isMultiple=false,$placeholder=null)
    {
    	return static::strOfChosenWithTags($name,$selectValues,$options,$isMultiple,$placeholder);
    }
    /** 根据提供的键值对，创建ajax类型的options字符串。用户可以搜索关键字后增加数据。 */
    public static function strOfChosenWithAjax($name,$ajaxUrl,$valuePath,$namePath=null,$selectValues=null,$selectNames=null,$options=null,$isMultiple=false,$placeholder=null)
    {

    	if (!isset($namePath) && isset($valuePath))
    	{
    		$namePath = $valuePath;
    	}

    	$strAttrs =  'search-type="ajax" '
					.'search-ajax-url="'.$ajaxUrl.'" '
					.'search-value-path="'.$valuePath.'" '
					.'search-name-path="'.$namePath.'" ';

    	return static::strOfChosen($name,$strAttrs,$selectValues,$selectNames,$options,$isMultiple,$placeholder);
    }

    /** 智能设定面包屑 */
    public static function breadCrumb($breadCrumb)
    {
		if (!isset($GLOBALS['breadCrumb']))
		{
			if (isset($GLOBALS['IS_SIDE_TREE']) && $GLOBALS['IS_SIDE_TREE'])
			{
				$GLOBALS['SIDE_TREE'][$breadCrumb['parent']][] = $breadCrumb;
				return false;
			}
			$GLOBALS['breadCrumb'] = $breadCrumb;
		}
		return true;
    }

    /**
     * 发起请求，并得到请求结果。（params参数中的分页信息也会被更新）
     * 如果不指定报错处理，则直接打印错误信息并退出。
     * @param  array &$params       [description]
     * @param  functino $errorCallBack [description]
     * @return [type]                [description]
     */
    public static function requestBreadCrumb(&$params,$errorCallBack=null)
    {
		$requestResult = HaoConnect::request($GLOBALS['breadCrumb']['urlParam'],$params);

		if (is_object($requestResult))
		{
			if ($requestResult->isResultsOK())
			{

			}
			else if (!is_null($errorCallBack))
			{
				$errorCallBack($requestResult);
			}
			else
			{
				print('<p>');
				print($requestResult->errorStr);
				print('</p>');
				exit;
			}
		}

		if (!isset($params['page_max']))
		{
			$params['page_max']    = $requestResult->find('extraInfo>pageMax',1);
			if ( $params['page_max']<1 )
			{
				$params['page_max']		= 1;
			}
			$params['count_total'] = $requestResult->find('extraInfo>countTotal',0);
		}
		return $requestResult;
    }
}

//-------------------全局方法-----------------------

/** debug 直接打印日志 */
function AX_DEBUG($p_info=null)
{
    if (defined('IS_AX_DEBUG') && !is_null($p_info))
    {
    	print("\n");
        $_dbt = debug_backtrace();
        foreach ($_dbt as $_i => $_d) {
            if(!array_key_exists('file', $_d) || $_d['file']=='' || $_d['file']==__file__)
            {
                continue;
            }
            $_fileName = pathinfo($_d['file'],PATHINFO_BASENAME);
            if ($_fileName == 'DBTool.php' || $_fileName == 'DBModel.php' || $_fileName == 'AbstractHandler.php' )
            {
            	continue;
            }
            $_dFuc = $_d['function'];
            // if (in_array($_dFuc , [ 'loadModelList' , 'loadModelListByIds' , 'loadModelListById' , 'loadModelFirstInList' , 'saveModel', 'update', 'delete' , 'count', 'countAll' ] ) )
            // {
            // 	continue;
            // }
            printf('%s [%d] %s -> %s ' , W2Time::microtimetostr(null,'Y-m-d H:i:s.u') , $_d['line'],  $_fileName, $_dFuc);
            break;
        }
        if (is_string($p_info))
        {
	        print(strlen($p_info)>100?" : \n":' : ');
	        print($p_info);
        }
        else
        {
        	var_export($p_info);
        }
        print("\n");
    }
}
