<?php
class Html extends Fuel\Core\Html
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
}
