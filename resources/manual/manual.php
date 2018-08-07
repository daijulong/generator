<?php

use Daijulong\Console\Console;

return [
    '    _________' . PHP_EOL .
    '   /  ______/                                   ___' . PHP_EOL .
    '  /  / ___ _____  ______  _____  __________  __/  /_____  ______' . PHP_EOL .
    ' /  / /_  /  _  \/  __  \/  _  \/  ___/ __ \/_   __/ __ \/  ___/' . PHP_EOL .
    '/  /___/ /   ___/  / /  /   ___/  /  / /_/ |_/  / / /_/ /  /' . PHP_EOL .
    '\_______/\_____/__/ /__/\_____/__/   \___/__/__/  \____/__/',

    Console::instance()->colored('Generator', 'green') .
    ' version ' .
    Console::instance()->colored(GENERATOR_VERSION, 'yellow') .
    ' 2018-08-02',

    'Usage' => 'command [options] [arguments]',
    'Options' => [
        '-h, --help' => '显示帮助信息',
    ],
    'Available commands' => [
        'config' => '查看配置',
        'make' => '生成文件',
        'manual' => '显示使用手册',
        'publish' => '发布资源',
    ],
];
