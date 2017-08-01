<?php

namespace Funaffect;

class Html extends \Fuel\Core\Html
{
    /**
     * hsc 短縮表記用
     *
     * @access protected
     * @return void
     */
    public static function hsc($text, $charset='UTF-8')
    {
        return htmlspecialchars($text, ENT_QUOTES, $charset);
    }

    /**
     * wfymd (Weekday from ymd) YYYY-MM-DD形式の時間表記から曜日を返す
     *
     * @access protected
     * @return void
     */
    public static function wfymd($text)
    {
        return date('w', strtotime($text.' 00:00:00'));
    }
}
