<?php

use Daijulong\Console\Console;

return [
    Console::instance()->colored('Command', 'green') .
    ' : ' .
    Console::instance()->colored('config', 'yellow'),

    'Usage' => 'config node [options]',
    'Options' => [
        '-h, --help' => '显示帮助信息',
    ],
    'Arguments' => [
        'node' => '节点，如：modules ，多个节点以“.”连接，如：modules.controller ，未指定节点则默认为根节点',
    ],
];
