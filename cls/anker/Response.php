<?php
namespace app\cls\anker;

class Response {
    public $status = 'success';
    public $code = 0;
    public $msg = '';

    public function __construct($code, $msg)
    {
        $this->code = $code;
        $this->msg = $msg;
        $this->status = $code==0 ? $this->status : 'error';
    }

    public static function output($code, $msg)
    {
        $obj = new Response($code, $msg);
        $list = json_encode($obj, JSON_UNESCAPED_UNICODE);
        echo $list;
    }
}