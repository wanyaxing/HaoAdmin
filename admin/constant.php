<?php
/**
* 常量配置
* @package conf
* @author axing
* @version 0.1
*/

/** 状态  - 不存在 */
define('STATUS_DISABLED',         0);
/** 状态  - 正常 */
define('STATUS_NORMAL',           1);
/** 状态  - 草稿、封号 */
define('STATUS_DRAFT',            2);
/** 状态  - 待审、禁言 */
define('STATUS_PENDING',          3);


/** 常量类，提供了遍历类中所有常量的方法 */
class CONST_CLASS
{
    /** 取出所有变量 */
    public static function getAllConstants()
    {
        $oClass = new ReflectionClass(get_called_class());
        $constants = $oClass->getConstants();
        return array_values($constants);
    }

    /** 取出指定变量的描述 */
    public static function getStr($pConst)
    {
        return $pConst;
    }

    /** 取出所有变量=>描述的字典 */
    public static function getAllStrsOfConstants()
    {
        $pConsts = func_get_args();
        if (count($pConsts)==0)
        {
            $pConsts = static::getAllConstants();
        }
        $list = array();
        foreach ( $pConsts as $pConst) {
            $list[$pConst] = static::getStr($pConst);
        }
        return $list;
    }
}

class DEVICE_TYPE extends CONST_CLASS
{
    const BROWSER  = 1;  //浏览器设备
    const PC       = 2;  //pc设备，服务器
    const LINUX    = 2;  //pc设备，服务器
    const ANDROID  = 3;  //安卓
    const IOS      = 4;  //iOS
    const WINDOWS  = 5;  //WP

    public static function getStr($pConst)
    {
        $valueList = array(
                 static::BROWSER            => '浏览器设备'
                ,static::PC                 => 'pc设备，服务器'
                ,static::ANDROID            => '安卓'
                ,static::IOS                => 'iOS'
                ,static::WINDOWS            => 'WP'
            );

        return isset($valueList[$pConst])?$valueList[$pConst]:'';
    }
}


class SMS_USEFOR extends CONST_CLASS
{
    const REGISTER    = 1;//注册用验证码
    const LOGIN       = 2;//登陆用验证码
    const RESTPWD     = 3;//找回密码用验证码
    const RESTTEL     = 4;//修改手机号用验证码

    public static function getStr($pConst)
    {
        $valueList = array(
                 static::REGISTER         => '注册'
                ,static::LOGIN            => '登陆'
                ,static::RESTPWD          => '找回密码'
                ,static::RESTTEL          => '修改手机号'
            );

        return isset($valueList[$pConst])?$valueList[$pConst]:'';
    }
}

class USER_LEVEL extends CONST_CLASS
{
    const PATIENT     = 1; //病人
    const NURSE       = 2; //陪诊
    const ADMIN       = 9; //管理员
}

class DEVICE_CLIENTINFO extends CONST_CLASS
{
    const PATIENT = '81angel-patient';
    const NURSE   = '81angel-nurse';
    const WEB     = '81angel-web';
    public static function getStr($pConst)
    {
        $valueList = array(
                 static::PATIENT           => '病人版'
                ,static::NURSE             => '陪诊版'
                ,static::WEB               => '网页版'
            );

        return isset($valueList[$pConst])?$valueList[$pConst]:'';
    }
}

class USER_SEX extends CONST_CLASS
{
    const UNKNOWN     = 0; //未知
    const MALE        = 1; //男
    const FEMALE      = 2; //女
}

class USER_VIP extends CONST_CLASS
{
    const NORMAL     = 0; //普通用户
    const VIP        = 1; //VIP用户
}

class USER_IDENTITY extends CONST_CLASS
{
    const IP           = 1;
    const TOKEN        = 2;
    const MAC          = 3;
}


class PUSH_TYPE extends CONST_CLASS
{
    const WEBVIEW              = 1;// webview
    const WEBBROWSER           = 2;// 外跳链接

    const SERVE_ORDER_DETAIL   = 11;
    const SERVE_ORDER_LIST     = 111;

    public static function getStr($pConst)
    {
        $valueList = array(
                 static::WEBVIEW            => 'webview'
                ,static::WEBBROWSER         => '外跳链接'
                ,static::SERVE_ORDER_DETAIL => '订单详情'
                ,static::SERVE_ORDER_LIST   => '订单列表'
            );

        return isset($valueList[$pConst])?$valueList[$pConst]:'';
    }
}


class ORDER_TYPE extends CONST_CLASS
{
    const REGISTER                          = 1  ;//挂号
    const NURSE                             = 2  ;//陪诊
    const REGISTER_AND_NURSE                = 3  ;//挂号+陪诊

    public static function getStr($pConst)
    {
        $valueList = array(
                 static::REGISTER           => '挂号'
                ,static::NURSE              => '陪诊'
                ,static::REGISTER_AND_NURSE => '挂号+陪诊'
            );

        return isset($valueList[$pConst])?$valueList[$pConst]:'';
    }

    /** 不同陪诊类型，价格不同 */
    public static function getPrice($pConst)
    {
        $valueList = array(
                 static::REGISTER                    => 0.01
                ,static::NURSE                       => 0.01
                ,static::REGISTER_AND_NURSE          => 0.01
            );

        return isset($valueList[$pConst])?$valueList[$pConst]:9999;
    }
}

class PAY_TYPE extends CONST_CLASS
{
    const ALI                            = 1  ;//支付宝
    const WX                             = 2  ;//微信
    const VIP                            = 3  ;//VIP券
    public static function getStr($pConst)
    {
        $valueList = array(
                 static::ALI             => '支付宝'
                ,static::WX              => '微信'
                ,static::VIP             => 'VIP券'
            );

        return isset($valueList[$pConst])?$valueList[$pConst]:'';
    }
}

class PAY_STATUS extends CONST_CLASS
{
    const NONE                    = 0    ;//
    const PAYING                  = 11   ;//付款中
    const PAYED                   = 21   ;//付款完成
    const REFUND_DOING            = 131  ;//退款中
    const REFUND_COMPLETE         = 141  ;//退款完成
}

class ORDER_STATUS extends CONST_CLASS
{
    const NEWORDER                      = 1  ;//新下单
    const UNPAY_WAIT_PAY                = 3  ;//重新选择付款方式
    const PAYING_ALI                    = 11 ;//支付宝付费中
    const PAYING_WX                     = 12 ;//微信付费中
    const PAYED                         = 21 ;//已付费
    const NURSED                        = 26 ;//已安排陪诊员
    const PAPERGOT                      = 31 ;//已取资料
    const SERVE_START                   = 41 ;//开始服务
    const SERVE_TIMEOUT                 = 44 ;//服务超时（系统自动处理）
    const SERVE_END                     = 46 ;//陪诊结束（陪诊员提交）
    const SERVE_COMPLETE                = 51 ;//服务结束（用户确认）
    const REVIEWED                      = 61 ;//已评价
    const CANCELED                      = 101;//已取消订单
    const TIMEOUT                       = 106;//超时自动取消
    const REFUND_ASKED                  = 111;//申请退款中
    const REFUND_ALLOWED                = 121;//同意退款
    const REFUND_DOING                  = 131;//已提交退款操作
    const REFUND_COMPLETE               = 141;//退款完成


    public static function getStr($pConst)
    {
        $valueList = array(
                 static::NEWORDER             => '新下单'
                ,static::UNPAY_WAIT_PAY       => '重新选择付款方式中'
                ,static::PAYING_ALI           => '支付宝付费中'
                ,static::PAYING_WX            => '微信付费中'
                ,static::PAYED                => '正在安排陪诊员'
                ,static::NURSED               => '已安排陪诊员'
                ,static::PAPERGOT             => '挂号中'
                ,static::SERVE_START          => '开始服务'
                ,static::SERVE_TIMEOUT        => '服务超时'
                ,static::SERVE_END            => '结束服务'
                ,static::SERVE_COMPLETE       => '待评价'
                ,static::REVIEWED             => '已评价'
                ,static::CANCELED             => '已取消订单'
                ,static::TIMEOUT              => '超时自动取消'
                ,static::REFUND_ASKED         => '申请退款中'
                ,static::REFUND_ALLOWED       => '退款流程中'
                ,static::REFUND_DOING         => '已提交退款操作'
                ,static::REFUND_COMPLETE      => '退款完成'
            );

        return isset($valueList[$pConst])?$valueList[$pConst]:'';
    }

    public static function getBtnOfAction($pConst)
    {
        $valueList = array(
                 static::NEWORDER             => '下单'
                ,static::UNPAY_WAIT_PAY       => '重新选择付款方式'
                ,static::PAYING_ALI           => '使用支付宝支付'
                ,static::PAYING_WX            => '使用微信支付'
                ,static::PAYED                => '付款成功'
                ,static::NURSED               => '安排陪诊员'
                ,static::PAPERGOT             => '挂号开始'
                ,static::SERVE_START          => '陪诊开始'
                ,static::SERVE_TIMEOUT        => '服务超时'
                ,static::SERVE_END            => '服务结束'
                ,static::SERVE_COMPLETE       => '确认结束'
                ,static::REVIEWED             => '评价'
                ,static::CANCELED             => '取消订单'
                ,static::TIMEOUT              => '超时自动取消'
                ,static::REFUND_ASKED         => '申请退款'
                ,static::REFUND_ALLOWED       => '同意退款'
                ,static::REFUND_DOING         => '批量执行退款'
                ,static::REFUND_COMPLETE      => '退款完成'
            );

        return isset($valueList[$pConst])?$valueList[$pConst]:'';
    }

    public static function getDes($pConst)
    {
        $valueList = array(
                 static::NEWORDER             => '2小时内未付款取消订单。'
                ,static::UNPAY_WAIT_PAY       => '2小时内未付款取消订单。'
                ,static::PAYING_ALI           => '请前往支付宝付款，2小时内未付款取消订单。'
                ,static::PAYING_WX            => '请前往微信付款，2小时内未付款取消订单。'
                ,static::PAYED                => '您的付款我们已收到，我们会安排专业人员为您服务。'
                ,static::NURSED               => '已安排好陪诊人员，请保持联系畅通。'
                ,static::PAPERGOT             => '正在为您安排后续挂号服务。'
                ,static::SERVE_START          => '陪诊员已开始服务。服务结束后，请确认您已收到您给陪诊员的所有证件，并确认服务结束。'
                ,static::SERVE_TIMEOUT        => '已完成指定事件的服务，请确认您已收到您给陪诊员的所有证件，并确认服务结束。'
                ,static::SERVE_END            => '陪诊员已开始服务。服务结束后，请确认您已收到您给陪诊员的所有证件，并确认服务结束。'
                ,static::SERVE_COMPLETE       => '陪诊结束，请对陪诊员的表现给予评价。'
                ,static::REVIEWED             => '您的评价我们收到啦，非常感谢您的支持。'
                ,static::CANCELED             => '您已取消了这个订单，真是非常遗憾未能为您提供服务。'
                ,static::TIMEOUT              => '该订单已超时，系统已自动取消该订单'
                ,static::REFUND_ASKED         => '我们已收到您的退款请求，我们需要一些时间来处理这个请求，请耐心等待。'
                ,static::REFUND_ALLOWED       => '我们已通过了您的退款请求，执行退款需要一些时间，请耐心等待。'
                ,static::REFUND_DOING         => '退款已在执行中，请留意您付款时的相关账户。'
                ,static::REFUND_COMPLETE      => '退款已退到您的账户，订单已结束，非常遗憾未能为您提供服务。'
            );

        return isset($valueList[$pConst])?$valueList[$pConst]:'';
    }

    public static function getColor($pConst)
    {
        $valueList = array(
                 static::NEWORDER             => array('r'=>'228','g'=>'53','b'=>'58','hex'=>'#e4353a')
                ,static::UNPAY_WAIT_PAY       => array('r'=>'228','g'=>'53','b'=>'58','hex'=>'#e4353a')
                ,static::PAYING_ALI           => array('r'=>'228','g'=>'53','b'=>'58','hex'=>'#e4353a')
                ,static::PAYING_WX            => array('r'=>'228','g'=>'53','b'=>'58','hex'=>'#e4353a')
                ,static::PAYED                => array('r'=>'163','g'=>'0','b'=>'125','hex'=>'#a3007d')
                ,static::NURSED               => array('r'=>'163','g'=>'0','b'=>'125','hex'=>'#a3007d')
                ,static::PAPERGOT             => array('r'=>'163','g'=>'0','b'=>'125','hex'=>'#a3007d')
                ,static::SERVE_START          => array('r'=>'163','g'=>'0','b'=>'125','hex'=>'#a3007d')
                ,static::SERVE_TIMEOUT        => array('r'=>'163','g'=>'0','b'=>'125','hex'=>'#a3007d')
                ,static::SERVE_END            => array('r'=>'163','g'=>'0','b'=>'125','hex'=>'#a3007d')
                ,static::SERVE_COMPLETE       => array('r'=>'236','g'=>'136','b'=>'14','hex'=>'#ec880e')
                ,static::REVIEWED             => array('r'=>'236','g'=>'136','b'=>'14','hex'=>'#ec880e')
                ,static::CANCELED             => array('r'=>'236','g'=>'136','b'=>'14','hex'=>'#ec880e')
                ,static::TIMEOUT              => array('r'=>'236','g'=>'136','b'=>'14','hex'=>'#ec880e')
                ,static::REFUND_ASKED         => array('r'=>'236','g'=>'136','b'=>'14','hex'=>'#ec880e')
                ,static::REFUND_ALLOWED       => array('r'=>'236','g'=>'136','b'=>'14','hex'=>'#ec880e')
                ,static::REFUND_DOING         => array('r'=>'236','g'=>'136','b'=>'14','hex'=>'#ec880e')
                ,static::REFUND_COMPLETE      => array('r'=>'236','g'=>'136','b'=>'14','hex'=>'#ec880e')
            );

        return isset($valueList[$pConst])?$valueList[$pConst]:array('r'=>'228','g'=>'53','b'=>'58','hex'=>'E4353A');
    }

    /**
     * 获得目标状态的下一步操作
     * @param  int $pUserID
     * @param  ServeOrderModel $pTargetModel
     * @return array
     */
    public static function getNextActions($pUserID,$pTargetModel)
    {
        $pOrderStatus = $pTargetModel->getOrderStatus();
        $actionsNext = array();
        $userModel = UserHandler::loadModelById($pUserID);
        if (is_object($userModel))
        {
            switch ($userModel->getLevel()) {
                case USER_LEVEL::PATIENT:
                    switch ($pOrderStatus) {
                        case ORDER_STATUS::NEWORDER:
                            $actionsNext = array(ORDER_STATUS::PAYING_ALI,ORDER_STATUS::PAYING_WX,ORDER_STATUS::CANCELED);
                            break;
                        case ORDER_STATUS::UNPAY_WAIT_PAY:
                            $actionsNext = array(ORDER_STATUS::PAYING_ALI,ORDER_STATUS::PAYING_WX,ORDER_STATUS::CANCELED);
                            break;
                        case ORDER_STATUS::PAYING_ALI:
                            $actionsNext = array(ORDER_STATUS::UNPAY_WAIT_PAY);
                            break;
                        case ORDER_STATUS::PAYING_WX:
                            $actionsNext = array(ORDER_STATUS::UNPAY_WAIT_PAY);
                            break;
                        case ORDER_STATUS::PAYED:
                            if ($pTargetModel->getIsThisCanBeRefundAskLocal())
                            {
                                $actionsNext = array(ORDER_STATUS::REFUND_ASKED);
                            }
                            break;
                        case ORDER_STATUS::NURSED:
                            if ($pTargetModel->getIsThisCanBeRefundAskLocal())
                            {
                                $actionsNext = array(ORDER_STATUS::REFUND_ASKED);
                            }
                            break;
                        case ORDER_STATUS::PAPERGOT:
                            if ($pTargetModel->getIsThisCanBeRefundAskLocal())
                            {
                                $actionsNext = array(ORDER_STATUS::REFUND_ASKED);
                            }
                            break;
                        case ORDER_STATUS::SERVE_START:
                            $actionsNext = array(ORDER_STATUS::SERVE_COMPLETE);
                            break;
                        case ORDER_STATUS::SERVE_TIMEOUT:
                            $actionsNext = array(ORDER_STATUS::SERVE_COMPLETE);
                            break;
                        case ORDER_STATUS::SERVE_END:
                            $actionsNext = array(ORDER_STATUS::SERVE_COMPLETE);
                            break;
                        case ORDER_STATUS::SERVE_COMPLETE:
                            $actionsNext = array(ORDER_STATUS::REVIEWED);
                            break;
                        case ORDER_STATUS::REVIEWED:
                            $actionsNext = array();
                            break;

                    }
                    break;
                case USER_LEVEL::NURSE:
                    switch ($pOrderStatus) {
                        case ORDER_STATUS::NURSED:
                            if ($pTargetModel->getOrderType() == ORDER_TYPE::NURSE)
                            {//陪诊不需要取资料哦。
                                $actionsNext = array(ORDER_STATUS::SERVE_START);
                            }
                            else
                            {//挂号需要取资料
                                $actionsNext = array(ORDER_STATUS::PAPERGOT);
                            }
                            break;
                        case ORDER_STATUS::PAPERGOT:
                            if ($pTargetModel->getOrderType() == ORDER_TYPE::REGISTER)
                            {//挂号不需要陪诊，直接结束服务
                                $actionsNext = array(ORDER_STATUS::SERVE_END);
                            }
                            else
                            {//陪诊可以开始服务
                                $actionsNext = array(ORDER_STATUS::SERVE_START);
                            }
                            break;
                        case ORDER_STATUS::SERVE_TIMEOUT:
                            $actionsNext = array(ORDER_STATUS::SERVE_END);
                            break;
                        case ORDER_STATUS::SERVE_START:
                            $actionsNext = array(ORDER_STATUS::SERVE_END);
                            break;

                    }
                    break;

                case USER_LEVEL::ADMIN:
                    switch ($pOrderStatus) {
                        case ORDER_STATUS::PAYED:
                            $actionsNext = array(ORDER_STATUS::NURSED);
                            break;
                        case ORDER_STATUS::REFUND_ASKED:
                            //同意退款，然后执行退款需要单独调用批量执行退款的接口哦。
                            $actionsNext = array(ORDER_STATUS::REFUND_ALLOWED);
                            break;
                        // 不支持直接退款，需要调用独立的批量退款接口。
                        // case ORDER_STATUS::REFUND_ALLOWED:
                        //     $actionsNext = array(ORDER_STATUS::REFUND_DOING);
                        //     break;

                    }
                    break;

            }
        }
        return $actionsNext;
    }
}


class FILTER_MARK extends CONST_CLASS
{
    const ALL                     = 1    ;  // 所有用户
    const ANDROID                 = 2    ;  // Android设备
    const IOS                     = 4    ;  // ios设备
    const PATIENT                 = 8    ;  // 病人
    const NURSE                   = 16   ;  // 陪诊员
    const VIP                     = 32   ;  // VIP


    public static function getStr($pConst)
    {
        $valueList = array(
                 static::ALL                       => '所有用户'
                ,static::ANDROID                   => 'Android设备'
                ,static::IOS                       => 'ios设备'
                ,static::PATIENT                   => '病人'
                ,static::NURSE                     => '陪诊员'
                ,static::VIP                       => 'VIP'
            );

        return isset($valueList[$pConst])?$valueList[$pConst]:'';
    }

    /** 获得指定用户的标识 */
    public static function getMarkOfUserModel($pTargetModel)
    {
        $filterMark = 0;

        if (is_object($pTargetModel))
        {
            switch ($pTargetModel->getLevel()) {
                case USER_LEVEL::PATIENT:
                    $filterMark += FILTER_MARK::PATIENT;
                    break;
                case USER_LEVEL::NURSE:
                    $filterMark += FILTER_MARK::NURSE;
                    break;
            }
            switch ($pTargetModel->getVip()) {
                case 1:
                    $filterMark += FILTER_MARK::VIP;
                    break;
            }
        }

        return $filterMark;
    }

    /** 获得当前设备的标识 */
    public static function getMarkOfThisDevice()
    {
        $filterMark = 0;

        $filterMarkThis += FILTER_MARK::ALL;

        switch (Utility::getHeaderValue('Devicetype')) {
            case DEVICE_TYPE::ANDROID:
                $filterMarkThis += FILTER_MARK::ANDROID;
                break;
            case DEVICE_TYPE::IOS:
                $filterMarkThis += FILTER_MARK::IOS;
                break;
        }

        $filterMark += FILTER_MARK::getMarkOfUserModel(Utility::getCurrentUserModel());

        return $filterMark;
    }

    /** 将标识分拆成数组 */
    public static function getMarksOfMarkTotal($filterMark)
    {
        return W2Math::getBitsOfNumber($filterMark);
    }
}


class DEVICE_PUSH_TYPE extends CONST_CLASS
{
    const USER_SINGLE     = 1;   //所有人
    const ALL             = 2;   //所有人
    const ANDROID_ALL     = 3;   //所有安卓
    const ANDROID_PATIENT = 311; //所有安卓病人
    const ANDROID_NURSE   = 321; //所有安卓陪诊
    const IOS_ALL         = 4;   //所有iOS
    const IOS_PATIENT     = 411; //所有IOS病人
    const IOS_NURSE       = 421; //所有IOS陪诊


    public static function getStr($pConst)
    {
        $valueList = array(
                 static::USER_SINGLE               => '指定用户'
                ,static::ALL                       => '所有人'
                ,static::ANDROID_ALL               => '所有安卓'
                ,static::ANDROID_PATIENT           => '所有安卓病人'
                ,static::ANDROID_NURSE             => '所有安卓陪诊'
                ,static::IOS_ALL                   => '所有iOS'
                ,static::IOS_PATIENT               => '所有IOS病人'
                ,static::IOS_NURSE                 => '所有IOS陪诊'
            );

        return isset($valueList[$pConst])?$valueList[$pConst]:'';
    }
}

class VERISON_ACTION_TYPE extends CONST_CLASS
{
    const MUST_UPGRADE     = 101;   //强制升级
    const ASK_TIMELY       = 102;   //每次询问
    const CANBE_IGNORE     = 103;   //可以忽略

    public static function getStr($pConst)
    {
        $valueList = array(
                 static::MUST_UPGRADE              => '强制升级'
                ,static::ASK_TIMELY                => '每次询问'
                ,static::CANBE_IGNORE              => '可以忽略'
            );

        return isset($valueList[$pConst])?$valueList[$pConst]:'';
    }
}
