<?php

class Utils {

    const ANDROID = 1;
    const IOS = 2;
    const OSX = 3;
    const GEAR_VR = 5;
    const HOLOLENS = 6;
    const WINDOWS = 7;
    const PLAYSTATION = 11;
    const XBOX = 13;

    public static function randomString() : string{
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $str = '';
        for ($i = 0; $i < 5; $i++) {
            $str .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $str;
    }

}
