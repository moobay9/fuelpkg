<?php
class Controller_Template extends Fuel\Core\Controller_Template
{
    public function after($response)
    {
        $response = parent::after($response);

        // SSL通信 または クッキーのセキュアフラグが TRUE の場合
        if (Input::server('SERVER_PORT') === '443' OR Config::get('cookie.secure'))
        {
            $response->set_header('Strict-Transport-Security', 'max-age=3600');
        }

        // iframe 制御
        $response->set_header('X-Frame-Options', 'SAMEORIGIN');

        // XSS 対策
        $response->set_header('X-Content-Type-Options', 'nosniff');


        return $response;
    }
}
