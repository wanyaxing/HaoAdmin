<?php
/**
 * 推送 病人版
 * @package W2
 * @author axing
 * @since 1.0
 * @version 1.0
 */

class W2PushPatient extends W2PUSH{
    public static $API_KEY_IOS        = null;
    public static $SECRET_KEY_IOS     = null;
    public static $API_KEY_ANDROID    = null;
    public static $SECRET_KEY_ANDROID = null;
}

W2PushPatient::$API_KEY_IOS        = W2PUSHPATIENT_API_KEY_IOS;
W2PushPatient::$SECRET_KEY_IOS     = W2PUSHPATIENT_SECRET_KEY_IOS;
W2PushPatient::$API_KEY_ANDROID    = W2PUSHPATIENT_API_KEY_ANDROID;
W2PushPatient::$SECRET_KEY_ANDROID = W2PUSHPATIENT_SECRET_KEY_ANDROID;
