<?php
namespace Test\open;

use ApiResponse\BaseResponse;
use ApiResponse\InterfaceResponse;

class TestAdd extends BaseResponse implements InterfaceResponse
{
    public function run(&$params)
    {
        // TODO: Implement run() method.

        return [
            'status' => true,
            'code' => '200',
            'data' => [
                'current_time' => date('Y-m-d H:i:s')
            ]
        ];
    }
}