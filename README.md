# HaoAdmin

## 我们将后台工作，总结成两件事：##
一：使用表格展示列表数据

二：使用弹框进行操作互动


###一：使用表格展示列表数据###
对应网址：yyy.com/list/xxxxxx

文件位置：/elo/list/xxxxxx.php

###代码结构：####
1. 页面配置
1. 筛选参数
1. 从接口获取结果
1. 引入面包屑导航
1. 设定筛选方案
1. 正文表格
    1. 表头（支持排序）
    1. 内容（逐列处理）
1. 新增按钮（或其他操作）
1. 引入分页导航组

###代码示例####
```php
<?php
/*
 *  本页面的配置，可用于数据请求、面包屑导航、侧边栏等多个场景
 *  本页面可被重复include，
 *  如：侧边栏时引入本页，则会在读取到配置后return，不会发生请求和输出页面代码
 *  如：正文中引入本页，则会发生请求并执行页面输出。
 */
    if (!Utility::breadCrumb(array(
                 'parent'   => '医院管理'
                ,'name'     => '科室列表'
                ,'rank'     => 90
                ,'isAuthed' =>  is_object($currentUserResult) && $currentUserResult->find('level')>=5
                ,'url'      => Utility::getCurrentFileUrl()
                ,'params'   => array()
                ,'urlParam'   => 'hospital_dept/list'
                    ))){return;}
/*
 *  变量：请求参数，用于数据请求和翻页设定
 *  支持默认参数、$_REQUEST、锁定参数，三者合并（后者覆盖前者）
 */
    $params = Utility::getParamsOfListInRequest(array('size'=>DEFAULT_PAGE_SIZE),$breadCrumb['params']);

/*
 * 发起请求，并得到请求结果。（params参数中的分页信息也会被更新）
 * 如果不指定报错处理，则直接打印错误信息并退出。
 */
    $requestResult = Utility::requestBreadCrumb($params);
?>

<?= Utility::strOfBreadCrumb($breadCrumb) ?>

<div class="search_nav form-inline clearfix">
    <form class="" action="?" data-pjax="true">
        <div class="form-group">
            <?= Utility::strOfChosenWithAjax('hospital_id','/ajax_haoconnect.php?url_param=hospital/list','.*?\d>id','.*?\d>name',W2HttpRequest::getRequestString('hospital_id')) ?>
            <input name="keyword" type="text" class="form-control" placeholder="搜索" value=<?= W2HttpRequest::getRequestString('keyword') ?>>
            <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-refresh"></span>应用</button>
        </div>
    </form>
</div>

<div class="table-responsive" style="clear:both;">
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <?= Utility::getThWithOrder('ID','id')?>
            <th>科室</th>
            <th>医院</th>
            <th>状态</th>
        </thead>
        <tbody>
    <?php
        foreach ($requestResult->results() as $detailResult)
        {
    ?>
        <tr>
            <td><a href=<?= '/edit/hospital_dept_detail?id='.$detailResult->find('id') ?> ><?= $detailResult->find('id') ?></a></td>
            <td><?= $detailResult->find('name') ?></td>
            <td><?= $detailResult->find('hospitalIDLocal>name') ?></td>
            <td><?= array('已删','正常','草稿','待审')[$detailResult->find('status')] ?></td>
        </tr>
    <?php
        }
    ?>
        </tbody>
    </table>
</div>
<a href=<?= '/edit/hospital_dept_detail?'.http_build_query($breadCrumb['params']) ?> class="btn btn-default pull-right" ><span class="glyphicon glyphicon-plus"></span>新增</a>

<?= Utility::strOfPagination($params);  ?>
```


###二：使用弹框进行操作互动###
对应网址：yyy.com/edit/xxxxxx?id=1

文件位置：/elo/edit/xxxxxx.php

####代码结构：####
1. 数据处理
1. 列出表单
1. 提交按钮

####代码示例####
```php
<?php
/*
 * /edit/目录下通常用于放置些弹框的页面，
 * 准确的说，是所有/edit/xxx的链接在页面中默认会使用弹窗打开。
 * 在本页，推荐的做法是，创建一个标准的form表单，互动效果由页面自动处理即可。
 */

/**
 * 根据是否传递了id参数来判断当前页到底是数据详情的编辑页还是新增页。
 * 编辑和新增，它俩的区别就在于是否有id。
 * 当然，字段的必须性，在程序实现的时候注意区别护理。
 */


/**
 * 在预处理数据的时候，我们的目标是获得$requestResult。
 * 如果有id就请求detail详情，如果没有id就new一个HaoResult，这样在后续处理的时候都可以使用->find方法来直接打印数据。
 */
    $detailId = W2HttpRequest::getRequestString('id');
    if ($detailId>0)
    {
        $requestResult = HaoConnect::request('hospital/detail',$_REQUEST,METHOD_GET);
    }
    if (isset($requestResult) && is_object($requestResult))
    {
        if ($requestResult->isResultsOK())
        {
            $urlParam = 'hospital/update';
        }
        else
        {
            var_export($requestResult);
            exit;
        }
    }
    else
    {
        $urlParam = 'hospital/add';
        $requestResult = HaoResult::instanceModel($_REQUEST);
    }


/**
 * 下面是输出表单并设定各单元初始值
 * 以下是使用ajax_haoconnect.php进行数据处理的默认方案，供参考。
    * 注意点：
    1.在各单元中，若设定div属性为required，则其包含的表单元素input/select/textarea也会被设定为required
    2.支持HTML5标准的表单校验规则，如指定input的属性，如type="email", type="number", type="tel", type="url", step, min, max, required, pattern, multiple等，
            并有一些本地化扩展，如增加了type="zipcode"邮编等，支持type="hidden"的完整验证（HTML5中是忽略的），支持多type共存，例如type="email|tel", 可以让文本框输入邮箱或者手机号码。
            ，额外增加了四个自定义属性值：data-key, data-target, data-min, data-max
            甚至，你也可以使用pattern="^\d+" 这样的正则约束哦。
            详见：http://www.zhangxinxu.com/wordpress/2012/12/jquery-html5validate-html5-form-validate-plugin/
    3.支持 下拉框／地图／上传头像等特殊的值处理，详见范例代码。
       3.1 $('select[search-type]')                     元素会被处理成特殊的［选择器］组件。开发时，将对应的已选值封装成标准的option即可。详见：Utility::strOfChosenWithTags()和Utility::strOfChosenWithAjax()
       3.2 $('input.input_which_upload_to_qiniu')       元素会被处理成特殊的［图片上传］组件。开发时，将图片地址作为值，放入input即可。
       3.3 $('input.datetimepicker')                    元素会被处理成特殊的［时间选择］组件，开发时，按格式Y-m-d H:i:s存入对应值即可。
       3.4 $('input.input_which_is_address')            元素会被处理成特殊的［地图］组件，开发时，建议同时放置input[name="lng"]和input[name="lat"]，然后分别放置对应的地址和gps即可。
       3.5 $('input[type=checkbox].bootstrap-switch')   元素会被处理成特殊的［开关］组件，开发时，建议value设为1，然后根据实际值，赋予是否checked的初始值。
       3.6 $('script[type="text/plain"]')               元素会被处理成特殊的［富文本编辑器］，开发时，只管将对应的值放入到<script></script>之间即可。
       3.7 $('input[name=captcha_key]')                 元素会被处理成特殊的［图片验证码］区域，支持点击刷新等操作，开发时，需要在form里放置$('input[name=captcha_code]')元素，用于给用户输入验证码。至于校验，请在接受到数据后调用对应的接口。
    4.我们已经封装里一些非常有用的快捷方法，便于大家迅速生成对应的html代码或取值。
       4.1  Utility::strOfSelect($name,$options,$value=null)                                                           根据提供的键值对，创建options字符串，并选中对应的值。
       4.2  Utility::strOfChosenWithTags($name,$selectValues=null,$options=null,$isMultiple=false,$placeholder=null)   根据提供的键值对，创建标签类型的options字符串。用户可以手动增加标签。
       4.3  Utility::strOfChosenWithAjax($name,$ajaxUrl,$valuePath,$namePath=null,$selectValues=null,$selectNames=null,$options=null,$isMultiple=false,$placeholder=null) 根据提供的键值对，创建ajax类型的options字符串。用户可以搜索关键字后增加数据。
       4.4  Utility::strOfRadio($name,$options,$value=null)                                                            根据提供的键值对，创建radio组，并选中对应的值。
       4.5  Utility::getThWithOrder($name,$order_key,$params=null)                                                     创建带sort功能的th字符串。
       4.7  Utility::getCurrentUserID()                                                                                获得当前登录用户ID
       4.8  Utility::getCurrentUserName()                                                                              可以从当前登录用户的真实姓名／昵称／手机号等里根据优先级挑一个非空的字段出来。
       4.9  Utility::getCurrentUserResult()                                                                            获得当前用户的信息对象，是HaoResult类型哦
       4.10 HaoConnect::find($path,$urlParam,  $params = array(),$method = METHOD_GET)                                 这是一个神奇的方法，可以直接请求接口并从结果中提取对应的值，准确用于输出当前页接口未提供的数据，如评论中知道userID不知道userName，就可以当场去查一个user/detail并find('name')出来。
    5.一些特殊的tips。
       5.1  页面样式框架使用的是标准的v3版本的bootstrap，可参考http://v3.bootcss.com/进行更多自定义开发。
       5.1  $('input.input_search_select')元素的显示效果会与前一个select相连接。
       5.2  分页按钮总是保持在7个（如果有7页以上数据的话），这样下一页按钮的位置就可以保持固定位置。
       5.3  面包屑导航按钮的当前页是可以点击的哦。
       5.4  在新增的页面里，也可以指定默认值哦，诀窍就是链接里指定参数，如管理员列表新增管理员，就是/edit/user_detail?level=9，详见：HaoResult::instanceModel($_REQUEST)
       5.5  在列表页里，通过edit弹框更改数据后，对应的行的数据也会刷新哦。而且是无视当前页筛选，比如在列表页删除一行数据，删除成功该数据仍会在页面上保留，只有刷新页面才会消失。详见js方法：HaoAdmin.update()
       5.6  图片上传的过程中，所在的from表单里的sumit按钮会被禁用。
       5.7  页面间跳转默认使用pjax进行无刷新跳转，还会有进度条出现哦，而且还支持访问历史的前进和后退。
       5.8  背景图在缩放浏览器后仍然能获得很棒的视觉体验哦，而且背景图会变的呢。
       5.9  退出登录其实就是打开登录窗口，因为登录窗口会自动抹掉用户的登录记录。
       5.10 登录成功后页面会刷新并重新载入。
 */
?>
<form class="form-horizontal clearfix" action="/ajax_haoconnect.php" method="post">
    <input name="url_param" type="hidden" class="form-control" placeholder="ID" value="<?= $urlParam; ?>">
    <input name="id" type="hidden" class="form-control" placeholder="ID" value="<?= $requestResult->find('id'); ?>">

    <div class="form-group">
        <label class="col-sm-2">名称</label>
        <div class="col-sm-10" required>
            <input name="name" type="string" class="form-control" placeholder="名称" value="<?= $requestResult->find('name'); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2">医院等级</label>
        <div class="col-sm-10" required>
            <input name="hospital_level" type="string" class="form-control" placeholder="医院等级" value="<?= $requestResult->find('hospitalLevel'); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2">省份</label>
        <div class="col-sm-10" required>
            <?= Utility::strOfChosenWithTags('provincial',$requestResult->find('provincial'),HaoConnect::search('.*?>provincial','hospital/get_all_provincials')) ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2">联系方式</label>
        <div class="col-sm-10">
            <input name="contact" type="string" class="form-control" placeholder="联系方式" value="<?= $requestResult->find('contact'); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2">图片</label>
        <div class="col-sm-10">
            <input name="photo" type="hidden" class="form-control input_which_upload_to_qiniu" placeholder="图片" value="<?= $requestResult->find('photo'); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2">描述</label>
        <div class="col-sm-10">
            <script name="description" type="text/plain" ><?= $requestResult->find('description'); ?></script>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2">地址</label>
        <div class="col-sm-10" required>
            <input name="address" type="hidden" class="form-control input_which_is_address" placeholder="地址" value="<?= $requestResult->find('address'); ?>">
            <input name="lng" type="hidden" class="form-control" placeholder="地址" value="<?= $requestResult->find('lng'); ?>">
            <input name="lat" type="hidden" class="form-control" placeholder="地址" value="<?= $requestResult->find('lat'); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2">状态</label>
        <div class="col-sm-10">
            <?= Utility::strOfRadio('status',['删除','正常','草稿','待审'],$requestResult->find('status','1'))?>
        </div>
    </div>
    <button type="submit" class="btn btn-default pull-right">更新</button>
</form>

```
