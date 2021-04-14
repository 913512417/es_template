<?php
namespace App\HttpController;
use App\Utility\Common\ReturnCode;

class Index extends BaseController
{

    public function index()
    {

    }

    function test()
    {
        $this->returnJson(ReturnCode::SUCESS);
    }


}