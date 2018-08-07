<?php

use Daijulong\Console\Console;

return [
    Console::instance()->colored('Command', 'green') .
    ' : ' .
    Console::instance()->colored('make', 'yellow'),

    'Usage' => 'make name [options]',
    'Options' => [
        '-g, --group' => '指定组，默认“default”',
        '-h, --help' => '显示帮助信息',
        '-o, --only' => '仅生成（组中）指定的模块，多个以“,”分隔',
    ],
    'Arguments' => [
        'name' => '名称，如：User ，可以带路径，如：Admin/User ，路径部分建议使用首字母大写的驼峰命名法，用以遵循 psr-4 构建类的命名空间',
    ],
];
