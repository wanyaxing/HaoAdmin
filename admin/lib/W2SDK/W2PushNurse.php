<?php
/**
 * 推送 病人版
 * @package W2
 * @author axing
 * @since 1.0
 * @version 1.0
 */

class W2PushNurse extends W2PushPatient{
    public static $API_KEY_IOS        = null;
    public static $SECRET_KEY_IOS     = null;
    public static $API_KEY_ANDROID    = null;
    public static $SECRET_KEY_ANDROID = null;
}

W2PushNurse::$API_KEY_IOS        = W2PUSHNURSE_API_KEY_IOS;
W2PushNurse::$SECRET_KEY_IOS     = W2PUSHNURSE_SECRET_KEY_IOS;
W2PushNurse::$API_KEY_ANDROID    = W2PUSHNURSE_API_KEY_ANDROID;
W2PushNurse::$SECRET_KEY_ANDROID = W2PUSHNURSE_SECRET_KEY_ANDROID;
