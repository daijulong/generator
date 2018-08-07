<?php

use Daijulong\Console\Console;

return [
    Console::instance()->colored('Command', 'green') .
    ' : ' .
    Console::instance()->colored('publish', 'yellow'),

    'Usage' => 'publish [options]',
    'Options' => [
        '-o, --only' => '仅发布指定项目，支持：bin|脚本，config|配置，stub|模板',
    ],
];
