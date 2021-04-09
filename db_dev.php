<?php
return [
    "mysql" => [
        'hostname' => ['47.111.96.27'], //是数组是表示读写分离，第一个为主库，后面的都为从库
        'username' => 'root', //读写分离式 不同username 用数组设置，要与hostname对应，
        'password' => 9901,
        'database' => 'qisu_run_log',
        'charset' => 'utf8',
        'port' => 3306,
        'poolConf' =>[
            'intervalCheckTime' => 30*1000,
            'maxIdleTime' => 15,
            'maxObjectNum' => 20,
            'minObjectNum' => 5,
            'getObjectTimeout' =>3.0,
            'autoPing' => 5,
            'extraConf' =>[]
        ],
    ]
];
