<?php

use Daijulong\Console\Console;

return [
    Console::instance()->colored('Command', 'green') .
    ' : ' .
    Console::instance()->colored('manual', 'yellow'),

    'Usage' => 'manual name [options]',
    'Options' => [
        '-h, --help' => '显示帮助信息',
    ],
    'Arguments' => [
        'name' => '命令名称，如：make',
    ],
];
